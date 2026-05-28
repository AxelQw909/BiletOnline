<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование: {{ $concert->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .seat-cell { width: 70px; height: 70px; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .bg-free { background-color: #ffffff; color: #18181b; border: 2px solid #e4e4e7; }
        .bg-booked { background-color: #f4f4f5; color: #a1a1aa; border: 2px solid #e4e4e7; cursor: not-allowed; }
        .bg-selected { background-color: #18181b !important; color: white !important; border-color: #18181b !important; }
        .row-label { width: 40px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #71717a; font-size: 0.8rem; }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 min-h-screen">

<div class="w-full p-6 md:p-10 space-y-10">
    <div class="bg-white p-8 md:p-12 rounded-3xl border border-zinc-100 shadow-sm flex flex-col md:flex-row justify-between items-start gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-extrabold uppercase tracking-tighter mb-4">{{ $concert->title }}</h1>
            <div class="flex flex-wrap gap-3">
                <span class="px-4 py-2 bg-zinc-100 rounded-xl text-xs font-bold uppercase tracking-widest text-zinc-600">{{ \Carbon\Carbon::parse($concert->date_time)->format('d.m.Y в H:i') }}</span>
                <span class="px-4 py-2 bg-indigo-50 rounded-xl text-xs font-bold uppercase tracking-widest text-indigo-700">{{ $concert->hall->address ?? 'Площадка не указана' }}</span>
            </div>
        </div>
        <div class="text-right border-l-4 border-zinc-900 pl-6">
            <p class="text-zinc-400 text-[10px] uppercase font-bold tracking-widest mb-1">Стоимость от</p>
            <p class="text-3xl font-black">{{ number_format($concert->base_price, 0, ',', ' ') }} ₽</p>
        </div>
    </div>

    <div class="bg-white p-8 md:p-12 rounded-3xl border border-zinc-100 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-10 text-center">Выберите места</h2>
        <div id="gridContainer" class="flex flex-col items-center gap-2 overflow-x-auto pb-4"></div>
        <div class="mt-12 w-full max-w-2xl mx-auto h-6 bg-zinc-100 rounded-t-[50px] flex items-center justify-center">
            <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Сцена</span>
        </div>
    </div>

    <button id="btnContinue" disabled class="w-full bg-zinc-900 text-white py-6 uppercase font-bold text-lg tracking-[0.2em] shadow-lg disabled:bg-zinc-200 transition-all hover:bg-indigo-600 rounded-2xl cursor-pointer">
        Продолжить оформление
    </button>
</div>

<div id="modal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl flex flex-col md:flex-row">
        <div class="w-full md:w-1/2 p-10 border-r border-zinc-100">
            <h2 class="text-2xl font-bold mb-6 uppercase tracking-tight">Ваши данные</h2>
            <div id="timerDisplay" class="mb-4 text-red-600 font-bold text-lg">Бронь активна: 10:00</div>
            
            <form id="bookingForm" class="space-y-4">
                @csrf
                <input type="text" name="customer_name" placeholder="Имя Фамилия" required class="w-full border border-zinc-200 p-4 rounded-xl outline-none focus:border-zinc-900">
                <input type="email" name="customer_email" placeholder="Email" required class="w-full border border-zinc-200 p-4 rounded-xl outline-none focus:border-zinc-900">
                <input type="tel" name="customer_phone" placeholder="Телефон" required class="w-full border border-zinc-200 p-4 rounded-xl outline-none focus:border-zinc-900">
                
                <div id="successMsg" class="hidden p-6 bg-green-50 text-green-800 rounded-xl font-bold text-center">
                    Билеты забронированы! Номер заказа отправлен на почту.
                </div>

                <div id="formActions" class="flex gap-4">
                    <button type="submit" id="submitBtn" class="flex-1 bg-zinc-900 text-white py-4 font-bold uppercase hover:bg-indigo-600">Забронировать</button>
                    <button type="button" onclick="hideModal()" class="flex-1 border border-zinc-200 py-4 font-bold uppercase hover:bg-zinc-100">Отмена</button>
                </div>
                <button type="button" id="closeBtn" onclick="hideModal()" class="hidden w-full bg-zinc-900 text-white py-4 font-bold uppercase">Закрыть</button>
            </form>
        </div>

        <div class="w-full md:w-1/2 p-10 bg-zinc-50 space-y-4 text-sm">
            <h3 class="font-bold text-zinc-500 uppercase tracking-widest mb-6">Детали бронирования</h3>
            <p><strong>Мероприятие:</strong> {{ $concert->title }}</p>
            <p><strong>Площадка:</strong> {{ $concert->hall->name ?? 'Не указано' }}</p>
            <p><strong>Адрес:</strong> {{ $concert->hall->address ?? 'Не указано' }}</p>
            <p><strong>Дата и время:</strong> {{ \Carbon\Carbon::parse($concert->date_time)->format('d.m.Y H:i') }}</p>
            <div class="border-t pt-4">
                <p><strong>Выбранные места:</strong></p>
                <div id="summarySeatsList" class="font-bold text-indigo-700"></div>
            </div>
            <div class="border-t pt-4">
                <p><strong>Комментарий организатора:</strong></p>
                <p class="text-zinc-600 italic">{{ $concert->payment_info ?? 'Нет комментариев' }}</p>
            </div>
            <p class="text-2xl font-bold pt-4 border-t">Итого: <span id="summaryPrice"></span> ₽</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const concert = @json($concert);
        let selectedSeats = [];
        let timerInterval;

        const container = document.getElementById('gridContainer');
        
        concert.hall.rows.forEach(row => {
            const rowWrapper = document.createElement('div');
            rowWrapper.className = "flex items-center gap-2";
            const label = document.createElement('div');
            label.className = "row-label";
            label.innerText = row.number;
            rowWrapper.appendChild(label);

            const seatGroup = document.createElement('div');
            seatGroup.className = "flex gap-2";
            
            row.seats.forEach(seat => {
                if (seat.type === 'aisle') {
                    const aisle = document.createElement('div');
                    aisle.className = "w-[70px] h-[70px]";
                    seatGroup.appendChild(aisle);
                } else {
                    const isBooked = concert.tickets.find(t => t.seat_id == seat.id && t.status !== 'cancelled');
                    const price = parseFloat(concert.base_price);
                    
                    const cell = document.createElement('div');
                    cell.className = `seat-cell rounded-xl cursor-pointer font-bold ${isBooked ? 'bg-booked' : 'bg-free hover:bg-zinc-100'}`;
                    cell.innerHTML = `<span class="text-lg">${seat.number}</span><span class="text-[10px] opacity-60">${price}₽</span>`;
                    
                    if (!isBooked) {
                        cell.addEventListener('click', () => {
                            const idx = selectedSeats.findIndex(s => s.seat_id == seat.id);
                            if (idx > -1) {
                                selectedSeats.splice(idx, 1);
                                cell.classList.remove('bg-selected');
                            } else {
                                selectedSeats.push({ seat_id: seat.id, row: row.number, seat: seat.number, price: price });
                                cell.classList.add('bg-selected');
                            }
                            document.getElementById('btnContinue').disabled = selectedSeats.length === 0;
                        });
                    }
                    seatGroup.appendChild(cell);
                }
            });
            rowWrapper.appendChild(seatGroup);
            rowWrapper.appendChild(label.cloneNode(true));
            container.appendChild(rowWrapper);
        });

        document.getElementById('btnContinue').addEventListener('click', () => {
            document.getElementById('summarySeatsList').innerHTML = selectedSeats.map(s => `Ряд ${s.row}, Место ${s.seat}`).join('<br>');
            document.getElementById('summaryPrice').innerText = selectedSeats.reduce((sum, s) => sum + s.price, 0);
            document.getElementById('modal').classList.remove('hidden');
            
            let time = 600;
            timerInterval = setInterval(() => {
                time--;
                let m = Math.floor(time/60), s = time%60;
                document.getElementById('timerDisplay').innerText = `Бронь активна: ${m}:${s < 10 ? '0'+s : s}`;
                if(time <= 0) hideModal();
            }, 1000);
        });

        window.hideModal = () => {
            clearInterval(timerInterval);
            document.getElementById('modal').classList.add('hidden');
        };

        document.getElementById('bookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Обработка...';

            try {
                const response = await fetch("{{ route('client.book', $concert->id) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        customer_name: e.target.customer_name.value,
                        customer_email: e.target.customer_email.value,
                        customer_phone: e.target.customer_phone.value,
                        // Отправляем row и seat, которые требует ваша таблица в БД
                        seats: selectedSeats.map(s => ({ row: s.row, seat: s.seat, price: s.price }))
                    })
                });

                const res = await response.json();

                if(res.success) {
                    document.getElementById('successMsg').classList.remove('hidden');
                    document.getElementById('formActions').classList.add('hidden');
                    document.getElementById('closeBtn').classList.remove('hidden');
                    document.getElementById('timerDisplay').classList.add('hidden');
                } else {
                    alert('Ошибка: ' + (res.message || 'Не удалось забронировать'));
                }
            } catch (err) {
                console.error('Ошибка:', err);
                alert('Произошла критическая ошибка.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Забронировать';
            }
        });
    });
</script>
</body>
</html>