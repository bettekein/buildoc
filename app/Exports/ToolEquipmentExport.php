<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolEquipmentExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        $tools = $this->project->tools()->get();

        return $tools->map(function ($tool, $index) {
            return [
                'no' => $index + 1,
                'name' => $tool->name,
                'specification' => $tool->specification ?? '',
                'management_no' => $tool->management_no ?? '',
                'last_inspection_date' => $tool->last_inspection_date?->format('Y/m/d') ?? '',
                'start_date' => $tool->pivot->start_date ?? '',
                'end_date' => $tool->pivot->end_date ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            '工具・機材名',
            '仕様・規格',
            '管理番号',
            '最終点検日',
            '持込開始日',
            '持込終了日',
        ];
    }

    public function title(): string
    {
        return '持込機械届（電動工具）';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF2CC'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getStyle('A:A')->getAlignment()->setHorizontal('center');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 20,
            'D' => 15,
            'E' => 12,
            'F' => 12,
            'G' => 12,
        ];
    }
}
