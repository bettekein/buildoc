<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Staff;
use App\Models\Vehicle;
use App\Models\Tool;
use Livewire\Component;

class AllocationManager extends Component
{
    public Project $project;

    // Modal states
    public $showStaffModal = false;
    public $showVehicleModal = false;
    public $showToolModal = false;

    // Form fields for adding
    public $selectedStaffId = null;
    public $staffRole = '';
    public $staffIsForeman = false;
    public $staffStartDate = null;
    public $staffEndDate = null;

    public $selectedVehicleId = null;
    public $vehicleStartDate = null;
    public $vehicleEndDate = null;

    public $selectedToolId = null;
    public $toolStartDate = null;
    public $toolEndDate = null;

    // Active tab
    public $activeTab = 'staff';

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    // Staff methods
    public function openStaffModal()
    {
        $this->resetStaffForm();
        $this->showStaffModal = true;
    }

    public function resetStaffForm()
    {
        $this->selectedStaffId = null;
        $this->staffRole = '';
        $this->staffIsForeman = false;
        $this->staffStartDate = null;
        $this->staffEndDate = null;
    }

    public function addStaff()
    {
        $this->validate([
            'selectedStaffId' => 'required|exists:staff,id',
        ]);

        // Check if already assigned
        if ($this->project->staff()->where('staff_id', $this->selectedStaffId)->exists()) {
            session()->flash('error', 'このスタッフは既に配置されています。');
            return;
        }

        $this->project->staff()->attach($this->selectedStaffId, [
            'is_foreman' => $this->staffIsForeman,
            'role' => $this->staffRole,
            'start_date' => $this->staffStartDate,
            'end_date' => $this->staffEndDate,
        ]);

        $this->showStaffModal = false;
        $this->resetStaffForm();
        session()->flash('message', 'スタッフを配置しました。');
    }

    public function removeStaff($staffId)
    {
        $this->project->staff()->detach($staffId);
        session()->flash('message', 'スタッフの配置を解除しました。');
    }

    public function toggleForeman($staffId)
    {
        $pivot = $this->project->staff()->where('staff_id', $staffId)->first()->pivot;
        $this->project->staff()->updateExistingPivot($staffId, [
            'is_foreman' => !$pivot->is_foreman,
        ]);
    }

    // Vehicle methods
    public function openVehicleModal()
    {
        $this->resetVehicleForm();
        $this->showVehicleModal = true;
    }

    public function resetVehicleForm()
    {
        $this->selectedVehicleId = null;
        $this->vehicleStartDate = null;
        $this->vehicleEndDate = null;
    }

    public function addVehicle()
    {
        $this->validate([
            'selectedVehicleId' => 'required|exists:vehicles,id',
        ]);

        if ($this->project->vehicles()->where('vehicle_id', $this->selectedVehicleId)->exists()) {
            session()->flash('error', 'この車両は既に配置されています。');
            return;
        }

        $this->project->vehicles()->attach($this->selectedVehicleId, [
            'start_date' => $this->vehicleStartDate,
            'end_date' => $this->vehicleEndDate,
        ]);

        $this->showVehicleModal = false;
        $this->resetVehicleForm();
        session()->flash('message', '車両を配置しました。');
    }

    public function removeVehicle($vehicleId)
    {
        $this->project->vehicles()->detach($vehicleId);
        session()->flash('message', '車両の配置を解除しました。');
    }

    // Tool methods
    public function openToolModal()
    {
        $this->resetToolForm();
        $this->showToolModal = true;
    }

    public function resetToolForm()
    {
        $this->selectedToolId = null;
        $this->toolStartDate = null;
        $this->toolEndDate = null;
    }

    public function addTool()
    {
        $this->validate([
            'selectedToolId' => 'required|exists:tools,id',
        ]);

        if ($this->project->tools()->where('tool_id', $this->selectedToolId)->exists()) {
            session()->flash('error', 'この工具は既に配置されています。');
            return;
        }

        $this->project->tools()->attach($this->selectedToolId, [
            'start_date' => $this->toolStartDate,
            'end_date' => $this->toolEndDate,
        ]);

        $this->showToolModal = false;
        $this->resetToolForm();
        session()->flash('message', '工具を配置しました。');
    }

    public function removeTool($toolId)
    {
        $this->project->tools()->detach($toolId);
        session()->flash('message', '工具の配置を解除しました。');
    }

    public function render()
    {
        return view('livewire.projects.allocation-manager', [
            'assignedStaff' => $this->project->staff()->get(),
            'assignedVehicles' => $this->project->vehicles()->get(),
            'assignedTools' => $this->project->tools()->get(),
            'availableStaff' => Staff::whereNotIn('id', $this->project->staff()->pluck('staff.id'))->get(),
            'availableVehicles' => Vehicle::whereNotIn('id', $this->project->vehicles()->pluck('vehicles.id'))->get(),
            'availableTools' => Tool::whereNotIn('id', $this->project->tools()->pluck('tools.id'))->get(),
        ])->layout('layouts.app');
    }
}
