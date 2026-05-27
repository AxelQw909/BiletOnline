@extends('layouts.app')
@section('title', 'Поиск и проверка билетов — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Верификация билетов</h2>
    </header>

    <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm mb-8">
        <form action="{{ route('organizer.tickets.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Выберите мероприятие</label>
                <select name="concert_id" onchange="this.form.submit()" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none bg-white transition">
                    <option value="">-- Выберите из списка --</option>
                    @foreach($concerts as $c)
                        <option value="{{ $c->id }}" {{ $selectedConcertId == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-xs uppercase font-bold text-zinc-400 mb-2">Поиск по номеру билета</label>
                <div class="flex gap-3">
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Например: 48392" class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none font-mono transition">
                    <button type="submit" class="bg-zinc-900 hover:bg-indigo-600 text-white px-8 rounded-xl text-sm uppercase font-bold tracking-wider transition-all active:scale-95">Найти</button>
                </div>
            </div>
        </form>
    </div>

    @if($selectedConcertId)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400">Результаты поиска</h3>
        </div>
        
        @if($tickets->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-zinc-100 text-zinc-400 text-sm">
                Билеты по заданным критериям не найдены.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach($tickets as $ticket)
                    <div class="bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                {{-- ИСПРАВЛЕНО: ticket_number -> ticket_code --}}
                                <span class="font-mono font-black text-lg text-zinc-900">№ {{ $ticket->ticket_code }}</span>
                                <span class="text-[10px] uppercase font-bold px-3 py-1 rounded-full 
                                    {{ $ticket->status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $ticket->status === 'paid' ? 'Оплачено' : 'Ожидание' }}
                                </span>
                            </div>
                            
                            <div class="space-y-3 text-sm text-zinc-600 border-t border-zinc-100 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-zinc-400">Место:</span> 
                                    <span class="font-bold text-zinc-900">Ряд {{ $ticket->row }}, Место {{ $ticket->seat }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-400">Цена:</span> 
                                    <span class="font-mono font-bold text-zinc-900">{{ $ticket->price }} ₽</span>
                                </div>
                                <div class="pt-2 border-t border-zinc-50">
                                    <div class="font-bold text-zinc-900">{{ $ticket->customer_name }}</div>
                                    <div class="text-xs text-zinc-400">{{ $ticket->customer_phone }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-zinc-100">
                            <form action="{{ route('organizer.tickets.status', $ticket->id) }}" method="POST">
                                @csrf
                                @if($ticket->status === 'pending')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-xs py-3 rounded-xl uppercase font-bold tracking-wider transition-all active:scale-95">Подтвердить оплату</button>
                                @else
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit" class="w-full border border-zinc-200 hover:bg-zinc-50 text-zinc-600 text-xs py-3 rounded-xl uppercase font-bold tracking-wider transition-all active:scale-95">Вернуть в ожидание</button>
                                @endif
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="text-center py-20 bg-white rounded-3xl border border-zinc-100 text-zinc-400 text-sm">
            Пожалуйста, выберите мероприятие, чтобы отобразить список билетов.
        </div>
    @endif
</div>
@endsection