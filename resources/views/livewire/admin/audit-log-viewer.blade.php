<div class="p-6 bg-gray-50 min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">監査ログ (Audit Logs)</h2>
            <div class="flex items-center space-x-4">
                <select wire:model.live="eventFilter"
                    class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">全てのイベント</option>
                    <option value="created">作成 (created)</option>
                    <option value="updated">更新 (updated)</option>
                    <option value="deleted">削除 (deleted)</option>
                    <option value="restored">復元 (restored)</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="キーワード検索..."
                    class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                日時</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作ユーザー</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                イベント</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                対象モデル</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                                変更内容</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm">
                        @forelse ($audits as $audit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ $audit->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $audit->user->name ?? 'System/Unknown' }}
                                    <span class="text-xs text-gray-400 block">{{ $audit->ip_address }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $audit->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $audit->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $audit->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $audit->event === 'restored' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($audit->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ class_basename($audit->auditable_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ $audit->auditable_id }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 break-words text-xs font-mono">
                                    <div class="max-h-32 overflow-y-auto">
                                        @if($audit->event === 'updated')
                                            @foreach($audit->getModified() as $attribute => $modified)
                                                <div class="mb-1">
                                                    <strong>{{ $attribute }}:</strong>
                                                    <span
                                                        class="text-red-600 border-b border-red-200 bg-red-50 px-1">{{ $modified['old'] ?? 'null' }}</span>
                                                    &rarr;
                                                    <span
                                                        class="text-green-600 border-b border-green-200 bg-green-50 px-1">{{ $modified['new'] ?? 'null' }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            {{ Str::limit(json_encode($audit->new_values, JSON_UNESCAPED_UNICODE), 200) }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">ログが見つかりません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $audits->links() }}
            </div>
        </div>
    </div>
</div>