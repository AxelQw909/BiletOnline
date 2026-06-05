@extends('layouts.app')
@section('title', 'Заявки на бронирование площадок')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    {{-- Заголовок --}}
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-950 tracking-tight uppercase">Заявки на бронирование</h2>
        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-2">Управление входящими запросами от организаторов</p>
    </header>

    <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
        @if($concerts->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-zinc-200 text-zinc-500 text-base">
                Входящих заявок на бронирование залов пока нет.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-100 text-[10px] uppercase tracking-widest text-zinc-400 font-bold">
                            <th class="py-6 px-4">Мероприятие</th>
                            <th class="py-6 px-4">Зал</th>
                            <th class="py-6 px-4">Дата</th>
                            <th class="py-6 px-4">Статус</th>
                            <th class="py-6 px-4 text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @foreach($concerts as $concert)
                            <tr class="hover:bg-zinc-50 transition-colors duration-200">
                                <td class="py-6 px-4">
                                    <div class="font-bold text-zinc-950 text-base">{{ $concert->title }}</div>
                                    <div class="text-[11px] text-zinc-500 font-bold uppercase tracking-wide mt-1">{{ $concert->organizer->company_name }} &bull; {{ $concert->organizer->phone }}</div>
                                </td>
                                <td class="py-6 px-4 text-zinc-800 font-bold">
                                    {{ $concert->hall->name }}
                                </td>
                                <td class="py-6 px-4 text-zinc-800 font-medium">
                                    {{ $concert->date_time->format('d.m.Y') }} 
                                    <span class="text-zinc-400 ml-1">{{ $concert->date_time->format('H:i') }}</span>
                                </td>
                                <td class="py-6 px-4">
                                    @if($concert->status === 'pending')
                                        <span class="text-[10px] uppercase font-bold tracking-widest bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg border border-amber-200">Ожидает</span>
                                    @elseif($concert->status === 'approved')
                                        <span class="text-[10px] uppercase font-bold tracking-widest bg-zinc-900 text-white px-3 py-1.5 rounded-lg">Одобрено</span>
                                    @else
                                        <span class="text-[10px] uppercase font-bold tracking-widest bg-red-50 text-red-600 px-3 py-1.5 rounded-lg border border-red-100">Отказано</span>
                                    @endif
                                </td>
                                <td class="py-6 px-4 text-right">
                                    @if($concert->status === 'pending')
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('partner.requests.status', $concert->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="bg-zinc-900 hover:bg-emerald-600 text-white text-[10px] px-5 py-3 uppercase font-bold tracking-widest rounded-xl transition-all active:scale-95">Одобрить</button>
                                            </form>
                                            <form action="{{ route('partner.requests.status', $concert->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-[10px] px-5 py-3 uppercase font-bold tracking-widest rounded-xl transition-all active:scale-95">Отказать</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-[10px] uppercase font-bold text-zinc-400 tracking-widest italic">Обработано</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection