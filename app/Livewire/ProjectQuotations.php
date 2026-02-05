<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

use App\Traits\Sortable;

class ProjectQuotations extends Component
{
    use WithPagination, Sortable;

    public $showTrashed = false;
    public $search = '';

    public function restore($id)
    {
        $project = Project::withTrashed()->find($id);
        $project->restore();
        session()->flash('message', '案件を復元しました。');
    }

    public function delete($id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            session()->flash('message', '案件をゴミ箱に移動しました。');
        }
    }

    public function forceDelete($id)
    {
        $project = Project::withTrashed()->find($id);
        $project->forceDelete();
        session()->flash('message', '案件を完全に削除しました。');
    }

    public function create()
    {
        return redirect()->route('projects.create');
    }

    public function exportCsv()
    {
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['ID', '案件名', '顧客名', '工期開始', '工期終了', 'ステータス', '作成日']);

        $query = Project::with('customer')->orderBy($this->sortField, $this->sortDirection);
        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        $query->chunk(100, function ($projects) use ($csv) {
            foreach ($projects as $project) {
                $csv->insertOne([
                    $project->id,
                    $project->name,
                    $project->customer->name ?? '',
                    $project->period_start?->format('Y-m-d'),
                    $project->period_end?->format('Y-m-d'),
                    $project->status,
                    $project->created_at->format('Y-m-d H:i:s'),
                ]);
            }
        });

        return response()->streamDownload(function () use ($csv) {
            echo $csv->toString();
        }, 'projects-' . date('Ymd') . '.csv');
    }

    public function render()
    {
        $query = Project::with('customer')->orderBy($this->sortField, $this->sortDirection);

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return view('livewire.project-quotations', [
            'projects' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
