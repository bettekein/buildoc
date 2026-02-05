<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-5xl mx-auto">
        {{ Breadcrumbs::render('billings.edit', $project, $billing) }}
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">出来高請求書作成 (第{{ $billing->billing_round }}回)</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $project->name }} （契約金額: ¥{{ number_format($contract_amount) }}）</p>
            </div>
            <div>
                <a href="{{ route('billings.index', $project) }}" class="text-gray-500 hover:text-gray-700 underline text-sm">一覧に戻る</a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border-l-4 border-green-500">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Input Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">基本情報</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">請求書番号</label>
                            <input type="text" wire:model="billing_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('billing_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">請求日 <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="billing_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('billing_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">支払期限</label>
                            <input type="date" wire:model="payment_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $billing->status ?? '未請求' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Calculation Inputs -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">出来高計算</h2>
                    
                    <div class="space-y-6">
                        <!-- Progress Input -->
                        <div class="grid grid-cols-2 gap-6 items-center bg-blue-50 p-4 rounded-lg">
                            <div>
                                <label class="block text-sm font-bold text-blue-900 mb-1">今回の累計進捗率 (%)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" wire:model.live.debounce.500ms="progress_rate" class="block w-full rounded-md border-blue-300 pl-3 pr-8 focus:border-blue-500 focus:ring-blue-500 text-right font-bold text-lg">
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">前回までの累計: {{ number_format($previous_billed_amount) }}</p>
                            </div>
                            
                            <div class="text-center text-gray-400 font-bold">OR</div>

                            <div>
                                <label class="block text-sm font-bold text-blue-900 mb-1">今回出来高額 (税抜)</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" wire:model.live.debounce.500ms="amount_this_time" class="block w-full rounded-md border-blue-300 pl-7 pr-3 focus:border-blue-500 focus:ring-blue-500 text-right font-bold text-lg">
                                </div>
                                @error('amount_this_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Adjustments -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">保留金解除額 (今回戻入)</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" wire:model.live.debounce.500ms="retention_release_amount" class="block w-full rounded-md border-gray-300 pl-7 pr-3 text-right">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">相殺金・協力会費</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" wire:model.live.debounce.500ms="offset_amount" class="block w-full rounded-md border-gray-300 pl-7 pr-3 text-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary Preview -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">計算結果プレビュー</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">契約金額</span>
                            <span class="font-medium">¥{{ number_format($contract_amount) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">累計出来高 ({{ $progress_rate }}%)</span>
                            <span class="font-medium">¥{{ number_format($cumulative_amount) }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">前回迄出来高</span>
                            <span class="font-medium">- ¥{{ number_format($previous_billed_amount) }}</span>
                        </div>
                        
                        <div class="flex justify-between pt-2">
                            <span class="font-bold text-gray-800">今回出来高 (税抜)</span>
                            <span class="font-bold text-lg">¥{{ number_format($amount_this_time) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">消費税 (10%)</span>
                            <span>¥{{ number_format($tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-bold text-gray-800">今回合計 (税込)</span>
                            <span class="font-bold">¥{{ number_format($gross_billing_amount) }}</span>
                        </div>

                        <div class="flex justify-between text-red-600">
                            <span>今回保留金 ({{ $retention_rate }}%)</span>
                            <span>- ¥{{ number_format($current_retention_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>保留金解除</span>
                            <span>+ ¥{{ number_format($retention_release_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600 border-b pb-2">
                            <span>相殺金</span>
                            <span>- ¥{{ number_format($offset_amount) }}</span>
                        </div>

                        <div class="flex justify-between pt-4 items-center">
                            <span class="font-bold text-xl text-gray-900">請求金額</span>
                            <span class="font-extrabold text-2xl text-blue-600">¥{{ number_format($final_billing_amount) }}</span>
                        </div>
                        
                        <!-- Tax Summary for Invoice -->
                        <div class="mt-6 pt-4 border-t text-xs text-gray-500">
                            <p class="font-bold mb-1">インボイス集計:</p>
                            <div class="flex justify-between">
                                <span>10%対象</span>
                                <span>¥{{ number_format($gross_billing_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>消費税(10%)</span>
                                <span>¥{{ number_format($tax_amount) }}</span>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <button wire:click="save" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition">
                                保存して更新
                            </button>
                            @if($billing->exists)
                            <a href="{{ route('billings.pdf', [$project, $billing]) }}" target="_blank" class="block w-full bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-lg shadow-md text-center transition">
                                PDFプレビュー
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
