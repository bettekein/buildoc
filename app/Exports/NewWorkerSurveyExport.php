<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Staff;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewWorkerSurveyExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected Project $project;
    protected ?Staff $staff;

    public function __construct(Project $project, ?Staff $staff = null)
    {
        $this->project = $project;
        $this->staff = $staff;
    }

    public function collection()
    {
        // 特定のスタッフ指定がある場合はそのスタッフのみ、なければ全配置スタッフ
        if ($this->staff) {
            $staffList = collect([$this->staff]);
        } else {
            $staffList = $this->project->staff()->get();
        }

        return $staffList->map(function ($member, $index) {
            return [
                'no' => $index + 1,
                'furigana' => $member->furigana ?? '',
                'name' => $member->name,
                'address' => $member->address ?? '',
                'emergency_name' => $this->getEmergencyField($member->emergency_contact, 'name'),
                'emergency_relation' => $this->getEmergencyField($member->emergency_contact, 'relation'),
                'emergency_phone' => $this->getEmergencyField($member->emergency_contact, 'phone'),
                'company' => $member->tenant?->name ?? '',
                'employment_type' => $member->employment_type ?? '正社員',
                'is_sole_proprietor' => $member->is_sole_proprietor ? '該当' : '-',
                'has_special_labor_insurance' => $member->has_special_labor_insurance ? '加入済' : '-',
                'experience_years' => $member->experience_years ?? '',
                'last_medical_checkup' => $member->last_medical_checkup?->format('Y/m/d') ?? '',
                'health_status' => $this->getHealthField($member->health_info, 'status'),
                'sendout_education' => $member->sendout_education_date?->format('Y/m/d') ?? '',
                'skill_trainings' => $this->formatArrayField($member->skill_trainings),
                'special_educations' => $this->formatArrayField($member->special_educations),
                'driver_licenses' => $this->formatDriverLicenses($member->driver_licenses),
            ];
        });
    }

    protected function getEmergencyField($data, $field): string
    {
        if (!is_array($data))
            return '';
        return $data[$field] ?? '';
    }

    protected function getHealthField($data, $field): string
    {
        if (!is_array($data))
            return '';
        return $data[$field] ?? '良好';
    }

    protected function formatArrayField($data): string
    {
        if (!is_array($data))
            return '';
        return implode(', ', array_column($data, 'name'));
    }

    protected function formatDriverLicenses($data): string
    {
        if (!is_array($data))
            return '';
        return implode(', ', array_map(function ($license) {
            return $license['type'] ?? '';
        }, $data));
    }

    public function headings(): array
    {
        return [
            'No.',
            'ふりがな',
            '氏名',
            '現住所',
            '緊急連絡先（氏名）',
            '続柄',
            '電話番号',
            '所属会社',
            '雇用形態',
            '一人親方',
            '労災特別加入',
            '経験年数',
            '健康診断日',
            '健康状態',
            '送出教育日',
            '技能講習',
            '特別教育',
            '運転免許',
        ];
    }

    public function title(): string
    {
        return '新規入場者調査票';
    }

    public function styles(Worksheet $sheet)
    {
        // プロジェクト情報を先頭に追加
        $sheet->insertNewRowBefore(1, 4);
        $sheet->setCellValue('A1', '新規入場者調査票');
        $sheet->setCellValue('A2', '事業所名: ' . $this->project->name);
        $sheet->setCellValue('A3', '作成日: ' . date('Y年m月d日'));

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
        ]);

        // ヘッダー行（5行目）のスタイル
        $sheet->getStyle('A5:R5')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCE4D6'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // データ部分の枠線
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:R{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No.
            'B' => 15,  // ふりがな
            'C' => 12,  // 氏名
            'D' => 30,  // 現住所
            'E' => 12,  // 緊急連絡先（氏名）
            'F' => 8,   // 続柄
            'G' => 15,  // 電話番号
            'H' => 18,  // 所属会社
            'I' => 10,  // 雇用形態
            'J' => 8,   // 一人親方
            'K' => 10,  // 労災特別加入
            'L' => 10,  // 経験年数
            'M' => 12,  // 健康診断日
            'N' => 8,   // 健康状態
            'O' => 12,  // 送出教育日
            'P' => 25,  // 技能講習
            'Q' => 25,  // 特別教育
            'R' => 20,  // 運転免許
        ];
    }
}
