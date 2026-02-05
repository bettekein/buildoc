<?php

namespace App\Livewire\Masters;

use Livewire\Component;

use App\Models\Tool;
use Livewire\WithPagination;

class ToolManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showTrashed = false;
    public $name, $management_no, $last_inspection_date;

    public function restore($id)
    {
        $tool = Tool::withTrashed()->find($id);
        if ($tool) {
            $tool->restore();
            session()->flash('message', '工具を復元しました。');
        }
    }

    public function delete($id)
    {
        $tool = Tool::find($id);
        if ($tool) {
            $tool->delete();
            session()->flash('message', '工具をゴミ箱に移動しました。');
        }
    }

    public function forceDelete($id)
    {
        $tool = Tool::withTrashed()->find($id);
        if ($tool) {
            $tool->forceDelete();
            session()->flash('message', '工具を完全に削除しました。');
        }
    }

    public $toolId = null;

    // ... (existing properties)

    public function create()
    {
        $this->reset(['name', 'management_no', 'last_inspection_date', 'toolId']);
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $tool = Tool::findOrFail($id);
        $this->toolId = $tool->id;
        $this->name = $tool->name;
        $this->management_no = $tool->management_no;
        $this->last_inspection_date = $tool->last_inspection_date ? $tool->last_inspection_date->format('Y-m-d') : null;
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'management_no' => 'nullable|string',
            'last_inspection_date' => 'nullable|date',
        ]);

        if ($this->toolId) {
            $tool = Tool::findOrFail($this->toolId);
            $tool->update([
                'name' => $this->name,
                'management_no' => $this->management_no,
                'last_inspection_date' => $this->last_inspection_date,
            ]);
            session()->flash('message', '工具情報を更新しました。');
        } else {
            Tool::create([
                'name' => $this->name,
                'management_no' => $this->management_no,
                'last_inspection_date' => $this->last_inspection_date,
            ]);
            session()->flash('message', '工具を登録しました。');
        }

        $this->showCreateModal = false;
        $this->reset(['name', 'management_no', 'last_inspection_date', 'toolId']);
    }

    public function render()
    {
        $query = Tool::where('name', 'like', '%' . $this->search . '%')->latest();

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        return view('livewire.masters.tool-manager', [
            'tools' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
