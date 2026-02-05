<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:text-blue-900">&larr;
                        案件一覧に戻る</a>
                    <h2 class="text-2xl font-bold text-gray-800 mt-2">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500">顧客: {{ $project->customer->name ?? '未設定' }} ｜ 工期:
                        {{ $project->period_start?->format('Y/m/d') }} - {{ $project->period_end?->format('Y/m/d') }}
                    </p>
                </div>
                <!-- Green File Export Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        グリーンファイル出力
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20">
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">労務安全関係
                            </div>
                            <a href="{{ route('greenfile.worker-roster', $project) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                📋 作業員名簿
                            </a>
                            <a href="{{ route('greenfile.social-insurance', $project) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🏥 社会保険加入状況
                            </a>
                            <a href="{{ route('greenfile.new-worker-survey', $project) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                📝 新規入場者調査票
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">機械・車両関係
                            </div>
                            <a href="{{ route('greenfile.vehicle-machinery', $project) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🚗 持込機械届（車両）
                            </a>
                            <a href="{{ route('greenfile.tool-equipment', $project) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🔧 持込機械届（工具）
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
        @endif

        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'staff')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'staff' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    スタッフ配置 ({{ $assignedStaff->count() }})
                </button>
                <button wire:click="$set('activeTab', 'vehicles')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'vehicles' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    車両配置 ({{ $assignedVehicles->count() }})
                </button>
                <button wire:click="$set('activeTab', 'tools')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'tools' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    工具配置 ({{ $assignedTools->count() }})
                </button>
                <button wire:click="$set('activeTab', 'subcontractors')"
                    class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'subcontractors' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    下請業者配置 ({{ $assignedSubcontractors->count() }})
                </button>
            </nav>
        </div>

        <!-- Staff Tab -->
        @if($activeTab === 'staff')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">配置スタッフ一覧</h3>
                    <button wire:click="openStaffModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+
                        スタッフ追加</button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">氏名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">職種</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">役割</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">職長</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">期間</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignedStaff as $staff)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $staff->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $staff->job_type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $staff->pivot->role ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button wire:click="toggleForeman({{ $staff->id }})"
                                        class="px-2 py-1 rounded text-xs font-semibold {{ $staff->pivot->is_foreman ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $staff->pivot->is_foreman ? '職長' : '-' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $staff->pivot->start_date ?? '-' }} ～ {{ $staff->pivot->end_date ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button wire:click="removeStaff({{ $staff->id }})"
                                        onclick="confirm('配置を解除しますか？') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-900">解除</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">スタッフが配置されていません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Vehicles Tab -->
        @if($activeTab === 'vehicles')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">配置車両一覧</h3>
                    <button wire:click="openVehicleModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+
                        車両追加</button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">車両名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ナンバー</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">車検満了日</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">使用期間</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignedVehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vehicle->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vehicle->plate_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vehicle->inspection_expiry?->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vehicle->pivot->start_date ?? '-' }} ～ {{ $vehicle->pivot->end_date ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button wire:click="removeVehicle({{ $vehicle->id }})"
                                        onclick="confirm('配置を解除しますか？') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-900">解除</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">車両が配置されていません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Tools Tab -->
        @if($activeTab === 'tools')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">配置工具一覧</h3>
                    <button wire:click="openToolModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+
                        工具追加</button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">工具名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">管理番号</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">最終点検日</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">使用期間</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignedTools as $tool)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tool->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->management_no }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tool->last_inspection_date?->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tool->pivot->start_date ?? '-' }} ～ {{ $tool->pivot->end_date ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button wire:click="removeTool({{ $tool->id }})"
                                        onclick="confirm('配置を解除しますか？') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-900">解除</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">工具が配置されていません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Subcontractors Tab -->
        @if($activeTab === 'subcontractors')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">配置下請業者一覧</h3>
                    <button wire:click="openSubcontractorModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+
                        下請業者追加</button>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">階層</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">会社名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">担当工事</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">安全衛生責任者</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">契約日</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignedSubcontractors as $sub)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-600">
                                        {{ $sub->pivot->tier }}次
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sub->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->pivot->work_content ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->pivot->safety_manager ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sub->pivot->contract_date ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button wire:click="removeSubcontractor({{ $sub->id }}, {{ $sub->pivot->tier }})"
                                        onclick="confirm('配置を解除しますか？') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-900">解除</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">下請業者が配置されていません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Staff Modal -->
    @if($showStaffModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showStaffModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">スタッフ追加</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">スタッフ選択</label>
                                <select wire:model="selectedStaffId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">選択してください</option>
                                    @foreach($availableStaff as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->job_type }})</option>
                                    @endforeach
                                </select>
                                @error('selectedStaffId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">役割・担当業務</label>
                                <input type="text" wire:model="staffRole"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="staffIsForeman" id="isForeman"
                                    class="rounded border-gray-300 text-blue-600">
                                <label for="isForeman" class="ml-2 text-sm text-gray-700">職長として配置</label>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">入場開始日</label>
                                    <input type="date" wire:model="staffStartDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">入場終了日</label>
                                    <input type="date" wire:model="staffEndDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="addStaff"
                            class="w-full sm:w-auto sm:ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">追加</button>
                        <button wire:click="$set('showStaffModal', false)"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Vehicle Modal -->
    @if($showVehicleModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showVehicleModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">車両追加</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">車両選択</label>
                                <select wire:model="selectedVehicleId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">選択してください</option>
                                    @foreach($availableVehicles as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->plate_number }})</option>
                                    @endforeach
                                </select>
                                @error('selectedVehicleId') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">使用開始日</label>
                                    <input type="date" wire:model="vehicleStartDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">使用終了日</label>
                                    <input type="date" wire:model="vehicleEndDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="addVehicle"
                            class="w-full sm:w-auto sm:ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">追加</button>
                        <button wire:click="$set('showVehicleModal', false)"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tool Modal -->
    @if($showToolModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showToolModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">工具追加</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">工具選択</label>
                                <select wire:model="selectedToolId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">選択してください</option>
                                    @foreach($availableTools as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->management_no }})</option>
                                    @endforeach
                                </select>
                                @error('selectedToolId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">使用開始日</label>
                                    <input type="date" wire:model="toolStartDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">使用終了日</label>
                                    <input type="date" wire:model="toolEndDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="addTool"
                            class="w-full sm:w-auto sm:ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">追加</button>
                        <button wire:click="$set('showToolModal', false)"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Subcontractor Modal -->
    @if($showSubcontractorModal)
        <div class="fixed z-10 inset-0 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="$set('showSubcontractorModal', false)">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">下請業者追加</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">階層</label>
                                <select wire:model.live="subcontractorTier"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1">一次下請</option>
                                    <option value="2">二次下請</option>
                                    <option value="3">三次下請</option>
                                </select>
                            </div>

                            @if($subcontractorTier > 1)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">直上の下請業者</label>
                                    <select wire:model="selectedParentSubcontractorId"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">選択してください</option>
                                        @foreach($assignedSubcontractors->where('pivot.tier', $subcontractorTier - 1) as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700">下請業者選択</label>
                                <select wire:model="selectedSubcontractorId"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">選択してください</option>
                                    @foreach($availableSubcontractors as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSubcontractorId') <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">担当工事内容</label>
                                <input type="text" wire:model="subcontractorWorkContent"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">契約日</label>
                                    <input type="date" wire:model="subcontractorContractDate"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">契約金額</label>
                                    <input type="number" wire:model="subcontractorContractAmount"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">安全衛生責任者</label>
                                    <input type="text" wire:model="subcontractorSafetyManager"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">主任技術者</label>
                                    <input type="text" wire:model="subcontractorChiefEngineer"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                        <button wire:click="addSubcontractor"
                            class="w-full sm:w-auto sm:ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white font-medium hover:bg-blue-700">追加</button>
                        <button wire:click="$set('showSubcontractorModal', false)"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>