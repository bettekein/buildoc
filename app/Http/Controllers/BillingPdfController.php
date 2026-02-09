<?php

namespace App\Http\Controllers;

use App\Models\ProgressBilling;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class BillingPdfController extends Controller
{
    public function show(ProgressBilling $billing)
    {
        $billing->load(['project.customer', 'project.tenant']);

        $html = view('billings.pdf', [
            'billing' => $billing,
            'project' => $billing->project,
            'customer' => $billing->project->customer,
            'tenant' => $billing->project->tenant,
        ])->render();

        $pdf = Browsershot::html($html)
            ->setNodeBinary('C:\\Program Files\\nodejs\\node.exe')
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->showBackground()
            ->pdf();

        return response()->streamDownload(
            fn() => print ($pdf),
            "billing-{$billing->billing_number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}