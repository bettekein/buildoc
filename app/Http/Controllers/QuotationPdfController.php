<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationPdfController extends Controller
{
    public function show(Project $project)
    {
        $project->load(['customer', 'tenant', 'quotationItems.details']);

        $pdf = Pdf::loadView('quotations.pdf', [
            'project' => $project,
        ]);

        // Optional: Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("quotation-{$project->id}.pdf");
    }
}
