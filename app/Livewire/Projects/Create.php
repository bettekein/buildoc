<?php

namespace App\Livewire\Projects;

use Livewire\Component;

use App\Models\Project;
use App\Models\Customer;
use Livewire\Attributes\Rule;

class Create extends Component
{
    #[Rule('required|exists:customers,id')]
    public $customer_id;

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required|date')]
    public $period_start;

    #[Rule('required|date|after_or_equal:period_start')]
    public $period_end;

    public function save()
    {
        $validated = $this->validate();

        $validated['status'] = 'draft'; // Manual setting for now

        // Project defaults
        // tenant_id handled by model events (BelongsToTenant)

        Project::create($validated);

        session()->flash('message', '案件を作成しました。');

        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('livewire.projects.create', [
            'customers' => Customer::all()
        ])->layout('layouts.app');
    }
}
