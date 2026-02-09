<?php

namespace App\Livewire\Masters;

use Livewire\Component;
use App\Models\MasterWorkCategory;
use Livewire\WithPagination;

class WorkCategoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $sort_order = 0;
    public $editingId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sort_order' => 'integer',
    ];

    public function render()
    {
        $categories = MasterWorkCategory::where('name', 'like', "%{$this->search}%")
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('livewire.masters.work-category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    public function edit($id)
    {
        $category = MasterWorkCategory::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->sort_order = $category->sort_order;
    }

    public function cancel()
    {
        $this->reset(['editingId', 'name', 'sort_order']);
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            MasterWorkCategory::find($this->editingId)->update([
                'name' => $this->name,
                'sort_order' => $this->sort_order,
            ]);
            session()->flash('message', '工種を更新しました。');
        } else {
            // Auto sort order if 0
            if ($this->sort_order == 0) {
                $maxSort = MasterWorkCategory::max('sort_order');
                $this->sort_order = $maxSort ? $maxSort + 10 : 10;
            }

            MasterWorkCategory::create([
                'name' => $this->name,
                'sort_order' => $this->sort_order,
            ]);
            session()->flash('message', '工種を登録しました。');
        }

        $this->cancel();
    }

    public function delete($id)
    {
        MasterWorkCategory::find($id)->delete();
        session()->flash('message', '工種を削除しました。');
    }
}
