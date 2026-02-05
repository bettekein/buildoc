<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;

class AuditLogViewer extends Component
{
    use WithPagination;

    public $search = '';
    public $eventFilter = '';
    public $modelFilter = '';

    public function render()
    {
        $query = Audit::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('event', 'like', '%' . $this->search . '%')
                    ->orWhere('auditable_type', 'like', '%' . $this->search . '%')
                    ->orWhere('old_values', 'like', '%' . $this->search . '%')
                    ->orWhere('new_values', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->eventFilter) {
            $query->where('event', $this->eventFilter);
        }

        if ($this->modelFilter) {
            $query->where('auditable_type', 'like', '%' . $this->modelFilter . '%');
        }

        $audits = $query->latest()->paginate(20);

        return view('livewire.admin.audit-log-viewer', [
            'audits' => $audits
        ])->layout('layouts.app');
    }
}
