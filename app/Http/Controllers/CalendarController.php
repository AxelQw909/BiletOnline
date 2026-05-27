<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getConcerts()
    {
        // 1. Убираем ->with, чтобы не падать на ошибках связей
        $concerts = Concert::where('status', 'approved')->get();
        
        $events = $concerts->map(function ($concert) {
            // 2. Используем старый надежный способ получения даты
            // Если формат даты в базе "YYYY-MM-DD", это сработает. 
            // Если дата сложная, оставляем как есть, если она работала раньше.
            $date = $concert->date_time;
            
            return [
                'title' => $concert->title,
                'start' => $date, // Попробуйте сначала просто вернуть переменную из базы
                'url'   => route('organizer.concert.view', $concert->id), // Ваша новая ссылка
                'backgroundColor' => '#18181b',
                'extendedProps' => [
                    // Используем оператор ?. (nullsafe) или старую добрую проверку
                    'hallName'    => $concert->hall ? $concert->hall->name : 'Без зала',
                    'partnerName' => ($concert->hall && $concert->hall->partner) ? $concert->hall->partner->company_name : 'Без площадки'
                ]
            ];
        });

        return response()->json($events);
    }
}