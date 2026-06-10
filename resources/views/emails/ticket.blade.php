<div style="font-family: sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px; color: #18181b;">
    <h2 style="color: #18181b; font-size: 24px;">Здравствуйте, {{ $customerName }}!</h2>
    <p style="font-size: 16px;">Ваши билеты успешно забронированы.</p>

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #4b5563; font-size: 16px; text-transform: uppercase; letter-spacing: 0.05em;">Детали мероприятия</h3>
        <p style="margin: 8px 0; font-size: 16px;"><strong>Мероприятие:</strong> {{ $concert->title }}</p>
        <p style="margin: 8px 0; font-size: 16px;"><strong>Площадка:</strong> {{ $concert->hall->name ?? 'Не указано' }}</p>
        <p style="margin: 8px 0; font-size: 16px;"><strong>Адрес:</strong> {{ $concert->hall->address ?? 'Не указано' }}</p>
        <p style="margin: 8px 0; font-size: 16px;"><strong>Дата и время:</strong> {{ \Carbon\Carbon::parse($concert->date_time)->format('d.m.Y H:i') }}</p>
    </div>

    <div style="margin: 20px 0;">
        {{-- Номер заказа один для всей группы --}}
        <div style="margin-bottom: 15px;">
            <span style="font-size: 16px; color: #4b5563;">Номер заказа: </span>
            <span style="font-size: 22px; font-weight: bold; color: #18181b; display: block;"># {{ $tickets->first()->ticket_code }}</span>
        </div>

        <h3 style="color: #18181b; font-size: 18px; border-bottom: 2px solid #eef2ff; padding-bottom: 5px;">Места:</h3>
        <ul style="padding: 0; list-style: none; margin: 0;">
            @foreach($tickets as $ticket)
                <li style="padding: 8px 0; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 16px;">
                    Ряд <strong>{{ $ticket->row }}</strong>, Место <strong>{{ $ticket->seat }}</strong>
                </li>
            @endforeach
        </ul>
    </div>

    <div style="border-top: 2px solid #f3f4f6; padding-top: 15px; margin-top: 20px;">
        <p style="margin: 0 0 5px 0; font-weight: bold; color: #374151; font-size: 16px;">Комментарий организатора:</p>
        <p style="color: #52525b; font-style: italic; margin: 0; font-size: 15px;">
            {{ $concert->payment_info ?? 'Нет комментариев' }}
        </p>
    </div>
</div>