@extends('layouts.app')
@section('title', 'Доступные площадки — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-12">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Доступные площадки партнеров</h2>
    </header>

    @if($partners->isEmpty())
        <div class="text-center p-20 bg-white rounded-3xl border border-zinc-100 shadow-sm text-zinc-400">
            Нет зарегистрированных площадок.
        </div>
    @else
        <div class="space-y-8 w-full">
            @foreach($partners as $partner)
                @if($partner->halls->isNotEmpty())
                    <div class="w-full bg-white p-8 rounded-3xl shadow-sm border border-zinc-100">
                        <div class="flex flex-col gap-2 mb-8 pb-6 border-b border-zinc-100">
                            <h3 class="text-xl font-bold text-zinc-900">{{ $partner->company_name }}</h3>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-zinc-500">
                                <span class="flex items-center gap-1.5">{{ $partner->address }}</span>
                                <a href="tel:{{ $partner->phone }}" class="flex items-center gap-1.5 font-semibold text-indigo-600">
                                    {{ $partner->phone }}
                                </a>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Доступные залы:</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                @foreach($partner->halls as $hall)
                                    <div class="group bg-zinc-50 border border-zinc-100 p-6 rounded-2xl flex flex-col justify-between">
                                        <div>
                                            <div class="font-bold text-lg text-zinc-900 mb-0.5">{{ $hall->name }}</div>
                                            <div class="text-xs text-zinc-500 mb-4">Адрес: {{ $hall->address }}</div>
                                            
                                            @if($hall->status === 'maintenance')
                                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-red-50 border border-red-200 text-xs font-bold text-red-600 mb-4">
                                                    На техническом обслуживании
                                                </div>
                                            @else
                                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-zinc-200 text-xs font-semibold text-zinc-700 mb-4">
                                                    {{ $hall->capacity }} мест
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4 flex flex-col gap-2">
                                            @if($hall->status === 'maintenance')
                                                <button disabled class="block w-full py-3 rounded-xl bg-zinc-200 text-zinc-500 text-center font-bold text-xs uppercase cursor-not-allowed">
                                                    Закрыт
                                                </button>
                                            @else
                                                <a href="{{ route('organizer.hall.rent', $hall->id) }}" 
                                                   class="block w-full py-3 rounded-xl bg-zinc-900 text-white text-center font-bold text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all active:scale-95">
                                                    Арендовать
                                                </a>
                                                <button onclick="openModal('{{ addslashes($hall->name) }}', {{ $hall->rows->load('seats')->toJson() }}, '{{ route('organizer.hall.rent', $hall->id) }}')" 
                                                        class="block w-full py-3 rounded-xl bg-zinc-100 text-zinc-900 text-center font-bold text-xs uppercase tracking-widest hover:bg-zinc-200 transition-all active:scale-95">
                                                    Посмотреть схему
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<div id="hallModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-4xl rounded-3xl p-8 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitle" class="text-xl font-bold uppercase">Схема зала</h3>
            <button onclick="closeModal()" class="text-zinc-400 hover:text-zinc-900 text-2xl">×</button>
        </div>
        <div id="modalContent" class="bg-zinc-50 p-6 rounded-2xl mb-6 flex flex-col items-center gap-2 overflow-x-auto min-h-[200px]"></div>
        <div class="flex gap-4">
            <button onclick="closeModal()" class="flex-1 py-3 bg-zinc-100 rounded-xl font-bold hover:bg-zinc-200 uppercase text-xs tracking-widest">Назад</button>
            <a id="btnRent" href="#" class="flex-[2] py-3 bg-indigo-600 text-white text-center rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-indigo-700">Арендовать зал</a>
        </div>
    </div>
</div>

<script>
    function openModal(name, rows, rentUrl) {
        document.getElementById('modalTitle').innerText = 'Схема: ' + name;
        document.getElementById('btnRent').href = rentUrl;
        const container = document.getElementById('modalContent');
        container.innerHTML = '';
        
        if (!rows || rows.length === 0) {
            container.innerHTML = '<p class="text-zinc-400">Схема не задана</p>';
        } else {
            rows.forEach(row => {
                const rowDiv = document.createElement('div');
                rowDiv.className = "flex gap-2";
                
                row.seats.forEach(seat => {
                    const cell = document.createElement('div');
                    if (seat.type === 'seat') {
                        cell.className = "h-8 w-8 text-[9px] flex items-center justify-center rounded bg-zinc-900 text-white font-bold";
                        cell.innerText = seat.number;
                    } else {
                        cell.className = "h-8 w-8 rounded bg-transparent border border-zinc-200";
                    }
                    rowDiv.appendChild(cell);
                });
                container.appendChild(rowDiv);
            });
        }

        document.getElementById('hallModal').classList.remove('hidden');
    }

    function closeModal() { 
        document.getElementById('hallModal').classList.add('hidden'); 
    }
</script>
@endsection