<?php

namespace App\Livewire\Projects;

use Livewire\Component;

use App\Models\Project;
use App\Models\Customer;
use Livewire\Attributes\Rule;

class Edit extends Component
{
    public Project $project;

    #[Rule('required|exists:customers,id')]
    public $customer_id;

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required|date')]
    public $period_start;

    #[Rule('required|date|after_or_equal:period_start')]
    public $period_end;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->customer_id = $project->customer_id;
        $this->name = $project->name;
        $this->period_start = $project->period_start?->format('Y-m-d');
        $this->period_end = $project->period_end?->format('Y-m-d');
    }

    public function save()
    {
        $validated = $this->validate();

        $this->project->update($validated);

        session()->flash('message', '案件情報を更新しました。');

        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('livewire.projects.edit', [
            'customers' => Customer::all()
        ])->layout('layouts.app');
    }
}
