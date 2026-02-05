<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkerRosterExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
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
            return [
                'no' => $index + 1,
                'name' => $member->name,
                'furigana' => $member->furigana ?? '',
                'birthday' => $member->birthday?->format('Y/m/d') ?? '',
                'age' => $member->birthday ? now()->diffInYears($member->birthday) : '',
                'blood_type' => $member->blood_type ?? '',
                'job_type' => $member->job_type ?? '',
                'experience_years' => $member->experience_years ?? '',
                'is_foreman' => $member->pivot->is_foreman ? '○' : '',
                'role' => $member->pivot->role ?? '',
                'start_date' => $member->pivot->start_date ?? '',
                'end_date' => $member->pivot->end_date ?? '',
                'qualifications' => is_array($member->qualifications) ? implode(', ', $member->qualifications) : ($member->qualifications ?? ''),
                'health_insurance' => $this->getInsuranceStatus($member->social_insurance_status, 'health'),
                'pension' => $this->getInsuranceStatus($member->social_insurance_status, 'pension'),
                'employment' => $this->getInsuranceStatus($member->social_insurance_status, 'employment'),
                'emergency_contact' => $this->formatEmergencyContact($member->emergency_contact),
            ];
        });
    }

    protected function getInsuranceStatus($status, $type): string
    {
        if (!is_array($status)) {
            return '';
        }
        return isset($status[$type]) && $status[$type] ? '○' : '';
    }

    protected function formatEmergencyContact($contact): string
    {
        if (!is_array($contact)) {
            return '';
        }
        $name = $contact['name'] ?? '';
        $phone = $contact['phone'] ?? '';
        $relation = $contact['relation'] ?? '';
        return trim("{$name} ({$relation}) {$phone}");
    }

    public function headings(): array
    {
        return [
            'No.',
            '氏名',
            'フリガナ',
            '生年月日',
            '年齢',
            '血液型',
            '職種',
            '経験年数',
            '職長',
            '担当業務',
            '入場日',
            '退場日',
            '保有資格',
            '健保',
            '年金',
            '雇用',
            '緊急連絡先',
        ];
    }

    public function title(): string
    {
        return '作業員名簿';
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Apply borders to all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:Q{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Center align specific columns
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E:E')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F:F')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I:I')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('N:P')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No.
            'B' => 15,  // 氏名
            'C' => 15,  // フリガナ
            'D' => 12,  // 生年月日
            'E' => 6,   // 年齢
            'F' => 8,   // 血液型
            'G' => 12,  // 職種
            'H' => 10,  // 経験年数
            'I' => 6,   // 職長
            'J' => 15,  // 担当業務
            'K' => 12,  // 入場日
            'L' => 12,  // 退場日
            'M' => 25,  // 保有資格
            'N' => 6,   // 健保
            'O' => 6,   // 年金
            'P' => 6,   // 雇用
            'Q' => 25,  // 緊急連絡先
        ];
    }
}
