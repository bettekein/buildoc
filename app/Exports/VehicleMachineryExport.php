<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleMachineryExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        $vehicles = $this->project->vehicles()->get();

        return $vehicles->map(function ($vehicle, $index) {
            return [
                'no' => $index + 1,
                'name' => $vehicle->name,
                'model_name' => $vehicle->model_name ?? '',
                'plate_number' => $vehicle->plate_number ?? '',
                'owner_name' => $vehicle->owner_name ?? '',
                'inspection_expiry' => $vehicle->inspection_expiry?->format('Y/m/d') ?? '',
                'start_date' => $vehicle->pivot->start_date ?? '',
                'end_date' => $vehicle->pivot->end_date ?? '',
                'insurance_info' => $this->formatInsurance($vehicle->insurance_info),
            ];
        });
    }

    protected function formatInsurance($info): string
    {
        if (!is_array($info)) {
            return '';
        }
        $company = $info['company'] ?? '';
        $number = $info['number'] ?? '';
        $expiry = $info['expiry'] ?? '';
        return trim("{$company} {$number} ({$expiry})");
    }

    public function headings(): array
    {
        return [
            'No.',
            '機械名称',
            '型式・能力',
            'ナンバー',
            '所有者',
            '車検満了日',
            '持込開始日',
            '持込終了日',
            '保険情報',
        ];
    }

    public function title(): string
    {
        return '持込機械届（車両）';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DEEBF7'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
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
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 12,
            'G' => 12,
            'H' => 12,
            'I' => 30,
        ];
    }
}
