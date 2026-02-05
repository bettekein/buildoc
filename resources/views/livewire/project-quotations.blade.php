<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">
        {{ Breadcrumbs::render('projects.index') }}
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">案件一覧</h2>
            <!-- Search and Actions -->
            <div class="flex items-center space-x-4">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="案件名・顧客名で検索..."
                    class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">

                <label class="flex items-center space-x-2 text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" wire:model.live="showTrashed"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>ゴミ箱を表示</span>
                </label>
                <button wire:click="exportCsv"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors mr-2">
                    CSV出力
                </button>
                <button wire:click="create"
                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + 新規案件
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700"
                            wire:click="sortBy('name')">
                            案件名
                            @if($sortField === 'name') <span
                            class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">顧客
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700"
                            wire:click="sortBy('period_start')">
                            工期
                            @if($sortField === 'period_start') <span
                            class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700"
                            wire:click="sortBy('status')">
                            ステータス
                            @if($sortField === 'status') <span
                            class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            アクション</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $project->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $project->customer->name ?? '（顧客なし）' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $project->period_start?->format('Y/m/d') }} -
                                    {{ $project->period_end?->format('Y/m/d') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $project->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @if($showTrashed)
                                    <button wire:click="restore({{ $project->id }})"
                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-colors">
                                        復元
                                    </button>
                                    <button wire:click="forceDelete({{ $project->id }})"
                                        onclick="confirm('本当に削除しますか？') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">
                                        完全削除
                                    </button>
                                @else
                                    <a href="{{ route('quotations.edit', $project) }}"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors">
                                        見積
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors">
                                        編集
                                    </a>
                                    {{-- <a href="{{ route('billings.index', $project) }}"
                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition-colors">
                                        請求
                                    </a> --}}
                                    <!-- Add Delete Button for active items? -->
                                    <button wire:click="delete({{ $project->id }})"
                                        onclick="confirm('ゴミ箱に移動しますか？') || event.stopImmediatePropagation()"
                                        class="text-gray-400 hover:text-red-600 ml-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                案件が登録されていません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>