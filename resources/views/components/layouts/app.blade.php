<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Buildoc Construction Cloud' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen">
            <!-- Navigation (Simple for now) -->
            <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
                <div class="flex items-center space-x-6">
                    <a href="/projects" class="text-xl font-extrabold text-blue-600 tracking-tighter">
                        Buildoc <span class="text-gray-400 text-xs font-normal">Cloud</span>
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="/projects" class="text-sm font-medium text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md hover:bg-blue-50 transition-colors">案件一覧</a>
                        <a href="#" class="text-sm font-medium text-gray-400 hover:text-gray-600 px-3 py-2">請求管理</a>
                        <a href="#" class="text-sm font-medium text-gray-400 hover:text-gray-600 px-3 py-2">安全書類</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="text-sm text-gray-500 mr-4">
                            {{ auth()->user()->name }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors">
                                ログアウト
                            </button>
                        </form>
                    @else
                        <div class="text-sm text-gray-500">Guest</div>
                    @endauth
                </div>
            </nav>

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
