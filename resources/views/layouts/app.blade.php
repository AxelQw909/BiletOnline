<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BiletOnline')</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        /* Кастомный минималистичный скроллбар */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f4f4f5; }
        ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 3px; }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex flex-col min-h-screen antialiased">

    <header class="bg-white border-b border-zinc-200 w-full sticky top-0 z-50">
        <div class="w-full px-4 sm:px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <a href="#" class="text-xl font-bold tracking-tight text-zinc-900">BILET<span class="text-zinc-400 font-normal">ONLINE</span></a>
            
            <nav class="flex flex-wrap justify-center items-center gap-6 text-sm font-medium text-zinc-600">
                @auth
                    @if(Auth::user()->role === 'partner')
                        <a href="{{ route('partner.profile') }}" class="hover:text-zinc-950 transition">Мой профиль</a>
                        <a href="{{ route('partner.requests') }}" class="hover:text-zinc-950 transition">Заявки</a>
                    @else
                        <a href="{{ route('organizer.profile') }}" class="hover:text-zinc-950 transition">Мой профиль</a>
                        <a href="{{ route('organizer.platforms') }}" class="hover:text-zinc-950 transition">Площадки</a>
                        <a href="{{ route('organizer.concerts') }}" class="hover:text-zinc-950 transition">Будущие концерты</a>
                        <a href="{{ route('organizer.tickets.search') }}" class="hover:text-zinc-950 transition">Поиск билета</a>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" class="text-zinc-400 hover:text-red-600 transition cursor-pointer font-medium">Выйти</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-grow w-full px-4 sm:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-zinc-900 text-white text-sm rounded-none flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-zinc-400 hover:text-white cursor-pointer">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-zinc-200 py-6 text-center text-xs text-zinc-400 w-full mt-auto">
        &copy; {{ date('Y') }} BiletOnline. Минимализм и функциональность.
    </footer>

</body>
</html>