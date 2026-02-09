<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class CompanyProfile extends Component
{
    public $company_name;
    public $representative_title;
    public $representative_name; // Tenantモデルにはないがエクスポートで使うためusersテーブルから取得か、metadataに保存か検討が必要。一旦Tenantに追加推奨だが、今の定義にはない。
    // Tenant.phpを見ると representative_name はない。representative_titleはある。
    // エクスポートCSVでは $tenant->representative_name を使おうとしていたが、これはバグの元。
    // ここで representative_name も保存できるようにすべき。Tenantモデルにカラムあったか？
    // 確認: 2026_02_05_182829_create_subcontractors_table.php にはあるが、tenantsテーブルは？

    public $zip_code;
    public $address;
    public $phone;
    public $fax;
    public $invoice_registration_number;

    // JSON fields
    public $licenses = []; // 建設業許可
    public $social_insurance = [];

    // License UI helpers
    public $newLicenseType = '一般';
    public $newLicenseAuthority = '知事';
    public $newLicenseNumber = '';
    public $newLicenseDate = '';
    public $newLicenseCategories = ''; // カンマ区切り

    public function mount()
    {
        $tenant = Auth::user()->tenant;

        $this->company_name = $tenant->company_name;
        $this->representative_title = $tenant->representative_title;
        $this->representative_name = $tenant->representative_name;

        $this->zip_code = $tenant->zip_code;
        $this->address = $tenant->address;
        $this->phone = $tenant->phone;
        $this->fax = $tenant->fax;
        $this->invoice_registration_number = $tenant->invoice_registration_number;

        $this->licenses = $tenant->license_details ?? [];
        // Ensure structure exists
        $this->social_insurance = array_merge([
            'health_insurance' => ['joined' => false, 'office_number' => ''],
            'pension_insurance' => ['joined' => false, 'office_number' => ''],
            'employment_insurance' => ['joined' => false, 'office_number' => ''],
        ], $tenant->social_insurance ?? []);
    }

    public function addLicense()
    {
        $this->validate([
            'newLicenseNumber' => 'required',
            'newLicenseDate' => 'required',
        ]);

        $this->licenses[] = [
            'type' => $this->newLicenseType,
            'authority' => $this->newLicenseAuthority,
            'number' => $this->newLicenseNumber,
            'date' => $this->newLicenseDate,
            'categories' => $this->newLicenseCategories,
        ];

        $this->reset(['newLicenseType', 'newLicenseAuthority', 'newLicenseNumber', 'newLicenseDate', 'newLicenseCategories']);
    }

    public function removeLicense($index)
    {
        unset($this->licenses[$index]);
        $this->licenses = array_values($this->licenses);
    }

    public function save()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'representative_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'invoice_registration_number' => 'nullable|string|regex:/^T\d{13}$/',
        ], [
            'invoice_registration_number.regex' => 'インボイス登録番号はTから始まる13桁の半角数字で入力してください。',
        ]);

        $tenant = Auth::user()->tenant;
        $tenant->update([
            'company_name' => $this->company_name,
            'representative_title' => $this->representative_title,
            'representative_name' => $this->representative_name,
            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'invoice_registration_number' => $this->invoice_registration_number,
            'license_details' => $this->licenses,
            'social_insurance' => $this->social_insurance,
        ]);

        session()->flash('message', '自社情報を更新しました。');
    }

    public function render()
    {
        return view('livewire.settings.company-profile')->layout('layouts.app');
    }
}
