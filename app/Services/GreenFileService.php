<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Tool;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GreenFileService
{
    /**
     * Generate Green Files (Safety Documents)
     *
     * @param Project $project
     * @param array $selectedDocuments List of document types to generate
     * @param array $selectedStaffIds
     * @param array $selectedVehicleIds
     * @param array $selectedToolIds
     * @return string Path to the generated ZIP file
     */
    public function generate(
        Project $project,
        array $selectedDocuments,
        array $selectedStaffIds = [],
        array $selectedVehicleIds = [],
        array $selectedToolIds = []
    ): string {
        $files = [];
        $tempDir = storage_path('app/temp/greenfiles/' . uniqid());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Pre-fetch related data
        $staffMembers = Staff::whereIn('id', $selectedStaffIds)->get();
        $vehicles = Vehicle::whereIn('id', $selectedVehicleIds)->get();
        $tools = Tool::whereIn('id', $selectedToolIds)->get();

        foreach ($selectedDocuments as $docType) {
            $pdfContent = null;
            $fileName = $docType . '.pdf';

            switch ($docType) {
                case 'worker_list': // 作業員名簿 (Form 5)
                    $pdfContent = $this->generateWorkerList($project, $staffMembers);
                    break;
                case 'vehicle_notification': // 車両届 (Form 3)
                    $pdfContent = $this->generateVehicleNotification($project, $vehicles);
                    break;
                case 'machinery_notification': // 持込機械届 (Form 2)
                    $pdfContent = $this->generateMachineryNotification($project, $tools);
                    break;
                case 'construction_ledger': // 施工体制台帳 (Form 1)
                    $pdfContent = $this->generateConstructionLedger($project);
                    break;
                    
                // Add other cases here (up to 15)
                
                default:
                    continue 2;
            }

            if ($pdfContent) {
                $filePath = $tempDir . '/' . $fileName;
                file_put_contents($filePath, $pdfContent);
                $files[] = $filePath;
            }
        }

        // Zip files
        $zipFileName = 'green_files_' . now()->format('YmdHis') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temp
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($tempDir);

        return $zipFileName; // Return filename relative to storage/public or full path? Returning generic path.
    }

    protected function generateWorkerList(Project $project, Collection $staffMembers)
    {
        $data = [
            'project' => $project,
            'staffMembers' => $staffMembers,
            'tenant' => $project->tenant,
        ];
        
        $pdf = Pdf::loadView('pdf.green-files.worker_list', $data);
        $pdf->setPaper('a4', 'landscape'); // Usually landscape for Worker List
        return $pdf->output();
    }

    protected function generateVehicleNotification(Project $project, Collection $vehicles)
    {
        $data = [
            'project' => $project,
            'vehicles' => $vehicles,
            'tenant' => $project->tenant,
        ];

        $pdf = Pdf::loadView('pdf.green-files.vehicle_notification', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->output();
    }

    protected function generateMachineryNotification(Project $project, Collection $tools)
    {
        $data = [
            'project' => $project,
            'tools' => $tools,
            'tenant' => $project->tenant,
        ];

        $pdf = Pdf::loadView('pdf.green-files.machinery_notification', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->output();
    }
    
    protected function generateConstructionLedger(Project $project)
    {
        $data = [
            'project' => $project,
            'tenant' => $project->tenant,
            'customer' => $project->customer,
        ];
        
        $pdf = Pdf::loadView('pdf.green-files.construction_ledger', $data);
        $pdf->setPaper('a4', 'landscape'); // Often A3 or A4 landscape
        return $pdf->output();
    }

    /**
     * Validate data for expiry logic
     */
    public function validateExpiry(Collection $staffMembers, Collection $vehicles, Collection $tools): array
    {
        $warnings = [];

        foreach ($staffMembers as $staff) {
            // Check health check date (e.g., valid for 1 year)
            if (!empty($staff->health_info['checkup_date'])) {
                $checkupDate = \Carbon\Carbon::parse($staff->health_info['checkup_date']);
                if ($checkupDate->diffInYears(now()) >= 1) {
                    $warnings[] = "Staff {$staff->name}: Health checkup expired (>1 year).";
                }
            } else {
                 $warnings[] = "Staff {$staff->name}: Missing health checkup date.";
            }
        }

        foreach ($vehicles as $vehicle) {
            if ($vehicle->inspection_expiry && $vehicle->inspection_expiry < now()) {
                $warnings[] = "Vehicle {$vehicle->plate_number}: Inspection expired.";
            }
             // Check insurance expiry
            if (!empty($vehicle->insurance_info['expiry'])) {
                 $insuranceExpiry = \Carbon\Carbon::parse($vehicle->insurance_info['expiry']);
                 if ($insuranceExpiry < now()) {
                     $warnings[] = "Vehicle {$vehicle->plate_number}: Insurance expired.";
                 }
            }
        }
        
         foreach ($tools as $tool) {
            if ($tool->last_inspection_date && \Carbon\Carbon::parse($tool->last_inspection_date)->diffInMonths(now()) >= 12) { // Assuming annual inspection
                $warnings[] = "Tool {$tool->name}: Last inspection > 1 year ago.";
            }
        }

        return $warnings;
    }
}
