<?php

namespace App\Livewire\Masters;

use Livewire\Component;

use App\Models\Staff;
use Livewire\WithPagination;

class StaffManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showTrashed = false; // Add this
    public $name, $job_type, $hiring_date;

    public function restore($id)
    {
        $staff = Staff::withTrashed()->find($id);
        if ($staff) {
            $staff->restore();
            session()->flash('message', 'スタッフを復元しました。');
        }
    }

    public function delete($id)
    {
        $staff = Staff::find($id);
        if ($staff) {
            $staff->delete();
            session()->flash('message', 'スタッフをゴミ箱に移動しました。');
        }
    }

    public function forceDelete($id)
    {
        $staff = Staff::withTrashed()->find($id);
        if ($staff) {
            $staff->forceDelete();
            session()->flash('message', 'スタッフを完全に削除しました。');
        }
    }

    public $staffId = null;

    // ... (existing properties)

    public function create()
    {
        $this->reset(['name', 'job_type', 'hiring_date', 'staffId']);
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $this->staffId = $staff->id;
        $this->name = $staff->name;
        $this->job_type = $staff->job_type;
        $this->hiring_date = $staff->hiring_date ? $staff->hiring_date->format('Y-m-d') : null;
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'job_type' => 'nullable|string',
            'hiring_date' => 'nullable|date',
        ]);

        if ($this->staffId) {
            $staff = Staff::findOrFail($this->staffId);
            $staff->update([
                'name' => $this->name,
                'job_type' => $this->job_type,
                'hiring_date' => $this->hiring_date,
            ]);
            session()->flash('message', 'スタッフ情報を更新しました。');
        } else {
            Staff::create([
                'name' => $this->name,
                'job_type' => $this->job_type,
                'hiring_date' => $this->hiring_date,
            ]);
            session()->flash('message', 'スタッフを登録しました。');
        }

        $this->showCreateModal = false;
        $this->reset(['name', 'job_type', 'hiring_date', 'staffId']);
    }

    public function render()
    {
        $query = Staff::where('name', 'like', '%' . $this->search . '%')->latest();

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        return view('livewire.masters.staff-manager', [
            'staffs' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
