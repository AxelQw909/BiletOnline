<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Concert;
use App\Models\Hall;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerController extends Controller
{
    // --- ПРОФИЛЬ И ОБНОВЛЕНИЕ ---
    public function profile()
    {
        $organizer = Auth::user();
        $concerts = Concert::where('user_id', $organizer->id)->get();
        return view('organizer.profile', compact('organizer', 'concerts'));
    }

    public function update(Request $request, $id)
    {
        $organizer = User::findOrFail($id);
        $organizer->update($request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]));
        return back()->with('success', 'Профиль обновлен');
    }

    // --- ПЛОЩАДКИ И АРЕНДА ---
    public function platforms() 
    {
        $partners = User::has('halls')->with('halls')->get();
        return view('organizer.platforms', compact('partners'));
    }

    public function rentHall($id)
    {
        $hall = Hall::findOrFail($id);
        $bookedDates = Concert::where('hall_id', $id)
                            ->where('status', 'approved')
                            ->pluck('date_time')
                            ->toArray();
        return view('organizer.rent_form', compact('hall', 'bookedDates'));
    }

    public function submitRent(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'date_time' => 'required|date',
            'base_price' => 'required|numeric',
            'payment_info' => 'nullable|string', 
            'custom_prices' => 'nullable|json',
        ]);

        Concert::create([
            'user_id'       => Auth::id(),
            'hall_id'       => $id,
            'title'         => $data['title'],
            'date_time'     => $data['date_time'],
            'payment_info'   => $data['payment_info'], 
            'base_price'     => $data['base_price'],
            'custom_prices'  => isset($data['custom_prices']) ? json_decode($data['custom_prices'], true) : null,
            'status'        => 'pending',
        ]);

        return redirect()->route('organizer.profile')->with('success', 'Заявка на аренду отправлена!');
    }

    // --- ОСТАЛЬНЫЕ СТРАНИЦЫ ---
    public function concerts() 
    { 
        $concerts = Concert::where('user_id', Auth::id())
                           ->where('status', 'approved')
                           ->with('tickets') 
                           ->get();
        return view('organizer.concerts', compact('concerts')); 
    }

    public function viewConcert($id)
    {
        $concert = Concert::where('user_id', Auth::id())->findOrFail($id);
        return view('organizer.concert_view', compact('concert'));
    }

    public function getSeatsStatus($id)
    {
        $concert = Concert::where('user_id', Auth::id())->findOrFail($id);
        $tickets = Ticket::where('concert_id', $id)
            ->get(['seat_id', 'status']); 
        
        return response()->json($tickets);
    }

    public function ticketsSearch(Request $request) 
    { 
        $concerts = Concert::where('user_id', Auth::id())->get();
        $selectedConcertId = $request->query('concert_id');
        $searchQuery = $request->query('search');
        
        $tickets = collect();

        if ($selectedConcertId) {
            $query = Ticket::where('concert_id', $selectedConcertId);
            
            if ($searchQuery) {
                $query->where(function($q) use ($searchQuery) {
                    $q->where('ticket_code', 'like', "%{$searchQuery}%")
                    ->orWhere('customer_name', 'like', "%{$searchQuery}%")
                    ->orWhere('customer_email', 'like', "%{$searchQuery}%");
                });
            }
            
            $tickets = $query->get();
        }

        return view('organizer.tickets_search', compact('concerts', 'selectedConcertId', 'searchQuery', 'tickets')); 
    }

    public function updateTicketStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->concert->user_id !== Auth::id()) {
            abort(403, 'У вас нет прав для изменения этого билета.');
        }

        $request->validate([
            'status' => 'required|in:pending,paid'
        ]);

        $ticket->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Статус билета успешно обновлен!');
    }

    // НОВЫЙ МЕТОД ДЛЯ МАССОВОГО ОБНОВЛЕНИЯ
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tickets,id'
        ]);

        // Проверка прав на все билеты
        $tickets = Ticket::whereIn('id', $request->ids)->get();
        foreach($tickets as $ticket) {
            if ($ticket->concert->user_id !== Auth::id()) {
                abort(403, 'У вас нет прав для изменения одного из билетов.');
            }
        }

        Ticket::whereIn('id', $request->ids)->update(['status' => 'paid']);

        return response()->json(['success' => true, 'message' => 'Статус билетов обновлен']);
    }

    public function calendar()
    {
        return view('organizer.calendar');
    }

    public function completedConcerts()
    {
        $concerts = Concert::where('date_time', '<', now())
            ->with(['hall.partner', 'tickets'])
            ->get()
            ->map(function ($concert) {
                $sold = $concert->tickets->where('status', 'sold')->count();
                $total = $concert->hall->capacity ?? 0;
                $price = $concert->ticket_price ?? 0;
                
                return [
                    'id' => $concert->id,
                    'title' => $concert->title,
                    'hall' => $concert->hall->name ?? 'Без зала',
                    'partner' => $concert->hall->partner->company_name ?? 'Без площадки',
                    'date' => $concert->date_time,
                    'sold' => $sold,
                    'revenue' => $sold * $price,
                    'unsold' => max(0, $total - $sold)
                ];
            });

        return view('organizer.completed', compact('concerts'));
    }
}