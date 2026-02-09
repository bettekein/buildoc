<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">
            工種マスター管理
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Section -->
            <div class="md:col-span-1">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editingId ? '編集' : '新規登録' }}</h3>

                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">工種名</label>
                            <input type="text" wire:model="name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="例: 仮設工事">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">表示順</label>
                            <input type="number" wire:model="sort_order"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('sort_order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            @if($editingId)
                                <button type="button" wire:click="cancel"
                                    class="mr-3 text-gray-600 hover:text-gray-800 text-sm">キャンセル</button>
                            @endif

                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm shadow-sm">
                                {{ $editingId ? '更新' : '登録' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Section -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4">
                        <input type="text" wire:model.live="search" placeholder="検索..."
                            class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    @if (session()->has('message'))
                        <div class="mb-4 text-green-600 text-sm font-medium">{{ session('message') }}</div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    順序</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    工種名</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->sort_order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $category->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 mr-2">編集</button>
                                        <button wire:click="delete({{ $category->id }})"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="confirm('本当に削除しますか？') || event.stopImmediatePropagation()">削除</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">工種が登録されていません</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>