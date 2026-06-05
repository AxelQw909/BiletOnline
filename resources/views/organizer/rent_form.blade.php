@extends('layouts.app')
@section('title', 'Аренда зала — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-8">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Подача заявки: {{ $hall->name }}</h2>
    </header>

    <div id="dataContainer" data-rows='@json($hall->rows->load('seats'))' class="hidden"></div>
    <div id="bookedDates" data-dates='@json($bookedDates)' class="hidden"></div>
    {{-- Номер телефона теперь берется из связи $hall->user, загруженной в контроллере --}}
    <div id="partnerPhone" data-phone="{{ $hall->user->phone ?? 'не указан' }}" class="hidden"></div>

    <form action="{{ route('organizer.hall.rent.submit', $hall->id) }}" method="POST" id="rentForm" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @csrf
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm space-y-4">
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-1">Название концерта</label>
                    <input type="text" name="title" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-1">Дата и время</label>
                    <input type="datetime-local" id="rentDate" name="date_time" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p id="dateError" class="text-red-500 text-[10px] font-bold mt-1 hidden">Этот зал забронирован на эту дату и время</p>
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-1">Способ оплаты</label>
                    <textarea name="payment_info" required placeholder="Оплата в кассе..." class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-xs uppercase font-bold text-zinc-400 mb-1">Базовая цена (₽)</label>
                    <input type="number" id="basePriceInput" name="base_price" value="500" min="0" required class="w-full border border-zinc-200 p-3 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
                </div>
                <input type="hidden" name="custom_prices" id="customPricesInput" value="{}">
                
                <button type="submit" id="submitBtn" class="w-full bg-zinc-900 hover:bg-indigo-600 text-white text-sm py-4 rounded-xl uppercase font-bold tracking-wider transition-all duration-300 active:scale-95">
                    Отправить заявку
                </button>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-6">
            <div class="p-6 bg-white rounded-3xl border border-zinc-100 shadow-sm">
                <p class="font-bold text-zinc-900 mb-2">Занятые даты:</p>
                @if(count($bookedDates) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($bookedDates as $date)
                            <span class="bg-zinc-100 text-zinc-600 px-3 py-1 rounded-full text-xs font-mono">{{ \Carbon\Carbon::parse($date)->format('d.m.Y H:i') }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-400 italic">На данный момент забронированных дат нет.</p>
                @endif
            </div>

            <div class="w-full border border-zinc-100 p-8 bg-white rounded-3xl shadow-sm overflow-x-auto flex justify-center">
                <div id="pricingGrid" class="inline-grid gap-2"></div>
            </div>
        </div>
    </form>
</div>

{{-- Модальное окно с размытым фоном --}}
<div id="successModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-3xl max-w-sm w-full text-center shadow-2xl border border-zinc-100 transform transition-all">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h3 class="text-lg font-black uppercase mb-4">Заявка отправлена!</h3>
        <p class="text-sm text-zinc-600 mb-6">
            Согласуйте детали с партнёром по номеру: <br>
            <span class="text-indigo-600 font-bold text-lg" id="phoneDisplay"></span>
        </p>
        <a href="{{ route('organizer.profile') }}" class="block w-full bg-zinc-900 hover:bg-zinc-800 text-white py-4 rounded-xl uppercase font-bold text-sm transition-all active:scale-95">Продолжить</a>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rowsData = JSON.parse(document.getElementById('dataContainer').dataset.rows);
        const bookedDates = JSON.parse(document.getElementById('bookedDates').dataset.dates);
        const rentDateInput = document.getElementById('rentDate');
        const dateError = document.getElementById('dateError');
        const rentForm = document.getElementById('rentForm');
        const successModal = document.getElementById('successModal');
        
        // Логика проверки даты
        rentDateInput.addEventListener('change', function() {
            const selectedDate = this.value.replace('T', ' ');
            if (bookedDates.includes(selectedDate)) {
                dateError.classList.remove('hidden');
                document.getElementById('submitBtn').disabled = true;
            } else {
                dateError.classList.add('hidden');
                document.getElementById('submitBtn').disabled = false;
            }
        });

        // Отправка формы (AJAX)
        rentForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(rentForm);
            
            fetch(rentForm.action, {
                method: 'POST',
                body: formData
            }).then(() => {
                // Получаем телефон из скрытого блока
                const phone = document.getElementById('partnerPhone').dataset.phone;
                document.getElementById('phoneDisplay').innerText = phone;
                successModal.classList.remove('hidden');
            });
        };

        // --- Логика сетки ---
        let customPrices = {};
        const basePriceInput = document.getElementById('basePriceInput');

        function renderPricingGrid() {
            const container = document.getElementById('pricingGrid');
            container.innerHTML = '';
            const basePrice = parseFloat(basePriceInput.value) || 0;
            let maxSeats = 0;
            rowsData.forEach(r => { if(r.seats.length > maxSeats) maxSeats = r.seats.length; });

            rowsData.sort((a,b) => a.number - b.number).forEach(row => {
                const rowLabel = document.createElement('div');
                rowLabel.className = "flex items-center justify-center font-bold text-zinc-400 w-10 text-xs";
                rowLabel.innerText = "Ряд " + row.number;
                container.appendChild(rowLabel);

                row.seats.sort((a,b) => a.number - b.number).forEach(seat => {
                    const cell = document.createElement('div');
                    if (seat.type === 'aisle' || seat.type === 'empty') {
                        cell.className = "h-14 w-14";
                    } else {
                        const key = `seat-${seat.id}`;
                        const price = customPrices[key] !== undefined ? customPrices[key] : basePrice;
                        cell.className = `h-14 w-14 rounded-xl flex flex-col items-center justify-center cursor-pointer transition-all border-2 ${customPrices[key] ? 'bg-amber-500 border-amber-600 text-white' : 'bg-zinc-900 border-zinc-800 text-white'} hover:opacity-80`;
                        cell.innerHTML = `<span class="text-[10px] opacity-75">${seat.number}</span><span class="font-bold text-xs">${price}₽</span>`;
                        cell.onclick = () => {
                            const val = prompt('Введите цену для места №' + seat.number + ' (Ряд ' + row.number + '):', price);
                            if (val !== null) { customPrices[key] = parseFloat(val); renderPricingGrid(); }
                        };
                    }
                    container.appendChild(cell);
                });
            });
            container.style.gridTemplateColumns = `40px repeat(${maxSeats}, minmax(56px, 1fr))`;
            document.getElementById('customPricesInput').value = JSON.stringify(customPrices);
        }

        basePriceInput.addEventListener('input', renderPricingGrid);
        renderPricingGrid();
    });
</script>
@endsection