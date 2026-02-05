<?php

namespace App\Livewire\Customers;

use Livewire\Component;

use App\Models\Customer;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public $search = '';
    public $showCreateModal = false;
    public $showTrashed = false;
    public $name, $phone, $address; // Basic fields for now

    public function restore($id)
    {
        $customer = Customer::withTrashed()->find($id);
        if ($customer) {
            $customer->restore();
            session()->flash('message', '顧客を復元しました。');
        }
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            session()->flash('message', '顧客をゴミ箱に移動しました。');
        }
    }

    public function forceDelete($id)
    {
        $customer = Customer::withTrashed()->find($id);
        if ($customer) {
            $customer->forceDelete();
            session()->flash('message', '顧客を完全に削除しました。');
        }
    }

    public $customerId = null;

    // ... (existing properties)

    public function create()
    {
        $this->reset(['name', 'phone', 'address', 'customerId']);
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->showCreateModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        if ($this->customerId) {
            $customer = Customer::findOrFail($this->customerId);
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            session()->flash('message', '顧客情報を更新しました。');
        } else {
            Customer::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'address' => $this->address,
            ]);
            session()->flash('message', '顧客を登録しました。');
        }

        $this->showCreateModal = false;
        $this->reset(['name', 'phone', 'address', 'customerId']);
    }

    public function render()
    {
        $query = Customer::where('name', 'like', '%' . $this->search . '%')->latest();

        if ($this->showTrashed) {
            $query->onlyTrashed();
        }

        return view('livewire.customers.manager', [
            'customers' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}
