@extends('layouts.app')
@section('title', $concert->title . ' — Управление')

@section('content')
{{-- Безопасная передача данных через атрибуты --}}
<div id="viewData" 
     data-schema='@json($concert->hall->schema)' 
     data-tickets='@json($concert->tickets)' 
     data-price='{{ $concert->base_price }}' 
     data-custom='@json($concert->custom_prices ?? [])' 
     data-concert-id='{{ $concert->id }}'
     class="hidden"></div>

<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen space-y-8">
    
    {{-- Заголовок и управление --}}
    <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">{{ $concert->title }}</h2>
            <p class="text-sm text-zinc-400 mt-1">Управление местами и продажами в реальном времени</p>
        </div>
        
        <input type="text" id="clientLink" value="{{ route('client.concert', $concert->id) }}" class="hidden">
        <button onclick="navigator.clipboard.writeText(document.getElementById('clientLink').value); alert('Ссылка скопирована!');" 
                class="bg-zinc-900 hover:bg-indigo-600 text-white px-6 py-3 text-sm font-bold uppercase tracking-widest rounded-xl transition-all active:scale-95">
            Копировать ссылку
        </button>
    </div>

    {{-- Схема зала --}}
    <div class="bg-white p-8 rounded-3xl border border-zinc-100 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 text-xs font-bold uppercase text-zinc-500">
                    <span class="w-4 h-4 bg-zinc-900 rounded-lg"></span> Свободно
                </div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase text-zinc-500">
                    <span class="w-4 h-4 bg-red-500 rounded-lg"></span> Занято
                </div>
            </div>
            <div id="updateIndicator" class="flex items-center gap-2 text-xs font-bold uppercase text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full animate-pulse">
                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> Обновление в реальном времени
            </div>
        </div>

        <div class="w-full flex justify-center overflow-x-auto p-4">
            <div id="statusGrid" class="inline-grid gap-2"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const el = document.getElementById('viewData');
        const concertId = el.dataset.concertId;
        const hallSchema = JSON.parse(el.dataset.schema);
        const basePrice = parseFloat(el.dataset.price);
        const customPrices = JSON.parse(el.dataset.custom);

        const container = document.getElementById('statusGrid');
        
        function renderSchema(tickets) {
            container.innerHTML = '';
            
            const rows = [...new Set(hallSchema.map(i => i.row))].sort((a,b) => a - b);
            const maxSeat = Math.max(...hallSchema.map(i => i.seat));
            
            // Настройка сетки: номер ряда + места
            container.style.gridTemplateColumns = `40px repeat(${maxSeat}, minmax(56px, 1fr))`;

            rows.forEach(rowNum => {
                const rowLabel = document.createElement('div');
                rowLabel.className = "flex items-center justify-center font-bold text-zinc-400 w-10 text-[10px]";
                rowLabel.innerText = "Ряд " + rowNum;
                container.appendChild(rowLabel);

                const rowItems = hallSchema.filter(i => i.row === rowNum);
                rowItems.forEach(item => {
                    const cell = document.createElement('div');
                    cell.id = `seat-${item.row}-${item.seat}`;
                    
                    if (item.type === 'aisle') {
                        cell.className = "h-14 w-14";
                    } else {
                        const key = `${item.row}-${item.seat}`;
                        const hasTicket = tickets.find(t => t.row == item.row && t.seat == item.seat);
                        const price = customPrices[key] !== undefined ? customPrices[key] : basePrice;
                        
                        cell.className = `h-14 w-14 rounded-xl flex flex-col items-center justify-center text-[9px] transition-all duration-500 ${hasTicket ? 'bg-red-500 text-white' : 'bg-zinc-900 text-white'}`;
                        cell.innerHTML = `<span class="opacity-75">${item.seat}</span><span class="font-bold text-xs">${hasTicket ? 'ЗАНЯТО' : price+'₽'}</span>`;
                    }
                    container.appendChild(cell);
                });
            });
        }

        async function updateStatus() {
            try {
                const response = await fetch(`{{ url('/organizer/concert') }}/${concertId}/seats`);
                const tickets = await response.json();
                
                tickets.forEach(ticket => {
                    const cell = document.getElementById(`seat-${ticket.row}-${ticket.seat}`);
                    if (cell && !cell.classList.contains('bg-red-500')) {
                        cell.className = "h-14 w-14 rounded-xl flex flex-col items-center justify-center text-[9px] bg-red-500 text-white transition-all duration-500";
                        cell.querySelector('.font-bold').innerText = 'ЗАНЯТО';
                    }
                });
            } catch (err) { console.error("Ошибка:", err); }
        }

        renderSchema(JSON.parse(el.dataset.tickets));
        setInterval(updateStatus, 5000);
    });
</script>
@endsection