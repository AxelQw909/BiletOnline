@extends('layouts.app')
@section('title', 'Аренды зала ' . $hall->name)

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('partner.profile') }}" class="text-zinc-400 hover:text-zinc-900 text-sm mb-6 inline-block">&larr; Назад в профиль</a>
        <h2 class="text-2xl font-black text-zinc-900 uppercase mb-8">Аренды зала: {{ $hall->name }}</h2>

        @if($concerts->isEmpty())
            <div class="bg-white p-10 rounded-3xl border border-dashed border-zinc-200 text-center text-zinc-400">
                У этого зала еще нет активных или завершенных аренд.
            </div>
        @else
            <div class="grid gap-4">
                @foreach($concerts as $concert)
                    <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-lg text-zinc-900">{{ $concert->title }}</h4>
                            <p class="text-xs text-zinc-500">Дата: {{ \Carbon\Carbon::parse($concert->date)->format('d.m.Y H:i') }}</p>
                            <div class="mt-2 text-xs font-medium text-zinc-700 bg-zinc-100 px-3 py-1 rounded-full inline-block">
                                Статус: {{ $concert->status === 'approved' ? 'Одобрено' : ($concert->status === 'pending' ? 'На рассмотрении' : 'Отклонено') }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-zinc-400 uppercase font-bold">Организатор</div>
                            <div class="font-bold text-zinc-900">{{ $concert->organizer->name ?? 'Не указан' }}</div>
                            <div class="text-sm text-indigo-600 font-semibold">{{ $concert->organizer->phone ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection