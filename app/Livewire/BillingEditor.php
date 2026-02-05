<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProgressBilling;
use App\Services\BillingCalculationService;
use Livewire\Component;

class BillingEditor extends Component
{
    public Project $project;
    public ProgressBilling $billing;
    
    // Inputs (Live)
    public $progress_rate = 0; // %
    public $amount_this_time = 0; // Tax excluded
    public $offset_amount = 0;
    public $retention_release_amount = 0;

    // Computed / Display Only
    public $contract_amount = 0;
    public $previous_billed_amount = 0; // Cumulative until last time
    public $cumulative_amount = 0; // New cumulative
    public $retention_rate = 0;
    public $current_retention_amount = 0;
    public $tax_amount = 0;
    public $gross_billing_amount = 0;
    public $final_billing_amount = 0;

    // Metadata
    public $billing_date;
    public $payment_date;
    public $note;
    public $billing_number;

    public function mount(Project $project, ProgressBilling $billing)
    {
        $this->project = $project;
        $this->billing = $billing;
        
        $this->contract_amount = $project->contract_amount ?? 0;
        $this->retention_rate = $project->retention_rate ?? 20.00;

        // Determine previous billed amount (from DB)
        // If this is a new record or existing, we need to sum up *previous* bills.
        // If we are editing an existing bill, we should sum everything BEFORE this bill.
        $this->previous_billed_amount = $this->project->progressBillings()
            ->where('id', '<', $this->billing->id ?? 999999999) // Simple check if id exists
            ->sum('amount_this_time') ?? 0;

        // Initialize fields
        $this->progress_rate = $billing->progress_rate ?? 0;
        $this->offset_amount = $billing->offset_amount ?? 0;
        $this->retention_release_amount = $billing->retention_release_amount ?? 0;
        
        $this->billing_date = $billing->billing_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->payment_date = $billing->payment_date?->format('Y-m-d');
        $this->note = $billing->note;
        $this->billing_number = $billing->billing_number;

        // Initial Calc
        $this->recalculate();
    }

    public function updatedProgressRate()
    {
        $this->recalculate('rate');
    }

    // If user manually adjusts amount, we might inverse-calc rate?
    // Requirement says "user rewrites 'current progress rate' OR 'current amount'".
    public function updatedAmountThisTime()
    {
         $this->recalculate('amount');
    }

    public function updatedOffsetAmount()
    {
        $this->recalculate();
    }

    public function updatedRetentionReleaseAmount()
    {
        $this->recalculate();
    }

    private function recalculate($source = 'rate')
    {
        $service = new BillingCalculationService();

        // If source is amount, we derive rate. 
        // Cumulative = Previous + Current
        // Rate = Cumulative / Contract * 100
        if ($source === 'amount') {
            $this->amount_this_time = (float) $this->amount_this_time;
            $cumulative = $this->previous_billed_amount + $this->amount_this_time;
            if ($this->contract_amount > 0) {
                // Determine rate from amount
                $this->progress_rate = round(($cumulative / $this->contract_amount) * 100, 2);
            }
        }

        // Calculate using Service (Master is Rate usually)
        $result = $service->calculate(
            contractAmount: (float) $this->contract_amount,
            progressRate: (float) $this->progress_rate,
            previousTotalProgressAmount: (float) $this->previous_billed_amount,
            retentionRate: (float) $this->retention_rate,
            offsetAmount: (float) $this->offset_amount,
            retentionReleaseAmount: (float) $this->retention_release_amount,
            taxRate: 10.00 // Fixed for now
        );

        // Map results back to properties
        $this->cumulative_amount = $result['cumulative_amount'];
        
        // If source was rate, update amount
        if ($source === 'rate') {
            $this->amount_this_time = $result['amount_this_time'];
        }
        
        $this->tax_amount = $result['tax_amount'];
        $this->gross_billing_amount = $result['gross_billing_amount'];
        $this->current_retention_amount = $result['retention_money'];
        $this->final_billing_amount = $result['final_billing_amount'];
    }

    public function save()
    {
        // Validation checks
        // 1. Cumulative amount <= Contract Amount (Allow small margin for rounding?)
        if ($this->cumulative_amount > $this->contract_amount) {
             $this->addError('progress_rate', '累計出来高が契約金額を超過しています。');
             return;
        }

        // 2. Current Amount >= 0 (Usually)
        if ($this->amount_this_time < 0) {
            $this->addError('amount_this_time', '今回出来高がマイナスです。');
            return;
        }

        $this->validate([
            'billing_date' => 'required|date',
            'billing_number' => 'nullable|string|max:255',
        ]);

        $this->billing->fill([
            'project_id' => $this->project->id, 
            // 'tenant_id' handled by trait? usually yes but better be safe if creating
            'billing_round' => $this->billing->billing_round ?? ($this->project->progressBillings()->count() + 1),
            'progress_rate' => $this->progress_rate,
            'previous_billed_amount' => $this->previous_billed_amount,
            'cumulative_amount' => $this->cumulative_amount,
            'amount_this_time' => $this->amount_this_time,
            'retention_money' => $this->current_retention_amount,
            'retention_release_amount' => $this->retention_release_amount,
            'offset_amount' => $this->offset_amount,
            'tax_amount' => $this->tax_amount,
            'billing_date' => $this->billing_date,
            'payment_date' => $this->payment_date,
            'note' => $this->note,
            'billing_number' => $this->billing_number,
            'status' => 'billed',
        ]);
        
        // If creating, tenant_id is needed if not auto-set by trait properly in Livewire context without auth user sometimes?
        // But we have auth user. Trait should handle it.

        $this->billing->save();

        session()->flash('message', '請求情報を保存しました。');
        
        // return redirect()->route('billings.index', $this->project);
    }

    public function render()
    {
        return view('livewire.billing-editor')->layout('layouts.app');
    }
}
