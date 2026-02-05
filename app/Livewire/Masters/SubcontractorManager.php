<?php

namespace App\Livewire\Masters;

use Livewire\Component;
use App\Models\Subcontractor;
use Livewire\WithPagination;

class SubcontractorManager extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;

    // Form fields
    public $name = '';
    public $name_kana = '';
    public $representative_name = '';
    public $representative_title = '';
    public $postal_code = '';
    public $address = '';
    public $phone = '';
    public $fax = '';
    public $email = '';
    public $safety_officer = '';
    public $employment_manager = '';
    public $chief_engineer = '';

    // Construction licenses (dynamic array)
    public $licenses = [];
    public $newLicenseType = '';
    public $newLicenseAuthority = '知事';
    public $newLicenseCategory = '一般';
    public $newLicenseNumber = '';
    public $newLicenseDate = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'name_kana' => 'nullable|string|max:255',
        'representative_name' => 'nullable|string|max:255',
        'postal_code' => 'nullable|string|max:10',
        'address' => 'nullable|string|max:500',
        'phone' => 'nullable|string|max:20',
        'fax' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
    ];

    public function render()
    {
        $subcontractors = Subcontractor::where('name', 'like', "%{$this->search}%")
            ->orWhere('address', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.masters.subcontractor-manager', [
            'subcontractors' => $subcontractors,
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->reset(['name', 'name_kana', 'representative_name', 'representative_title', 'postal_code', 'address', 'phone', 'fax', 'email', 'safety_officer', 'employment_manager', 'chief_engineer', 'licenses', 'editingId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $sub = Subcontractor::findOrFail($id);
        $this->editingId = $id;
        $this->name = $sub->name;
        $this->name_kana = $sub->name_kana;
        $this->representative_name = $sub->representative_name;
        $this->representative_title = $sub->representative_title;
        $this->postal_code = $sub->postal_code;
        $this->address = $sub->address;
        $this->phone = $sub->phone;
        $this->fax = $sub->fax;
        $this->email = $sub->email;
        $this->safety_officer = $sub->safety_officer;
        $this->employment_manager = $sub->employment_manager;
        $this->chief_engineer = $sub->chief_engineer;
        $this->licenses = $sub->construction_licenses ?? [];
        $this->showModal = true;
    }

    public function addLicense()
    {
        if ($this->newLicenseType && $this->newLicenseNumber) {
            $this->licenses[] = [
                'type' => $this->newLicenseType,
                'authority' => $this->newLicenseAuthority,
                'category' => $this->newLicenseCategory,
                'number' => $this->newLicenseNumber,
                'date' => $this->newLicenseDate,
            ];
            $this->reset(['newLicenseType', 'newLicenseNumber', 'newLicenseDate']);
            $this->newLicenseAuthority = '知事';
            $this->newLicenseCategory = '一般';
        }
    }

    public function removeLicense($index)
    {
        unset($this->licenses[$index]);
        $this->licenses = array_values($this->licenses);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'name_kana' => $this->name_kana,
            'representative_name' => $this->representative_name,
            'representative_title' => $this->representative_title,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'email' => $this->email,
            'safety_officer' => $this->safety_officer,
            'employment_manager' => $this->employment_manager,
            'chief_engineer' => $this->chief_engineer,
            'construction_licenses' => $this->licenses,
        ];

        if ($this->editingId) {
            Subcontractor::find($this->editingId)->update($data);
            session()->flash('message', '下請業者情報を更新しました。');
        } else {
            Subcontractor::create($data);
            session()->flash('message', '下請業者を登録しました。');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        Subcontractor::find($id)->delete();
        session()->flash('message', '下請業者を削除しました。');
    }
}
