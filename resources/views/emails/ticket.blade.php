<div style="font-family: sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px; color: #18181b;">
    <h2 style="color: #18181b;">Здравствуйте, {{ $customerName }}!</h2>
    <p>Ваши билеты успешно забронированы.</p>

    <div style="background-color: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #4b5563; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">Детали мероприятия</h3>
        <p style="margin: 5px 0;"><strong>Мероприятие:</strong> {{ $concert->title }}</p>
        <p style="margin: 5px 0;"><strong>Площадка:</strong> {{ $concert->hall->name ?? 'Не указано' }}</p>
        <p style="margin: 5px 0;"><strong>Адрес:</strong> {{ $concert->hall->address ?? 'Не указано' }}</p>
        <p style="margin: 5px 0;"><strong>Дата и время:</strong> {{ \Carbon\Carbon::parse($concert->date_time)->format('d.m.Y H:i') }}</p>
    </div>

    <div style="margin: 20px 0;">
        <h3 style="color: #18181b;">Номера ваших билетов:</h3>
        <ul style="background: #eef2ff; padding: 15px 30px; border-radius: 8px; list-style: none; margin: 0;">
            @foreach($ticketNumbers as $number)
                <li style="font-size: 20px; font-weight: bold; color: #4338ca; margin-bottom: 5px;"># {{ $number }}</li>
            @endforeach
        </ul>
    </div>

    {{-- Важный блок с комментарием организатора --}}
    <div style="border-top: 2px solid #f3f4f6; padding-top: 15px; margin-top: 20px;">
        <p style="margin: 0 0 5px 0; font-weight: bold; color: #374151;">Комментарий организатора:</p>
        <p style="color: #52525b; font-style: italic; margin: 0;">
            {{ $concert->payment_info ?? 'Нет комментариев' }}
        </p>
    </div>
</div>