<?php

namespace App\Http\Controllers;

use App\Models\ProgressBilling;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillingPdfController extends Controller
{
    public function show(Project $project, ProgressBilling $billing)
    {
        // Ensure the billing belongs to the project (optional but good for safety)
        if ($billing->project_id !== $project->id) {
            abort(404);
        }

        $project->load('customer', 'tenant');
        // $billing->load('items'); 

        $html = view('billings.pdf', [
            'project' => $project,
            'billing' => $billing,
        ])->render();

        $pdf = \Spatie\Browsershot\Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"billing-{$billing->billing_number}.pdf\"");
    }
}
