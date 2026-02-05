<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class TenantManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $tenantId;

    // Form fields
    public $company_name;
    public $license_number;
    public $phone;
    public $address;

    public function create()
    {
        $this->reset(['company_name', 'license_number', 'phone', 'address', 'tenantId']);
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId = $tenant->id;
        $this->company_name = $tenant->company_name;
        $this->license_number = $tenant->license_number;
        $this->phone = $tenant->phone;
        $this->address = $tenant->address;

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Tenant::create([
            'company_name' => $this->company_name,
            'license_number' => $this->license_number,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->showCreateModal = false;
        session()->flash('message', 'テナントを作成しました。');
    }

    public function update()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $tenant = Tenant::findOrFail($this->tenantId);
        $tenant->update([
            'company_name' => $this->company_name,
            'license_number' => $this->license_number,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'テナント情報を更新しました。');
    }

    public function delete($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        session()->flash('message', 'テナントを削除しました。');
    }

    public function render()
    {
        $tenants = Tenant::where('company_name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.tenant-manager', [
            'tenants' => $tenants
        ])->layout('layouts.app');
    }
}
