<div class="p-6 bg-gray-50 min-h-screen font-sans">
    {{ Breadcrumbs::render('quotations.edit', $project) }}
    <!-- Header Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 sticky top-0 z-10 bg-opacity-95 backdrop-blur-sm">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $project->name }} 見積作成</h2>
                <p class="text-sm text-gray-500 mt-1">案件ID: {{ $project->id }} | 顧客: {{ $project->customer->name }}</p>
            </div>
            <div class="flex space-x-8 text-right">
                <div>
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">原価合計</span>
                    <span class="text-xl font-medium text-gray-600">¥{{ number_format($totalCost) }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">粗利率</span>
                    <span @class(['text-xl font-bold', 'text-green-600' => $profitMargin >= 20, 'text-yellow-500' => $profitMargin < 20 && $profitMargin > 10, 'text-red-500' => $profitMargin <= 10])>
                        {{ number_format($profitMargin, 1) }}%
                    </span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">見積総額 (税抜)</span>
                    <span class="text-3xl font-extrabold text-blue-600 tracking-tight">¥{{ number_format($grandTotal) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('quotations.pdf', $project) }}" target="_blank"
                class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2 px-6 rounded-lg shadow-sm flex items-center transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                PDF出力
            </a>
            <button wire:click="save" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-lg flex items-center transition-all transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                内容を保存
            </button>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm flex items-center animate-fade-in-down">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- Quotation Items -->
    <div class="space-y-6 pb-20">
        @foreach ($items as $index => $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-200 hover:shadow-md">
                <!-- Item Header (Accordion Trigger) -->
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center group cursor-pointer" 
                    wire:click="$toggle('items.{{ $index }}.expanded')">
                    <div class="flex items-center flex-1 space-x-4">
                        <button class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform {{ $item['expanded'] ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 w-1/2" wire:click.stop>
                            <span class="text-gray-400 text-sm font-mono text-xs">#{{ $index + 1 }}</span>
                            <input type="text" 
                                wire:model.blur="items.{{ $index }}.name" 
                                class="w-full bg-transparent border-b border-transparent hover:border-gray-300 focus:border-blue-500 focus:outline-none focus:ring-0 font-bold text-lg text-gray-800 placeholder-gray-300 transition-colors"
                                placeholder="大項目名を入力 (例: 仮設工事)">
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6" wire:click.stop>
                        <div class="text-right">
                            <span class="block text-xs text-gray-400">小計</span>
                            <span class="text-lg font-bold text-gray-700">¥{{ number_format($item['total_amount']) }}</span>
                        </div>
                        <button wire:click="removeItem({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors p-2 rounded-full hover:bg-red-50" title="削除">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Item Body (Details Table) -->
                @if($item['expanded'])
                <div class="p-4 bg-white animate-fade-in">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-xs text-gray-400 uppercase border-b border-gray-100">
                                    <th class="py-3 px-2 w-1/4 font-medium">名称</th>
                                    <th class="py-3 px-2 w-1/5 font-medium">規格・仕様</th>
                                    <th class="py-3 px-2 w-24 font-medium text-right">数量</th>
                                    <th class="py-3 px-2 w-20 font-medium text-center">単位</th>
                                    <th class="py-3 px-2 w-32 font-medium text-right">単価</th>
                                    <th class="py-3 px-2 w-32 font-medium text-right">原価</th>
                                    <th class="py-3 px-2 w-32 font-medium text-right">金額</th>
                                    <th class="py-3 px-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($item['details'] as $detailIndex => $detail)
                                    <tr class="group hover:bg-blue-50/30 transition-colors">
                                        <td class="p-2">
                                            <input type="text" class="w-full bg-gray-50 border-none rounded focus:ring-1 focus:ring-blue-500 text-gray-700 placeholder-gray-300 text-sm py-1.5 px-2" 
                                                wire:model.blur="items.{{ $index }}.details.{{ $detailIndex }}.name" placeholder="詳細名称">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" class="w-full bg-gray-50 border-none rounded focus:ring-1 focus:ring-blue-500 text-gray-600 placeholder-gray-300 text-sm py-1.5 px-2" 
                                                wire:model.blur="items.{{ $index }}.details.{{ $detailIndex }}.specification" placeholder="規格">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.01" class="w-full bg-white border border-gray-200 rounded text-right focus:ring-1 focus:ring-blue-500 text-gray-700 font-mono py-1.5 px-2" 
                                                wire:model.live="items.{{ $index }}.details.{{ $detailIndex }}.quantity">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" class="w-full bg-white border border-gray-200 rounded text-center focus:ring-1 focus:ring-blue-500 text-gray-600 py-1.5 px-2" 
                                                wire:model.blur="items.{{ $index }}.details.{{ $detailIndex }}.unit">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" class="w-full bg-white border border-gray-200 rounded text-right focus:ring-1 focus:ring-blue-500 text-gray-700 font-mono py-1.5 px-2" 
                                                wire:model.live="items.{{ $index }}.details.{{ $detailIndex }}.unit_price">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" class="w-full bg-white border border-gray-200 rounded text-right focus:ring-1 focus:ring-blue-500 text-gray-500 font-mono py-1.5 px-2" 
                                                wire:model.live="items.{{ $index }}.details.{{ $detailIndex }}.cost_price">
                                        </td>
                                        <td class="p-2 text-right">
                                            <span class="font-bold text-gray-800">¥{{ number_format($detail['total_price']) }}</span>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button wire:click="removeDetail({{ $index }}, {{ $detailIndex }})" class="text-gray-200 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-2 border-t border-dashed border-gray-200">
                        <button wire:click="addDetail({{ $index }})" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            内訳を追加
                        </button>
                    </div>
                </div>
                @endif
            </div>
        @endforeach

        <button wire:click="addItem" class="w-full py-4 border-2 border-dashed border-gray-300 rounded-xl text-gray-400 font-bold hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition-all">
            + 大項目を追加
        </button>
    </div>
</div>
