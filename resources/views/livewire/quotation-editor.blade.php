<div class="p-4" x-data>
    <!-- Header: Project Stats -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 sticky top-0 z-10 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">見積書編集: {{ $project->name }}</h2>
            </div>
            <div class="flex space-x-6 text-sm">
                <div class="text-right">
                    <span class="block text-gray-500">受注合計額</span>
                    <span class="text-2xl font-bold text-gray-800">¥{{ number_format($totalOrderAmount) }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-gray-500">想定原価合計</span>
                    <span class="text-xl font-bold text-gray-600">¥{{ number_format($totalEstimatedCost) }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-gray-500">想定利益 / 利益率</span>
                    <div class="{{ $totalProfitRate < 10 ? 'text-red-600' : 'text-green-600' }}">
                        <span class="text-xl font-bold">¥{{ number_format($totalProfit) }}</span>
                        <span class="text-lg font-bold ml-1">({{ number_format($totalProfitRate, 1) }}%)</span>
                    </div>
                </div>
            </div>
            <div>
                <button wire:click="save" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                    保存する
                </button>
            </div>
        </div>
        
        @if (session()->has('message'))
            <div class="mt-2 text-green-600 font-medium">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- Items List -->
    <div class="space-y-6">
        @foreach($items as $itemIndex => $item)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <!-- Item Header (Category) -->
                <div class="bg-gray-100 p-3 flex items-center justify-between border-b border-gray-200">
                    <div class="flex items-center space-x-2 w-full">
                        <span class="cursor-move text-gray-400">
                            <!-- Drag Handle Icon Placeholder -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        </span>
                        
                        <!-- Order Controls for fallback -->
                        <div class="flex flex-col space-y-0.5">
                             <button type="button" wire:click="moveItemUp({{ $itemIndex }})" class="text-gray-500 hover:text-blue-500 text-xs">▲</button>
                             <button type="button" wire:click="moveItemDown({{ $itemIndex }})" class="text-gray-500 hover:text-blue-500 text-xs">▼</button>
                        </div>

                        <div class="flex-1">
                            <input type="text" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.name" placeholder="工種名 (例: 仮設工事)" class="w-full bg-transparent border-none text-lg font-bold focus:ring-0 placeholder-gray-400">
                        </div>
                        <div class="text-right px-4">
                            <span class="text-sm font-bold text-gray-600">小計: ¥{{ number_format(collect($item['details'])->sum('total_price')) }}</span>
                        </div>
                        <button wire:click="removeItem({{ $itemIndex }})" class="text-red-400 hover:text-red-600 p-1" onclick="confirm('この工種を削除しますか？') || event.stopImmediatePropagation()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="p-2">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pl-2 w-10"></th>
                                <th class="w-1/4">名称 / 仕様</th>
                                <th class="w-20 text-center">数量</th>
                                <th class="w-20 text-center">単位</th>
                                <th class="w-28 text-right">単価</th>
                                <th class="w-28 text-right">金額</th>
                                <th class="w-24 text-right">原価</th>
                                <th class="w-32 text-center">粗利 / 利益率</th>
                                <th class="w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($item['details'] as $detailIndex => $detail)
                                @php
                                    $qty = (float)($detail['quantity'] ?? 0);
                                    $price = (float)($detail['unit_price'] ?? 0);
                                    $cost = (float)($detail['cost_price'] ?? 0);
                                    $amount = $qty * $price;
                                    $profit = $amount - ($qty * $cost);
                                    $profitRate = $amount > 0 ? ($profit / $amount) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="pl-2 text-center text-gray-300">
                                         <div class="flex flex-col items-center space-y-0.5">
                                             <button type="button" wire:click="moveDetailUp({{ $itemIndex }}, {{ $detailIndex }})" class="hover:text-blue-500 text-xs leading-none">▲</button>
                                             <button type="button" wire:click="moveDetailDown({{ $itemIndex }}, {{ $detailIndex }})" class="hover:text-blue-500 text-xs leading-none">▼</button>
                                         </div>
                                    </td>
                                    <td class="py-1">
                                        <input type="text" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.name" placeholder="名称" class="w-full border-gray-200 rounded-sm text-sm py-1 px-2 focus:ring-blue-500 mb-1">
                                        <input type="text" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.specification" placeholder="仕様" class="w-full border-gray-100 rounded-sm text-xs py-0.5 px-2 text-gray-500 focus:ring-blue-500 bg-gray-50">
                                    </td>
                                    <td class="py-1">
                                        <input type="number" step="0.01" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.quantity" class="w-full text-center border-gray-200 rounded-sm text-sm py-1 px-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-1">
                                        <input type="text" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.unit" list="units" class="w-full text-center border-gray-200 rounded-sm text-sm py-1 px-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-1">
                                        <input type="number" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.unit_price" class="w-full text-right border-gray-200 rounded-sm text-sm py-1 px-1 focus:ring-blue-500">
                                    </td>
                                    <td class="py-1 text-right font-medium text-gray-800">
                                        ¥{{ number_format($amount) }}
                                    </td>
                                    <td class="py-1">
                                        <input type="number" wire:model.live.debounce.500ms="items.{{ $itemIndex }}.details.{{ $detailIndex }}.cost_price" class="w-full text-right border-gray-200 rounded-sm text-sm py-1 px-1 focus:ring-blue-500 bg-red-50">
                                    </td>
                                    <td class="py-1 text-center">
                                        <div class="text-xs">¥{{ number_format($profit) }}</div>
                                        <div class="font-bold {{ $profitRate < 10 ? 'text-red-500' : 'text-green-600' }}">
                                            {{ number_format($profitRate, 1) }}%
                                        </div>
                                    </td>
                                    <td class="py-1 text-center">
                                       <button wire:click="removeDetail({{ $itemIndex }}, {{ $detailIndex }})" class="text-gray-300 hover:text-red-500">
                                            ×
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2 text-center">
                        <button wire:click="addDetail({{ $itemIndex }})" class="text-blue-500 hover:text-blue-700 text-sm font-medium py-1 px-3 border border-blue-200 rounded-full hover:bg-blue-50 transition">
                            + 明細を追加
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bottom Actions -->
    <div class="mt-8 text-center pb-20">
        <button wire:click="addItem" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-8 rounded-lg shadow-lg">
            + 大項目（工種）を追加
        </button>
    </div>

    <!-- Datalist for Units -->
    <datalist id="units">
        <option value="式">
        <option value="m">
        <option value="m2">
        <option value="m3">
        <option value="個">
        <option value="本">
        <option value="kg">
        <option value="t">
        <option value="日">
        <option value="人工">
        <option value="回">
    </datalist>
</div>
