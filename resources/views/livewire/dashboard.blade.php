<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('dashboard') }}
        
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Dashboard</h2>
            <p class="text-gray-500">ようこそ、{{ auth()->user()->name }} さん</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Active Projects -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">進行中案件数</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $activeProjectsCount }}<span class="text-sm font-normal text-gray-400 ml-1">件</span></p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <!-- Monthly Billing -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">今月の請求予定</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">¥{{ number_format($monthlyBillingTotal) }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <!-- Unbilled Alert -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">今月の未請求額</p>
                    <p class="mt-1 text-3xl font-bold {{ $monthlyUnbilledTotal > 0 ? 'text-red-500' : 'text-gray-900' }}">¥{{ number_format($monthlyUnbilledTotal) }}</p>
                </div>
                <div class="p-3 {{ $monthlyUnbilledTotal > 0 ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-400' }} rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upcoming Billings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">請求期限の近い案件</h3>
                    <a href="{{ route('billings.index', ['project' => 1]) }}" class="text-sm text-blue-600 hover:text-blue-800">全て見る &rarr;</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($upcomingBillings as $billing)
                        <div class="p-4 hover:bg-gray-50 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $billing->project->name }}</p>
                                <p class="text-xs text-gray-500">{{ $billing->project->customer->name }} - 第{{ $billing->billing_round }}回</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">¥{{ number_format($billing->amount_this_time) }}</p>
                                <p class="text-xs {{ $billing->billing_date->isPast() ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                    {{ $billing->billing_date->format('Y/m/d') }}
                                </p>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('billings.edit', [$billing->project, $billing]) }}" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg text-sm">請求処理</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">直近の請求予定はありません</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">最近の案件</h3>
                    <a href="{{ route('projects.index') }}" class="text-sm text-blue-600 hover:text-blue-800">全て見る &rarr;</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($recentProjects as $project)
                        <div class="p-4 hover:bg-gray-50 transition-colors flex justify-between items-center">
                            <div>
                                <p class="text-sm font-bold text-gray-800">{{ $project->name }}</p>
                                <p class="text-xs text-gray-500">{{ $project->customer->name }}</p>
                            </div>
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($project->status == '受注') bg-green-100 text-green-800 
                                    @elseif($project->status == '施工中') bg-blue-100 text-blue-800
                                    @elseif($project->status == '完了') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $project->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
