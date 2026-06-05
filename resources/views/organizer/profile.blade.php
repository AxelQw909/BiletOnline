@extends('layouts.app')
@section('title', 'Профиль организатора — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-950 tracking-tight uppercase">Личный кабинет</h2>
    </header>

    <div class="w-full grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {{-- Блок данных организатора --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-950 mb-6 border-b border-zinc-100 pb-4">Данные организатора</h3>
                
                <form id="profileForm" action="{{ route('organizer.profile.update', $organizer->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs text-zinc-600 uppercase font-bold mb-2">Название организации</label>
                        <input type="text" name="company_name" value="{{ $organizer->company_name }}" required readonly class="profile-input w-full border border-zinc-200 p-4 rounded-xl text-base text-zinc-800 bg-zinc-50 focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-600 uppercase font-bold mb-2">Номер телефона</label>
                        <input type="text" name="phone" value="{{ $organizer->phone }}" required readonly class="profile-input w-full border border-zinc-200 p-4 rounded-xl text-base text-zinc-800 bg-zinc-50 focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-600 uppercase font-bold mb-2">Почта (Email)</label>
                        <input type="email" value="{{ $organizer->email }}" disabled class="w-full border border-zinc-200 p-4 rounded-xl text-base bg-zinc-50 text-zinc-500 cursor-not-allowed">
                    </div>
                    
                    <button type="button" id="editBtn" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all duration-300 active:scale-95">
                        Редактировать данные
                    </button>
                </form>
            </div>
        </div>

        {{-- Блок мероприятий --}}
        <div class="lg:col-span-3">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-950 mb-6 border-b border-zinc-100 pb-4">Мои мероприятия</h3>
                
                @if($concerts->isEmpty())
                    <div class="text-center py-20 border-2 border-dashed border-zinc-200 rounded-2xl text-zinc-500 text-base">
                        У вас пока нет созданных мероприятий.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="text-xs uppercase text-zinc-500">
                                <tr>
                                    <th class="py-5 px-4">Название</th>
                                    <th class="py-5 px-4">Дата</th>
                                    <th class="py-5 px-4">Статус</th>
                                    <th class="py-5 px-4">Цена</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($concerts as $concert)
                                    <tr class="hover:bg-zinc-50 transition-colors">
                                        <td class="py-6 px-4 font-bold text-zinc-900 text-base">{{ $concert->title }}</td>
                                        <td class="py-6 px-4 text-zinc-700 font-mono text-base">{{ $concert->date_time->format('d.m.Y H:i') }}</td>
                                        <td class="py-6 px-4">
                                            <span class="px-4 py-2 rounded-full text-xs uppercase font-bold 
                                                {{ $concert->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 
                                                   ($concert->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-zinc-100 text-zinc-700') }}">
                                                @if($concert->status === 'approved') Одобрено
                                                @elseif($concert->status === 'pending') На рассмотрении
                                                @elseif($concert->status === 'rejected') Отклонено
                                                @else {{ $concert->status }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-6 px-4 font-bold text-zinc-900 text-base">{{ number_format($concert->base_price, 0) }} ₽</td>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtn = document.getElementById('editBtn');
        const form = document.getElementById('profileForm');
        const inputs = document.querySelectorAll('.profile-input');
        let isEditing = false;

        editBtn.addEventListener('click', function() {
            if (!isEditing) {
                // Режим редактирования
                inputs.forEach(input => {
                    input.readOnly = false;
                    input.classList.remove('bg-zinc-50');
                    input.classList.add('bg-white', 'border-zinc-300');
                });
                editBtn.innerText = 'Сохранить';
                editBtn.classList.remove('bg-zinc-900');
                editBtn.classList.add('bg-indigo-600');
                isEditing = true;
            } else {
                // Сохранение
                form.submit();
            }
        });
    });
</script>
@endsection