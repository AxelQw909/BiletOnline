@extends('layouts.app')
@section('title', 'Купить билет на: ' . $concert->title)

@section('content')
{{-- Безопасная передача данных через data-атрибуты --}}
<div id="bookingData" 
     data-schema='@json($concert->hall->schema)' 
     data-tickets='@json($concert->tickets)' 
     data-custom='@json($concert->custom_prices ?? [])' 
     data-baseprice='{{ $concert->base_price }}' 
     class="hidden"></div>

<div class="w-full bg-white border border-zinc-200 p-6">
    <div class="border-b border-zinc-200 pb-4 mb-6">
        <h2 class="text-xl font-bold uppercase">{{ $concert->title }}</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div id="clientGrid" class="grid gap-1 p-2 bg-white border border-zinc-200"></div>
        </div>

        <div class="lg:col-span-1 border-l border-zinc-200 pl-8">
            <form id="bookingForm" class="space-y-4">
                @csrf
                <input type="text" id="custName" placeholder="Имя" class="w-full border p-2 text-sm">
                <input type="email" id="custEmail" placeholder="Email" class="w-full border p-2 text-sm">
                <button type="submit" class="w-full bg-zinc-900 text-white py-3 uppercase text-sm">Забронировать</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Считываем данные из DOM вместо использования @json внутри скрипта
        const dataEl = document.getElementById('bookingData');
        const hallSchema = JSON.parse(dataEl.dataset.schema);
        const tickets = JSON.parse(dataEl.dataset.tickets);
        const customPrices = JSON.parse(dataEl.dataset.custom);
        const basePrice = parseFloat(dataEl.dataset.baseprice);

        const container = document.getElementById('clientGrid');
        const maxSeat = Math.max(...hallSchema.map(i => i.seat));
        container.style.gridTemplateColumns = `repeat(${maxSeat}, minmax(42px, 1fr))`;

        hallSchema.forEach(item => {
            const cell = document.createElement('div');
            if (item.type === 'aisle') {
                cell.className = "h-10 bg-zinc-100 border-dashed border";
            } else {
                const key = `${item.row}-${item.seat}`;
                const isTaken = tickets.some(t => t.row == item.row && t.seat == item.seat);
                const price = customPrices[key] !== undefined ? customPrices[key] : basePrice;
                
                cell.className = isTaken ? "h-10 bg-zinc-200 text-zinc-400 border flex flex-col items-center justify-center text-[9px]" : "h-10 bg-zinc-950 text-white border flex flex-col items-center justify-center text-[9px] cursor-pointer";
                cell.innerHTML = `<span>${item.row}-${item.seat}</span><span class="font-bold">${isTaken ? 'ЗАНЯТ' : price+'₽'}</span>`;
            }
            container.appendChild(cell);
        });
    });
</script>
@endsection