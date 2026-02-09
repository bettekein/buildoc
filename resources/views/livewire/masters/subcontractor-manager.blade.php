<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">下請業者マスター</h2>
                <p class="text-sm text-gray-500">施工体制台帳・再下請負通知書の作成に使用します</p>
            </div>
            <button wire:click="openModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                新規登録
            </button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('message') }}</div>
        @endif

        <!-- Search -->
        <div class="mb-4">
            <input type="text" wire:model.live="search" placeholder="会社名・住所で検索..."
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">会社名</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">代表者</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">住所</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">電話番号</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">建設業許可</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subcontractors as $sub)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $sub->name }}</div>
                                @if($sub->name_kana)
                                    <div class="text-xs text-gray-400">{{ $sub->name_kana }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sub->representative_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                {{ $sub->address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $sub->phone }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if(is_array($sub->construction_licenses))
                                    @foreach($sub->construction_licenses as $license)
                                        <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                                            {{ $license['type'] ?? '' }}
                                        </span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button wire:click="edit({{ $sub->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">編集</button>
                                <button wire:click="delete({{ $sub->id }})"
                                    onclick="confirm('削除しますか？') || event.stopImmediatePropagation()"
                                    class="text-red-600 hover:text-red-900">削除</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">下請業者が登録されていません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $subcontractors->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <div class="bg-white px-6 pt-5 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingId ? '下請業者編集' : '下請業者新規登録' }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- 会社名 -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">会社名 <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- 会社名カナ -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">会社名（カナ）</label>
                                <input type="text" wire:model="name_kana"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- 代表者 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">代表者役職</label>
                                <input type="text" wire:model="representative_title" placeholder="代表取締役"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">代表者氏名</label>
                                <input type="text" wire:model="representative_name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- 住所 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">郵便番号</label>
                                <input type="text" wire:model="postal_code" placeholder="123-4567"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">住所</label>
                                <input type="text" wire:model="address"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- 連絡先 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">電話番号</label>
                                <input type="text" wire:model="phone"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">FAX番号</label>
                                <input type="text" wire:model="fax"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                                <input type="email" wire:model="email"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- 安全衛生関連 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">安全衛生推進者</label>
                                <input type="text" wire:model="safety_officer"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">雇用管理責任者</label>
                                <input type="text" wire:model="employment_manager"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">主任技術者</label>
                                <input type="text" wire:model="chief_engineer"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <!-- 社会保険加入状況 -->
                        <div class="mt-6 border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">社会保険加入状況</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- 健康保険 -->
                                <div class="bg-gray-50 p-3 rounded">
                                    <h5 class="text-xs font-bold text-gray-600 mb-2">健康保険</h5>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" wire:model="socialInsurance.health_insurance.joined"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="ml-2 text-xs">加入</span>
                                    </div>
                                    <input type="text" wire:model="socialInsurance.health_insurance.office_number"
                                        placeholder="番号" class="w-full text-xs border-gray-300 rounded">
                                </div>
                                <!-- 厚生年金 -->
                                <div class="bg-gray-50 p-3 rounded">
                                    <h5 class="text-xs font-bold text-gray-600 mb-2">厚生年金保険</h5>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" wire:model="socialInsurance.pension_insurance.joined"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="ml-2 text-xs">加入</span>
                                    </div>
                                    <input type="text" wire:model="socialInsurance.pension_insurance.office_number"
                                        placeholder="番号" class="w-full text-xs border-gray-300 rounded">
                                </div>
                                <!-- 雇用保険 -->
                                <div class="bg-gray-50 p-3 rounded">
                                    <h5 class="text-xs font-bold text-gray-600 mb-2">雇用保険</h5>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" wire:model="socialInsurance.employment_insurance.joined"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="ml-2 text-xs">加入</span>
                                    </div>
                                    <input type="text" wire:model="socialInsurance.employment_insurance.office_number"
                                        placeholder="番号" class="w-full text-xs border-gray-300 rounded">
                                </div>
                            </div>
                        </div>

                        <!-- 建設業許可 -->
                        <div class="mt-6 border-t pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">建設業許可</h4>

                            <!-- 登録済み許可一覧 -->
                            @if(count($licenses) > 0)
                                <div class="mb-4 space-y-2">
                                    @foreach($licenses as $index => $license)
                                        <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                            <span class="text-sm">
                                                {{ $license['type'] }}工事業 / {{ $license['authority'] }} {{ $license['category'] }}
                                                第{{ $license['number'] }}号
                                                @if($license['date']) ({{ $license['date'] }}) @endif
                                            </span>
                                            <button wire:click="removeLicense({{ $index }})"
                                                class="text-red-500 hover:text-red-700 text-sm">削除</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- 許可追加フォーム -->
                            <div class="grid grid-cols-6 gap-2">
                                <div class="col-span-2">
                                    <input type="text" wire:model="newLicenseType" placeholder="許可業種（建築, 土木等）"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <select wire:model="newLicenseAuthority"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        <option value="知事">知事</option>
                                        <option value="大臣">大臣</option>
                                    </select>
                                </div>
                                <div>
                                    <select wire:model="newLicenseCategory"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        <option value="一般">一般</option>
                                        <option value="特定">特定</option>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" wire:model="newLicenseNumber" placeholder="許可番号"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <button wire:click="addLicense"
                                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded text-sm">追加</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="save"
                            class="w-full sm:w-auto sm:ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">
                            保存
                        </button>
                        <button wire:click="$set('showModal', false)"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">
                            キャンセル
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>