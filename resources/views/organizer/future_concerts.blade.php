@extends('layouts.app')
@section('title', 'Будущие концерты — BiletOnline')

@section('content')
<div class="w-full bg-white border border-zinc-200 p-6">
    <h2 class="text-lg font-bold uppercase tracking-tight mb-6 border-b border-zinc-200 pb-3">Мои заявки и будущие концерты</h2>

    @if($concerts->isEmpty())
        <div class="text-center p-12 text-zinc-400 text-sm border border-dashed border-zinc-200">
            Вы пока не подавали заявок на проведение концертов.
        </div>
    @else
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="border-b border-zinc-200 text-xs uppercase text-zinc-400 font-bold">
                        <th class="py-3 px-4">Название мероприятия</th>
                        <th class="py-3 px-4">Площадка / Зал</th>
                        <th class="py-3 px-4">Дата проведения</th>
                        <th class="py-3 px-4">Статус</th>
                        <th class="py-3 px-4 text-right">Действие</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @foreach($concerts as $concert)
                        <tr class="hover:bg-zinc-50 transition">
                            <td class="py-4 px-4">
                                <div class="font-bold text-zinc-900">{{ $concert->title }}</div>
                                <div class="text-xs text-zinc-500">Организатор: {{ $concert->dk_title ?? '—' }}</div>
                            </td>
                            <td class="py-4 px-4 text-zinc-700">
                                {{ $concert->hall->name }}
                            </td>
                            <td class="py-4 px-4 text-zinc-600">
                                {{ $concert->date_time->format('d.m.Y H:i') }}
                            </td>
                            <td class="py-4 px-4">
                                @if($concert->status === 'pending')
                                    <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 font-medium">Ожидает одобрения</span>
                                @elseif($concert->status === 'approved')
                                    <span class="text-xs bg-zinc-900 text-white px-2 py-0.5 font-medium">Одобрено партнером</span>
                                @else
                                    <span class="text-xs bg-red-50 text-red-700 border border-red-200 px-2 py-0.5 font-medium">Отклонено</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if($concert->status === 'approved')
                                    <a href="{{ route('organizer.concert.open', $concert->id) }}" class="bg-zinc-900 hover:bg-zinc-800 text-white text-xs px-3 py-1.5 uppercase font-medium tracking-wider transition">Открыть</a>
                                @else
                                    <span class="text-xs text-zinc-400 font-mono">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection