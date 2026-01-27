@php
    // Calculate total project amount from helper or relation (simplified for now)
    $contractAmount = $project->quotationItems->sum('total_amount');
    $billedTotal = $billings->sum('amount_this_time');
    $remaining = $contractAmount - $billedTotal;
@endphp

<div class="p-6 bg-gray-50 min-h-screen font-sans">
    {{ Breadcrumbs::render('billings.index', $project) }}
    
    <div class="max-w-7xl mx-auto">
        <!-- Project Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $project->name }} - 請求管理</h1>
                <p class="text-sm text-gray-500 mt-1">顧客: {{ $project->customer->name }} | 契約金額: ¥{{ number_format($contractAmount) }}</p>
            </div>
            
            <div class="flex space-x-6 text-right">
                <div>
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">請求済累計</span>
                    <span class="text-xl font-bold text-blue-600">¥{{ number_format($billedTotal) }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">請求残高</span>
                    <span class="text-xl font-bold text-gray-600">¥{{ number_format($remaining) }}</span>
                </div>
            </div>
        </div>

        <!-- Billings List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-700">請求履歴</h2>
                <button wire:click="exportCsv" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg font-medium shadow-sm transition-colors mr-2">
                    CSV出力
                </button>
                <button wire:click="createNextBilling" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    次回請求を作成
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('billing_round')">
                            回
                            @if($sortField === 'billing_round') <span class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('billing_date')">
                            請求日
                             @if($sortField === 'billing_date') <span class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('amount_this_time')">
                            今回請求額
                            @if($sortField === 'amount_this_time') <span class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">出来高率</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($billings as $billing)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                第{{ $billing->billing_round }}回
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $billing->billing_date?->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-800">
                                ¥{{ number_format($billing->amount_this_time) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                {{ $billing->progress_rate }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $billing->status === 'paid' ? 'bg-green-100 text-green-800' : ($billing->status === 'billed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $billing->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('billings.edit', [$project, $billing]) }}" class="text-blue-600 hover:text-blue-900 mr-2">編集</a>
                                <a href="{{ route('billings.pdf', [$project, $billing]) }}" target="_blank" class="text-gray-600 hover:text-gray-900">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">まだ請求履歴がありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
