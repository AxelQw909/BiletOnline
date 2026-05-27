@extends('layouts.app')
@section('title', 'Будущие концерты — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Будущие концерты</h2>
    </header>

    @if($concerts->isEmpty())
        <div class="text-center py-20 border-2 border-dashed border-zinc-200 rounded-3xl text-zinc-400 text-sm">
            У вас пока нет одобренных концертов.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($concerts as $concert)
                @php
                    $tickets = $concert->tickets ?? collect();
                    $soldTickets = $tickets->where('status', 'paid')->count();
                    $totalEarnings = $tickets->where('status', 'paid')->sum('price');
                    $totalPlaces = $concert->hall->capacity ?? 0;
                @endphp

                <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <h3 class="text-xl font-bold text-zinc-900 mb-6 uppercase tracking-tight">{{ $concert->title }}</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-sm border-b border-zinc-50 pb-2">
                                <span class="text-zinc-400 font-medium">Зал</span>
                                <span class="font-bold text-zinc-900">{{ $concert->hall->name ?? 'Не указан' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-zinc-50 pb-2">
                                <span class="text-zinc-400 font-medium">Дата</span>
                                <span class="font-bold text-zinc-900 font-mono">
                                    {{ $concert->date_time ? \Carbon\Carbon::parse($concert->date_time)->format('d.m.Y H:i') : '—' }}
                                </span>
                            </div>
                            
                            {{-- Блок статистики --}}
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div class="bg-zinc-50 p-4 rounded-2xl">
                                    <div class="text-[10px] uppercase font-bold text-zinc-400">Продано</div>
                                    <div class="text-lg font-black text-indigo-600">{{ $soldTickets }} <span class="text-xs text-zinc-300">/ {{ $totalPlaces }}</span></div>
                                </div>
                                <div class="bg-zinc-50 p-4 rounded-2xl">
                                    <div class="text-[10px] uppercase font-bold text-zinc-400">Выручка</div>
                                    <div class="text-lg font-black text-zinc-900">{{ number_format($totalEarnings, 0, ',', ' ') }} <span class="text-xs font-normal">₽</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('organizer.concert.view', $concert->id) }}" 
                       class="block w-full py-4 rounded-xl bg-zinc-900 text-white text-center text-sm font-bold uppercase tracking-widest hover:bg-indigo-600 transition-all duration-300 active:scale-95">
                        Открыть детали
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection