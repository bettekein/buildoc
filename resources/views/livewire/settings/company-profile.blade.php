<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('自社情報設定') }}
            </h2>
        </x-slot>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form wire:submit.prevent="save">
                <!-- 基本情報 -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">基本情報</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">商号または名称</label>
                            <input type="text" wire:model="company_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">インボイス登録番号</label>
                            <input type="text" wire:model="invoice_registration_number" placeholder="T1234567890123" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">代表者役職</label>
                            <input type="text" wire:model="representative_title" placeholder="代表取締役" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">代表者氏名</label>
                            <input type="text" wire:model="representative_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                             @error('representative_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">郵便番号</label>
                            <input type="text" wire:model="zip_code" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">住所</label>
                            <input type="text" wire:model="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">電話番号</label>
                            <input type="text" wire:model="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">FAX番号</label>
                            <input type="text" wire:model="fax" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- 建設業許可 -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">建設業許可</h3>
                    
                    <div class="space-y-4 mb-4">
                        @foreach($licenses as $index => $license)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border">
                                <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <span class="text-xs text-gray-500 block">区分・許可</span>
                                        <span class="text-sm font-medium">{{ $license['type'] }}・{{ $license['authority'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">許可番号</span>
                                        <span class="text-sm font-medium">第{{ $license['number'] }}号</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">許可年月日</span>
                                        <span class="text-sm font-medium">{{ $license['date'] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">建設業の種類</span>
                                        <span class="text-sm font-medium">{{ $license['categories'] }}</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeLicense({{ $index }})" class="text-red-500 hover:text-red-700">削除</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 border rounded-lg bg-blue-50">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">新規追加</h4>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">区分</label>
                                <select wire:model="newLicenseType" class="block w-full border-gray-300 rounded-md text-sm">
                                    <option value="一般">一般</option>
                                    <option value="特定">特定</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">許可</label>
                                <select wire:model="newLicenseAuthority" class="block w-full border-gray-300 rounded-md text-sm">
                                    <option value="知事">知事</option>
                                    <option value="大臣">大臣</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">許可番号（数字のみ）</label>
                                <input type="text" wire:model="newLicenseNumber" class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">許可年月日</label>
                                <input type="date" wire:model="newLicenseDate" class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">種類（カンマ区切り）</label>
                                <input type="text" wire:model="newLicenseCategories" placeholder="土,建,大,左..." class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                             <button type="button" wire:click="addLicense" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">追加</button>
                        </div>
                    </div>
                </div>

                <!-- 社会保険加入状況 -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">社会保険加入状況</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- 健康保険 -->
                        <div class="p-4 border rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-800">健康保険</h4>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="social_insurance.health_insurance.joined" id="health_joined" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="health_joined" class="ml-2 text-sm text-gray-700">加入している</label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500">営業所整理記号及び被保険者整理番号</label>
                                <input type="text" wire:model="social_insurance.health_insurance.office_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="例: 01-イロハ-12345">
                            </div>
                        </div>

                        <!-- 厚生年金保険 -->
                        <div class="p-4 border rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-800">厚生年金保険</h4>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="social_insurance.pension_insurance.joined" id="pension_joined" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="pension_joined" class="ml-2 text-sm text-gray-700">加入している</label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500">営業所整理記号及び被保険者整理番号</label>
                                <input type="text" wire:model="social_insurance.pension_insurance.office_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="例: 01-イロハ-12345">
                            </div>
                        </div>

                        <!-- 雇用保険 -->
                        <div class="p-4 border rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-800">雇用保険</h4>
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="social_insurance.employment_insurance.joined" id="employment_joined" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="employment_joined" class="ml-2 text-sm text-gray-700">加入している</label>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="block text-xs text-gray-500">労働保険番号</label>
                                <input type="text" wire:model="social_insurance.employment_insurance.office_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="例: 12-1-12-123456-123">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    @if (session()->has('message'))
                        <div class="mr-4 text-green-600 font-medium text-sm">
                            {{ session('message') }}
                        </div>
                    @endif
                    <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-medium">
                        保存する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
