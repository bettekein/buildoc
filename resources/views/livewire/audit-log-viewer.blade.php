    <div class="max-w-7xl mx-auto">
        {{ Breadcrumbs::render('audits.index') }}
        
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">操作ログ (監査)</h2>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">イベントタイプ</label>
                <select wire:model.live="filterEvent" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">全て</option>
                    <option value="created">作成 (Created)</option>
                    <option value="updated">更新 (Updated)</option>
                    <option value="deleted">削除 (Deleted)</option>
                    <option value="restored">復元 (Restored)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">モデル検索</label>
                <input type="text" wire:model.live="filterModel" placeholder="Project, Customer..." class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('created_at')">
                            日時
                            @if($sortField === 'created_at') <span class="pl-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ユーザー</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">イベント</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">対象モデル</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">変更詳細</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($audits as $audit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $audit->created_at->format('Y/m/d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $audit->user->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                    @if($audit->event == 'created') bg-green-100 text-green-800
                                    @elseif($audit->event == 'updated') bg-blue-100 text-blue-800
                                    @elseif($audit->event == 'deleted') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($audit->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ class_basename($audit->auditable_type) }}</span>
                                <span class="text-xs ml-1">#{{ $audit->auditable_id }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <details class="group">
                                    <summary class="cursor-pointer text-blue-600 hover:text-blue-800 text-xs font-medium select-none">
                                        詳細を表示
                                    </summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded border border-gray-200 text-xs font-mono overflow-auto max-h-48 max-w-lg">
                                        @foreach($audit->getModified() as $attribute => $modified)
                                            <div class="mb-1">
                                                <span class="font-bold text-gray-700">{{ $attribute }}:</span>
                                                @if(isset($modified['old']))
                                                    <span class="text-red-500 line-through">{{ Str::limit((string)$modified['old'], 50) }}</span> ->
                                                @endif
                                                <span class="text-green-600">{{ Str::limit((string)$modified['new'], 50) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">ログが見つかりませんでした。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $audits->links() }}
            </div>
        </div>
    </div>
</div>
