<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SocialInsuranceExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        $staff = $this->project->staff()->get();

        return $staff->map(function ($member, $index) {
            $insurance = is_array($member->social_insurance_status) ? $member->social_insurance_status : [];

            return [
                'no' => $index + 1,
                'furigana' => $member->furigana ?? '',
                'name' => $member->name,
                'health_insurance' => $this->getInsuranceType($insurance, 'health'),
                'health_insurance_number' => $this->getInsuranceNumber($insurance, 'health'),
                'pension' => $this->getInsuranceType($insurance, 'pension'),
                'pension_number' => $this->getInsuranceNumber($insurance, 'pension'),
                'employment' => $this->getInsuranceType($insurance, 'employment'),
                'employment_number' => $this->getInsuranceNumber($insurance, 'employment'),
                'labor_insurance' => $member->has_special_labor_insurance ? '特別加入' : ($this->getInsuranceType($insurance, 'labor') ?: '適用'),
            ];
        });
    }

    protected function getInsuranceType($data, $type): string
    {
        if (!isset($data[$type]))
            return '';

        $status = $data[$type];
        if (is_bool($status)) {
            return $status ? '加入' : '未加入';
        }
        if (is_array($status)) {
            return $status['status'] ?? ($status['enrolled'] ? '加入' : '未加入');
        }
        return (string) $status;
    }

    protected function getInsuranceNumber($data, $type): string
    {
        if (!isset($data[$type]) || !is_array($data[$type]))
            return '';
        return $data[$type]['number'] ?? '';
    }

    public function headings(): array
    {
        return [
            'No.',
            'ふりがな',
            '氏名',
            '健康保険',
            '被保険者番号',
            '厚生年金',
            '基礎年金番号',
            '雇用保険',
            '被保険者番号',
            '労災保険',
        ];
    }

    public function title(): string
    {
        return '社会保険加入状況';
    }

    public function styles(Worksheet $sheet)
    {
        // プロジェクト情報を先頭に追加
        $sheet->insertNewRowBefore(1, 5);
        $sheet->setCellValue('A1', '全建統一様式第5号－別紙');
        $sheet->setCellValue('A2', '社会保険加入状況');
        $sheet->setCellValue('A3', '事業所名: ' . $this->project->name);
        $sheet->setCellValue('A4', '作成日: ' . date('Y年m月d日'));

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['size' => 10],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
        ]);

        // ヘッダー行（6行目）のスタイル
        $sheet->getStyle('A6:J6')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DDEBF7'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // データ部分の枠線
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A6:J{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // 中央揃え
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F:F')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('J:J')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No.
            'B' => 15,  // ふりがな
            'C' => 12,  // 氏名
            'D' => 12,  // 健康保険
            'E' => 15,  // 被保険者番号
            'F' => 12,  // 厚生年金
            'G' => 15,  // 基礎年金番号
            'H' => 10,  // 雇用保険
            'I' => 15,  // 被保険者番号
            'J' => 10,  // 労災保険
        ];
    }
}
