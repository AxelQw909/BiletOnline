@extends('layouts.app')
@section('title', 'Личный кабинет партнера — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Личный кабинет партнера</h2>
    </header>

    <div class="w-full grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {{-- Данные партнера --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-widest text-zinc-900 mb-6 border-b border-zinc-100 pb-4">Данные партнера</h3>
                
                <form action="{{ route('partner.profile.update', ['id' => $partner->id]) }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Название площадки</label>
                        <input type="text" name="company_name" value="{{ $partner->company_name }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Адрес площадки</label>
                        <input type="text" name="address" value="{{ $partner->address }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Контактный номер</label>
                        <input type="text" name="phone" value="{{ $partner->phone }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs text-zinc-400 uppercase font-bold mb-2">Почта</label>
                        <input type="email" name="email" value="{{ $partner->email }}" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-zinc-900 focus:outline-none transition">
                    </div>
                    <button type="submit" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all duration-300 active:scale-95">
                        Сохранить изменения
                    </button>
                </form>
            </div>
        </div>

        {{-- Секция залов --}}
        <div class="lg:col-span-3 space-y-8">
            {{-- Блок вывода сообщений об ошибках или успехе --}}
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl" role="alert">
                    <p class="font-bold uppercase text-xs tracking-widest mb-1">Ошибка</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-xl" role="alert">
                    <p class="font-bold uppercase text-xs tracking-widest mb-1">Успешно</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm">
                <h2 class="text-xl font-bold uppercase tracking-tight">Ваши залы</h2>
                <a href="{{ route('partner.hall.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-6 py-3 uppercase font-bold tracking-widest rounded-xl transition-all active:scale-95">
                    + Добавить зал
                </a>
            </div>

            @if($halls->isEmpty())
                <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-zinc-200 text-zinc-400 text-sm">
                    У вас пока нет созданных залов. Нажмите «Добавить зал», чтобы создать схему.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($halls as $hall)
                        <div class="bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm hover:shadow-md transition-all group">
                            <h4 class="font-bold text-lg text-zinc-900 mb-2">{{ $hall->name }}</h4>
                            <p class="text-xs text-zinc-400 mb-4 flex items-center gap-2">
                                <span></span> {{ $hall->address }}
                            </p>
                            
                            <div class="bg-zinc-50 border border-zinc-100 rounded-2xl p-4 mb-6">
                                <div class="text-[10px] uppercase font-bold text-zinc-400">Вместимость</div>
                                <div class="text-xl font-black text-zinc-900">{{ $hall->capacity }} <span class="text-xs font-normal text-zinc-500">мест</span></div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('partner.hall.edit', $hall->id) }}" 
                                   class="flex-1 text-center border-2 border-zinc-900 text-zinc-900 hover:bg-zinc-900 hover:text-white py-3 rounded-xl text-xs uppercase font-bold tracking-wider transition-all">
                                    Редактировать
                                </a>
                                
                                <form action="{{ route('partner.hall.destroy', $hall->id) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот зал?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 hover:bg-red-500 text-red-500 hover:text-white py-3 px-4 rounded-xl text-xs uppercase font-bold tracking-wider transition-all">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection