<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProgressBilling;
use Livewire\Component;

use App\Traits\Sortable;

class BillingManager extends Component
{
    use Sortable;

    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->sortField = 'billing_round';
        $this->sortDirection = 'asc';
    }

    public function createNextBilling()
    {
        // Determine next round number
        $lastRound = $this->project->progressBillings()->max('billing_round') ?? 0;
        $nextRound = $lastRound + 1;

        // Create new billing draft
        $billing = new ProgressBilling();
        $billing->tenant_id = $this->project->tenant_id;
        $billing->project_id = $this->project->id;
        $billing->billing_round = $nextRound;
        $billing->billing_date = now();
        $billing->status = 'unbilled'; // Draft
        $billing->save();

        // Redirect to edit page
        return redirect()->route('billings.edit', [$this->project, $billing]);
    }

    public function exportCsv()
    {
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['回数', '請求日', '今回請求額', '消費税', '出来高率', 'ステータス']);

        $billings = $this->project->progressBillings()
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        foreach ($billings as $billing) {
            $csv->insertOne([
                $billing->billing_round,
                $billing->billing_date?->format('Y-m-d'),
                $billing->amount_this_time,
                $billing->tax_amount,
                $billing->progress_rate . '%',
                $billing->status,
            ]);
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'billings-' . $this->project->id . '-' . date('Ymd') . '.csv');
    }

    public function render()
    {
        return view('livewire.billing-manager', [
            'billings' => $this->project->progressBillings()
                ->orderBy($this->sortField, $this->sortDirection)
                ->get()
        ])->layout('layouts.app');
    }
}
