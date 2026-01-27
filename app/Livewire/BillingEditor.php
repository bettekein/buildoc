<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProgressBilling;
use Livewire\Component;

class BillingEditor extends Component
{
    public Project $project;
    public ProgressBilling $billing;
    
    // Form fields
    public $amount_this_time = 0;
    public $progress_rate = 0;
    public $tax_amount = 0;
    public $billing_date;
    public $payment_date;
    public $note;
    public $billing_number;

    public function mount(Project $project, ProgressBilling $billing)
    {
        $this->project = $project;
        $this->billing = $billing;
        
        $this->amount_this_time = $billing->amount_this_time;
        $this->progress_rate = $billing->progress_rate;
        $this->tax_amount = $billing->tax_amount;
        $this->billing_date = $billing->billing_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->payment_date = $billing->payment_date?->format('Y-m-d');
        $this->note = $billing->note;
        $this->billing_number = $billing->billing_number;
    }

    public function updatedAmountThisTime()
    {
        // Auto-calculate tax (assuming 10% and floor rounding)
        $this->tax_amount = floor($this->amount_this_time * 0.10);
        
        // Update rate based on total contract amount
        $contractTotal = $this->project->quotationItems->sum('total_amount');
        
        if ($contractTotal > 0) {
            // Calculate previous billings total
            $previousBilled = ProgressBilling::where('project_id', $this->project->id)
                ->where('id', '!=', $this->billing->id)
                ->sum('amount_this_time');
            
            $cumulativeTotal = $previousBilled + $this->amount_this_time;
            
            // Calculate progress rate (cumulative)
            $this->progress_rate = round(($cumulativeTotal / $contractTotal) * 100, 2);
        } else {
            $this->progress_rate = 0;
        }
    }

    public function save()
    {
        $this->validate([
            'amount_this_time' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
        ]);

        $this->billing->update([
            'amount_this_time' => $this->amount_this_time,
            'progress_rate' => $this->progress_rate,
            'tax_amount' => $this->tax_amount,
            'billing_date' => $this->billing_date,
            'payment_date' => $this->payment_date,
            'note' => $this->note,
            'billing_number' => $this->billing_number,
            'status' => 'billed',
        ]);

        session()->flash('message', '請求情報を保存しました。');
        
        // Optional: Redirect back to list
        // return redirect()->route('billings.index', $this->project);
    }

    public function render()
    {
        return view('livewire.billing-editor')->layout('layouts.app');
    }
}
