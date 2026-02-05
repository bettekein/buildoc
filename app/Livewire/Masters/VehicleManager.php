<?php

namespace App\Livewire\Masters;

use Livewire\Component;

use App\Models\Vehicle;
use Livewire\WithPagination;

class VehicleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showTrashed = false;
    public $name, $plate_number, $inspection_expiry;

    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->find($id);
        if ($vehicle) {
            $vehicle->restore();
            session()->flash('message', '車両を復元しました。');
        }
    }

    public function delete($id)
    {
        $vehicle = Vehicle::find($id);
        if ($vehicle) {
            $vehicle->delete();
            session()->flash('message', '車両をゴミ箱に移動しました。');
        }
    }

    public function forceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->find($id);
        if ($vehicle) {
            $vehicle->forceDelete();
            session()->flash('message', '車両を完全に削除しました。');
        }
    }

    public function create()
    {
        $this->reset(['name', 'plate_number', 'inspection_expiry']);
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'plate_number' => 'nullable|string',
            'inspection_expiry' => 'nullable|date',
        ]);

        Vehicle::create([
            'name' => $this->name,
            'plate_number' => $this->plate_number,
            'inspection_expiry' => $this->inspection_expiry,
        ]);

        $this->showCreateModal = false;
        session()->flash('message', '車両を登録しました。');
    }

    public function render()
    {
        $query = Vehicle::where('name', 'like', '%' . $this->search . '%')->latest();

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        return view('livewire.masters.vehicle-manager', [
            'vehicles' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
