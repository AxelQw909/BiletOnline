@extends('layouts.app')
@section('title', 'Календарь мероприятий — BiletOnline')

@section('content')
<div class="w-full px-6 py-10 bg-zinc-50 min-h-screen">
    <header class="mb-10">
        <h2 class="text-3xl font-extrabold text-zinc-900 tracking-tight uppercase">Календарь мероприятий</h2>
    </header>

    <div class="bg-white p-6 rounded-3xl border border-zinc-100 shadow-sm max-w-5xl mx-auto">
        <div id='calendar' class="text-sm"></div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<style>
    /* Цвет кнопок и заголовка */
    .fc .fc-button-primary { background-color: #18181b !important; border: none !important; color: white !important; }
    .fc .fc-button-primary:hover { background-color: #3f3f46 !important; }
    .fc .fc-toolbar-title { font-weight: 800 !important; text-transform: uppercase; }
    
    /* Стили событий БЕЗ ЧЕРНОГО ФОНА */
    .fc-event { 
        cursor: pointer !important; 
        border: none !important; 
        background-color: transparent !important; /* Убираем фон */
    }
    .fc-event:hover {
        /* При наведении можно добавить легкое изменение, 
           например, подчеркивание или изменение прозрачности,
           но чтобы текст оставался черным. */
        opacity: 0.8 !important;
    }
    .fc .fc-daygrid-event { white-space: normal !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ru',
            height: 'auto',
            aspectRatio: 1.8,
            // Перевод всех кнопок на русский
            buttonText: {
                today:    'Сегодня',
                month:    'Месяц',
                week:     'Неделя',
                day:      'День',
                list:     'Список'
            },
            events: '{{ route("api.concerts") }}',
            headerToolbar: { 
                left: 'prev,next today', 
                center: 'title', 
                right: 'dayGridMonth,timeGridWeek' 
            },
            eventContent: function(arg) {
                let props = arg.event.extendedProps || {};
                let title = arg.event.title || 'Концерт';
                let hall = props.hallName || '---';
                let partner = props.partnerName || '---';
                
                // ТЕКСТ ТЕПЕРЬ ЧЕРНЫЙ И БЕЗ ФОНА
                return {
                    html: `<div style="color: black !important;" class="p-1 text-[12px] leading-tight overflow-hidden">
                            <div class="font-black mb-0.5 uppercase tracking-tighter truncate">${title}</div>
                            <div class="opacity-90 font-medium truncate">${partner}</div>
                            <div class="opacity-80 italic truncate text-[11px]">${hall}</div>
                          </div>`
                };
            }
        });
        calendar.render();
    });
</script>
@endsection