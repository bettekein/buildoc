<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrganizationChartExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithCustomStartCell, WithEvents
{
    protected $project;
    protected $tenant;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->tenant = Tenant::find($project->tenant_id);
    }

    public function collection()
    {
        return collect([]);
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return '下請負業者編成表';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // 余白
            'B' => 30, // 元請
            'C' => 30, // 一次
            'D' => 30, // 二次
            'E' => 30, // 三次
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['name' => 'ＭＳ Ｐゴシック', 'size' => 11]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $project = $this->project;
                $tenant = $this->tenant;

                // タイトル
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', '下　請　負　業　者　編　成　表');
                $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 工事名など
                $sheet->setCellValue('B3', '工事名称: ' . $project->name);
                $sheet->setCellValue('E3', '作成日: ' . date('Y年m月d日'));

                // ヘッダー行
                $row = 5;
                $sheet->setCellValue('B' . $row, '元請業者');
                $sheet->setCellValue('C' . $row, '一次下請業者');
                $sheet->setCellValue('D' . $row, '二次下請業者');
                $sheet->setCellValue('E' . $row, '三次下請業者');

                $sheet->getStyle('B' . $row . ':E' . $row)->getFont()->setBold(true);
                $sheet->getStyle('B' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $row . ':E' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');

                $row++;

                // データ構築
                // 階層データの取得
                $tier1s = $project->subcontractors()->wherePivot('tier', 1)->get();
                // 簡易的に全階層を取得してメモリ展開
                $allSubs = $project->subcontractors()->get();

                // 元請（自社）のボックス
                $startRow = $row;
                $sheet->setCellValue('B' . $row, $tenant->company_name);
                $sheet->setCellValue('B' . ($row + 1), "安: (未設定)"); // 安全衛生責任者
                $sheet->getStyle('B' . $row . ':B' . ($row + 1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // 一次下請けのループ
                $currentRow = $row;

                foreach ($tier1s as $t1) {
                    $t1Row = $currentRow; // この一次下請けの開始行
    
                    // 一次下請け書き込み
                    $this->writeBox($sheet, 'C', $t1Row, $t1);

                    // 直下の二次下請けを探す
                    $tier2s = $allSubs->filter(function ($item) use ($t1) {
                        return $item->pivot->tier == 2 && $item->pivot->parent_subcontractor_id == $t1->id;
                    });

                    if ($tier2s->isEmpty()) {
                        $currentRow += 3; // 次の一次下請け用に間隔を空ける
                    } else {
                        foreach ($tier2s as $t2) {
                            $t2Row = $currentRow;
                            // 二次下請け書き込み
                            $this->writeBox($sheet, 'D', $t2Row, $t2);

                            // 直下の三次下請けを探す
                            $tier3s = $allSubs->filter(function ($item) use ($t2) {
                                return $item->pivot->tier == 3 && $item->pivot->parent_subcontractor_id == $t2->id;
                            });

                            if ($tier3s->isEmpty()) {
                                $currentRow += 3;
                            } else {
                                foreach ($tier3s as $t3) {
                                    $t3Row = $currentRow;
                                    // 三次下請け書き込み
                                    $this->writeBox($sheet, 'E', $t3Row, $t3);
                                    $currentRow += 3;
                                }
                            }
                        }
                    }

                    // ツリー線（簡易）を描画するロジックは省略し、ボックス配置のみとする
                    // 本来は罫線でつなぐ必要がある
                }

                // 元請ボックスの高さを調整（全体をカバーするように）もし必要なら
                // $sheet->mergeCells('B' . $startRow . ':B' . ($currentRow - 1));
                // $sheet->getStyle('B' . $startRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    
            },
        ];
    }

    private function writeBox($sheet, $col, $row, $sub)
    {
        $sheet->setCellValue($col . $row, $sub->name);
        $sheet->setCellValue($col . ($row + 1), "工: " . ($sub->pivot->work_content ?? ''));
        $sheet->setCellValue($col . ($row + 2), "安: " . ($sub->pivot->safety_manager ?? ''));

        $sheet->getStyle($col . $row . ':' . $col . ($row + 2))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($col . $row)->getFont()->setBold(true);
    }
}
