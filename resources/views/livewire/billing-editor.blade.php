<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-4xl mx-auto">
        {{ Breadcrumbs::render('billings.edit', $project, $billing) }}
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">請求書編集 (第{{ $billing->billing_round }}回)</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $project->name }}</p>
            </div>
            <div>
                <a href="{{ route('billings.index', $project) }}" class="text-gray-500 hover:text-gray-700 underline text-sm">一覧に戻る</a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 space-y-8">
                <!-- Billing Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">請求書番号</label>
                        <input type="text" wire:model="billing_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
                        <select wire:model="billing.status" disabled class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-gray-500">
                            <option value="unbilled">未請求</option>
                            <option value="billed">請求済</option>
                            <option value="paid">入金済</option>
                        </select>
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
                </div>

                <hr class="border-gray-100">

                <!-- Amount Calculation -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">請求金額計算</h3>
                    <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <label class="font-medium text-gray-700">今回請求額 (税抜)</label>
                            <div class="w-1/3">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" wire:model.live="amount_this_time" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-blue-500 focus:ring-blue-500 text-right font-bold text-lg">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <label class="font-medium text-gray-700">消費税 (10%)</label>
                            <div class="w-1/3">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" wire:model="tax_amount" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-blue-500 focus:ring-blue-500 text-right bg-white">
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <label class="font-bold text-gray-900 text-lg">請求合計 (税込)</label>
                            <div class="text-2xl font-extrabold text-blue-600">
                                ¥{{ number_format((float)$amount_this_time + (float)$tax_amount) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-right">
                    <button wire:click="save" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:-translate-y-0.5">
                        保存する
                    </button>
                    
                    <a href="{{ route('billings.pdf', [$project, $billing]) }}" target="_blank" class="ml-4 bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition hover:-translate-y-0.5 inline-block text-center">
                        PDFダウンロード
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
