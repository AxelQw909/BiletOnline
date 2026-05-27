<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Экспорт отчета по конкретному концерту в PDF.
     */
    public function exportPdf($id)
    {
        $concert = Concert::with(['hall.partner', 'tickets'])->findOrFail($id);

        $soldTickets = $concert->tickets->where('status', 'sold');
        $soldCount = $soldTickets->count();
        
        $revenue = $soldTickets->sum('price') ?: ($soldCount * $concert->base_price);
        
        $totalCapacity = $concert->hall->capacity ?? 0;
        $unsoldCount = max(0, $totalCapacity - $soldCount);

        $data = [
            'title'   => $concert->title,
            'partner' => $concert->hall->partner->company_name ?? 'Без площадки',
            'hall'    => $concert->hall->name ?? 'Без зала',
            'date'    => $concert->date_time->format('d.m.Y H:i'),
            'sold'    => $soldCount,
            'revenue' => $revenue,
            'unsold'  => $unsoldCount
        ];

        $pdf = Pdf::loadView('pdf.concert-report', ['data' => $data]);
        
        return $pdf->download('report_concert_' . $id . '.pdf');
    }

    /**
     * Список завершенных концертов с авто-обновлением статусов.
     */
    public function completedIndex()
    {
        // 1. Автоматический перевод прошедших концертов в статус 'completed'
        Concert::where('date_time', '<', now())
               ->where('status', '!=', 'completed')
               ->update(['status' => 'completed']);

        // 2. Получение списка завершенных концертов
        $concerts = Concert::where('status', 'completed')
            ->with(['hall.partner', 'tickets'])
            ->latest('date_time')
            ->get()
            ->map(function ($concert) {
                $soldTickets = $concert->tickets->where('status', 'sold');
                $soldCount = $soldTickets->count();
                $totalCapacity = $concert->hall->capacity ?? 0;

                return (object) [
                    'id'      => $concert->id,
                    'title'   => $concert->title,
                    'partner' => $concert->hall->partner->company_name ?? 'Без площадки',
                    'hall'    => $concert->hall->name ?? 'Без зала',
                    'date'    => $concert->date_time->format('d.m.Y H:i'),
                    'sold'    => $soldCount,
                    'revenue' => $soldTickets->sum('price') ?: ($soldCount * $concert->base_price),
                    'unsold'  => max(0, $totalCapacity - $soldCount)
                ];
            });

        return view('organizer.completed', compact('concerts'));
    }
}