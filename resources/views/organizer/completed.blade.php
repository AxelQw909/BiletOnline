@extends('layouts.app')
@section('title', 'Завершенные концерты')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Завершенные мероприятия</h2>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        @forelse($concerts as $concert)
            <div class="bg-white p-6 rounded-3xl border border-zinc-200 shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-xl font-black text-zinc-900 mb-4">{{ $concert->title }}</h3>
                
                <div class="space-y-3 text-sm text-zinc-600 mb-6">
                    <div class="flex justify-between">
                        <span>Площадка:</span>
                        <span class="font-semibold text-zinc-900">{{ $concert->partner }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Зал:</span>
                        <span class="font-semibold text-zinc-900">{{ $concert->hall }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Дата:</span>
                        <span class="font-semibold text-zinc-900">{{ $concert->date }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-4 border-y border-zinc-100 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-black text-zinc-900">{{ $concert->sold }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-zinc-500">Продано</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-black text-zinc-900">{{ $concert->unsold }}</div>
                        <div class="text-[10px] uppercase tracking-widest text-zinc-500">Свободно</div>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <div class="text-xs uppercase text-zinc-400">Выручка</div>
                    <div class="text-3xl font-black text-zinc-900">{{ number_format($concert->revenue, 0, ',', ' ') }} ₽</div>
                </div>

                <a href="{{ route('organizer.export.pdf', $concert->id) }}" 
                   class="block w-full text-center bg-zinc-900 text-white font-bold py-3 rounded-xl hover:bg-black transition">
                    Скачать PDF отчет
                </a>
            </div>
        @empty
            <p class="col-span-full text-center text-zinc-500 py-20">Пока нет завершенных мероприятий.</p>
        @endforelse
    </div>
</div>
@endsection