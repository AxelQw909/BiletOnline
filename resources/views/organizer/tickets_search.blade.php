@extends('layouts.app')
@section('title', 'Поиск и проверка билетов — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-8">
        <h2 class="text-3xl font-black text-zinc-900 uppercase tracking-tight">Верификация билетов</h2>
    </header>

    <div id="status-message" class="hidden mb-6 p-4 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-center">
        Билеты успешно подтверждены!
    </div>

    {{-- Поиск --}}
    <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm mb-8">
        <form action="{{ route('organizer.tickets.search') }}" method="GET" class="flex gap-4">
            <select name="concert_id" onchange="this.form.submit()" class="w-1/3 border border-zinc-200 p-3 rounded-lg text-base bg-white focus:ring-0">
                <option value="">Выберите концерт...</option>
                @foreach($concerts as $c)
                    <option value="{{ $c->id }}" {{ $selectedConcertId == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Номер билета или имя..." class="w-full border border-zinc-200 p-3 rounded-lg text-base focus:ring-0">
            <button type="submit" class="bg-zinc-900 text-white px-8 rounded-lg text-base font-bold uppercase hover:bg-indigo-600 transition">Поиск</button>
        </form>
    </div>

    @if($selectedConcertId)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tickets->groupBy('customer_phone') as $phone => $clientTickets)
                <div class="bg-white rounded-2xl border border-zinc-200 p-6 shadow-sm flex flex-col">
                    <div class="mb-4">
                        <div class="font-black text-lg text-zinc-900">{{ $clientTickets->first()->customer_name }}</div>
                        <div class="text-sm text-zinc-500 font-mono mb-3">{{ $phone }}</div>
                        <div class="flex justify-between items-center bg-zinc-100 p-3 rounded-lg">
                            <span class="text-sm font-bold text-zinc-700">Кол-во мест: {{ $clientTickets->count() }}</span>
                            <button onclick="toggleDetails(this)" class="text-sm font-bold uppercase text-indigo-600 hover:underline">Подробнее</button>
                        </div>
                    </div>

                    <div class="details-block hidden space-y-3 mt-2 pt-4 border-t border-zinc-100">
                        @foreach($clientTickets as $ticket)
                            <div class="flex items-center justify-between text-sm py-1">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" class="ticket-checkbox w-5 h-5" value="{{ $ticket->id }}" {{ $ticket->status === 'paid' ? 'disabled checked' : '' }}>
                                    <span class="font-mono text-zinc-500">#{{ $ticket->ticket_code }}</span>
                                </div>
                                <span class="font-bold text-zinc-800">Р.{{ $ticket->row }} М.{{ $ticket->seat }}</span>
                                <span class="font-bold {{ $ticket->status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $ticket->status === 'paid' ? 'Оплачено' : 'Ожидает' }}
                                </span>
                            </div>
                        @endforeach
                        
                        <button onclick="confirmSelected(this)" class="w-full mt-4 bg-zinc-900 text-white text-sm font-bold uppercase py-3 rounded-lg hover:bg-indigo-600 transition active:scale-95">
                            Подтвердить оплату
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-zinc-500 text-lg">Билеты не найдены</div>
            @endforelse
        </div>
    @endif
</div>

<script>
    function toggleDetails(btn) {
        const block = btn.closest('.bg-white').querySelector('.details-block');
        block.classList.toggle('hidden');
        btn.innerText = block.classList.contains('hidden') ? 'Подробнее' : 'Скрыть';
    }

    function confirmSelected(btn) {
        const block = btn.closest('.details-block');
        const checkboxes = block.querySelectorAll('.ticket-checkbox:checked:not(:disabled)');
        const ids = Array.from(checkboxes).map(cb => cb.value);

        if (ids.length === 0) return alert('Выберите хотя бы один билет, ожидающий оплаты');

        fetch("{{ route('organizer.tickets.bulk.status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Показываем плашку
                const msg = document.getElementById('status-message');
                msg.classList.remove('hidden');
                
                // Перезагрузка через 1.5 секунды
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        });
    }
</script>
@endsection