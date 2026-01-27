@unless ($breadcrumbs->isEmpty())
    <nav class="w-full mb-4">
        <ol class="flex items-center space-x-2 text-xs text-gray-500">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <a href="{{ $breadcrumb->url }}" class="hover:text-gray-700 hover:underline transition-colors">
                            {{ $breadcrumb->title }}
                        </a>
                    </li>
                @else
                    <li class="font-medium text-gray-700 truncate max-w-xs">
                        {{ $breadcrumb->title }}
                    </li>
                @endif

                @unless($loop->last)
                    <li class="text-gray-400">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </li>
                @endif

            @endforeach
        </ol>
    </nav>
@endunless
