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

class ConstructionLedgerExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithCustomStartCell, WithEvents
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
        // このエクスポートでは collection() は主要なデータソースとして使わず、
        // registerEvents でセルに直接書き込む方式を採用します（複雑な帳票レイアウトのため）
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
        return '施工体制台帳';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 2,  // 余白
            'B' => 15, // ラベル
            'C' => 20, // 内容
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // デフォルトフォント
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
                $customer = $project->customer;

                // タイトル
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', '施　工　体　制　台　帳');
                $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 作成日
                $sheet->setCellValue('H2', '作成日: ' . date('Y年m月d日'));

                // ■ 工事の概要
                $sheet->setCellValue('B4', '1. 工事の名称');
                $sheet->mergeCells('C4:J4');
                $sheet->setCellValue('C4', $project->name);

                $sheet->setCellValue('B5', '2. 発注者住所');
                $sheet->mergeCells('C5:J5');
                $sheet->setCellValue('C5', optional($customer)->address ?? '');

                $sheet->setCellValue('B6', '   発注者氏名');
                $sheet->mergeCells('C6:J6');
                $sheet->setCellValue('C6', optional($customer)->name ?? '');

                $sheet->setCellValue('B7', '3. 工事現場住所');
                $sheet->mergeCells('C7:J7');
                $sheet->setCellValue('C7', $project->site_address ?? '');

                $sheet->setCellValue('B8', '4. 工期');
                $sheet->mergeCells('C8:J8');
                $period = ($project->period_start ? $project->period_start->format('Y年m月d日') : '') . ' ～ ' . ($project->period_end ? $project->period_end->format('Y年m月d日') : '');
                $sheet->setCellValue('C8', $period);

                $sheet->setCellValue('B9', '5. 契約日');
                $sheet->mergeCells('C9:J9');
                $sheet->setCellValue('C9', $project->contract_date ? $project->contract_date->format('Y年m月d日') : '');


                // ■ 元請業者（自社）の情報
                $row = 11;
                $sheet->setCellValue('A' . $row, '【作成建設業者（元請）】');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                $sheet->setCellValue('B12', '商号または名称');
                $sheet->mergeCells('C12:F12');
                $sheet->setCellValue('C12', $tenant->company_name);

                $sheet->setCellValue('G12', '代表者氏名');
                $sheet->mergeCells('H12:J12');
                $sheet->setCellValue('H12', $tenant->representative_name ?? '');
                $row++;

                $sheet->setCellValue('B' . $row, '住所');
                $sheet->mergeCells('C' . $row . ':J' . $row);
                $sheet->setCellValue('C' . $row, "〒{$tenant->zip_code} {$tenant->address}");
                $row++;

                $sheet->setCellValue('B' . $row, '電話番号');
                $sheet->mergeCells('C' . $row . ':F' . $row);
                $sheet->setCellValue('C' . $row, $tenant->phone);

                $sheet->setCellValue('G' . $row, 'FAX番号');
                $sheet->mergeCells('H' . $row . ':J' . $row);
                $sheet->setCellValue('H' . $row, $tenant->fax);
                $row++;

                // 建設業許可
                $sheet->setCellValue('B' . $row, '建設業許可');
                $sheet->mergeCells('C' . $row . ':J' . $row);
                $sheet->setCellValue('C' . $row, $tenant->formatted_licenses);
                $row++;

                // 現場担当者情報（プロジェクトのスタッフから取得するのが理想だが、ここでは枠のみ）
                $sheet->setCellValue('B' . $row, '現場代理人');
                $sheet->mergeCells('C' . $row . ':F' . $row);
                $sheet->setCellValue('C' . $row, ''); // 未実装: プロジェクト設定から取得
    
                $sheet->setCellValue('G' . $row, '権限');
                $sheet->mergeCells('H' . $row . ':J' . $row);
                $sheet->setCellValue('H' . $row, '工事の請負契約締結権限の有無：　有　・　無');
                $row++;

                $sheet->setCellValue('B' . $row, '主任技術者');
                $sheet->mergeCells('C' . $row . ':F' . $row);
                $sheet->setCellValue('C' . $row, ''); // 未実装
    
                $sheet->setCellValue('G' . $row, '資格');
                $sheet->mergeCells('H' . $row . ':J' . $row);
                $sheet->setCellValue('H' . $row, '');
                $row++;

                $sheet->setCellValue('B' . $row, '安全衛生責任者');
                $sheet->mergeCells('C' . $row . ':F' . $row);
                $sheet->setCellValue('C' . $row, ''); // 未実装
    
                $sheet->setCellValue('G' . $row, '資格');
                $sheet->mergeCells('H' . $row . ':J' . $row);
                $sheet->setCellValue('H' . $row, '');
                $row++;


                // ■ 一次下請業者情報
                $row += 2;
                $sheet->setCellValue('A' . $row, '【一次下請負人一覧】');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                // ヘッダー
                $headers = ['No', '会社名/代表者', '建設業許可', '工期/契約日', '現場責任者/主任技術者'];
                $sheet->setCellValue('B' . $row, $headers[0]);
                $sheet->setCellValue('C' . $row, $headers[1]);
                $sheet->mergeCells('C' . $row . ':D' . $row);
                $sheet->setCellValue('E' . $row, $headers[2]);
                $sheet->mergeCells('E' . $row . ':F' . $row);
                $sheet->setCellValue('G' . $row, $headers[3]);
                $sheet->mergeCells('G' . $row . ':H' . $row);
                $sheet->setCellValue('I' . $row, $headers[4]);
                $sheet->mergeCells('I' . $row . ':J' . $row);

                $sheet->getStyle('B' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('B' . $row . ':J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');
                $row++;

                $subs = $this->project->firstTierSubcontractors()->get();
                $idx = 1;

                foreach ($subs as $sub) {
                    $startRow = $row;

                    $sheet->setCellValue('B' . $row, $idx);
                    $sheet->setCellValue('C' . $row, $sub->name);
                    $sheet->mergeCells('C' . $row . ':D' . $row);
                    $sheet->setCellValue('E' . $row, $sub->formatted_licenses); // Subcontractorモデルのアクセサ
                    $sheet->mergeCells('E' . $row . ':F' . $row);

                    $period = ($sub->pivot->period_start ?? '') . ' ～ ' . ($sub->pivot->period_end ?? '');
                    $sheet->setCellValue('G' . $row, "工期: " . $period);
                    $sheet->mergeCells('G' . $row . ':H' . $row);

                    $sheet->setCellValue('I' . $row, "安: " . ($sub->pivot->safety_manager ?? ''));
                    $sheet->mergeCells('I' . $row . ':J' . $row);
                    $row++;

                    // 2行目
                    $sheet->setCellValue('C' . $row, "代表: " . $sub->representative_name);
                    $sheet->mergeCells('C' . $row . ':D' . $row);

                    $sheet->setCellValue('E' . $row, "電話: " . $sub->phone);
                    $sheet->mergeCells('E' . $row . ':F' . $row);

                    $sheet->setCellValue('G' . $row, "契約: " . ($sub->pivot->contract_date ?? ''));
                    $sheet->mergeCells('G' . $row . ':H' . $row);

                    $sheet->setCellValue('I' . $row, "主: " . ($sub->pivot->site_chief_engineer ?? ''));
                    $sheet->mergeCells('I' . $row . ':J' . $row);

                    // 罫線
                    $sheet->getStyle('B' . $startRow . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                    $sheet->getStyle('B' . $startRow . ':B' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER); // Noは中央寄せ
    
                    $sheet->mergeCells('B' . $startRow . ':B' . $row); // No列結合
    
                    $row++;
                    $idx++;
                }

                if ($subs->isEmpty()) {
                    $sheet->setCellValue('C' . $row, '登録された一次下請業者はありません');
                    $sheet->mergeCells('C' . $row . ':J' . $row);
                    $sheet->getStyle('B' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // 全体の枠線（簡易）
                // $sheet->getStyle('B4:J' . ($row-1))->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
