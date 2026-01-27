<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;
use App\Traits\Sortable;

class AuditLogViewer extends Component
{
    use WithPagination, Sortable;

    public $filterEvent = '';
    public $filterModel = '';

    public function mount()
    {
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function render()
    {
        $audits = Audit::with('user');

        if ($this->filterEvent) {
            $audits->where('event', $this->filterEvent);
        }

        if ($this->filterModel) {
            $audits->where('auditable_type', 'like', '%' . $this->filterModel . '%');
        }

        return view('livewire.audit-log-viewer', [
            'audits' => $audits->orderBy($this->sortField, $this->sortDirection)->paginate(20)
        ])->layout('layouts.app');
    }
}
