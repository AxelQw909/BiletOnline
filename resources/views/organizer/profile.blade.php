@extends('layouts.app')
@section('title', 'Профиль организатора — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Личный кабинет</h2>
    </header>

    <div class="w-full grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {{-- Блок данных организатора --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 mb-6 border-b border-zinc-100 pb-4">Данные организатора</h3>
                
                <form action="{{ route('organizer.profile.update', $organizer->id) }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Название организации</label>
                        <input type="text" name="company_name" value="{{ $organizer->company_name }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Номер телефона</label>
                        <input type="text" name="phone" value="{{ $organizer->phone }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Почта (Email)</label>
                        <input type="email" value="{{ $organizer->email }}" disabled class="w-full border border-zinc-100 p-3 rounded-xl text-sm bg-zinc-50 text-zinc-400 cursor-not-allowed">
                    </div>
                    <button type="submit" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all duration-300 active:scale-95">
                        Сохранить профиль
                    </button>
                </form>
            </div>
        </div>

        {{-- Блок мероприятий --}}
        <div class="lg:col-span-3">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 mb-6 border-b border-zinc-100 pb-4">Мои мероприятия</h3>
                
                @if($concerts->isEmpty())
                    <div class="text-center py-20 border-2 border-dashed border-zinc-100 rounded-2xl text-zinc-400 text-sm">
                        У вас пока нет созданных мероприятий.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="text-xs uppercase text-zinc-400">
                                <tr>
                                    <th class="py-4 px-4">Название</th>
                                    <th class="py-4 px-4">Дата</th>
                                    <th class="py-4 px-4">Статус</th>
                                    <th class="py-4 px-4">Цена</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($concerts as $concert)
                                    <tr class="hover:bg-zinc-50 transition-colors">
                                        <td class="py-5 px-4 font-bold text-zinc-900">{{ $concert->title }}</td>
                                        <td class="py-5 px-4 text-zinc-500 font-mono text-sm">{{ $concert->date_time->format('d.m.Y H:i') }}</td>
                                        <td class="py-5 px-4">
                                            <span class="px-3 py-1.5 rounded-full text-[10px] uppercase font-bold 
                                                {{ $concert->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : 
                                                   ($concert->status === 'rejected' ? 'bg-red-50 text-red-600' : 'bg-zinc-100 text-zinc-600') }}">
                                                
                                                @if($concert->status === 'approved') Одобрено
                                                @elseif($concert->status === 'pending') На рассмотрении
                                                @elseif($concert->status === 'rejected') Отклонено
                                                @else {{ $concert->status }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-5 px-4 font-bold text-zinc-900">{{ number_format($concert->base_price, 0) }} ₽</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection