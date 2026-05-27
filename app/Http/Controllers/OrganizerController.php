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
            'user_id'        => Auth::id(), // Этого достаточно для привязки организатора
            'hall_id'        => $id,
            'title'          => $data['title'],
            'date_time'      => $data['date_time'],
            'payment_info'   => $data['payment_info'], 
            'base_price'     => $data['base_price'],
            'custom_prices'  => isset($data['custom_prices']) ? json_decode($data['custom_prices'], true) : null,
            'status'         => 'pending',
        ]);

        return redirect()->route('organizer.profile')->with('success', 'Заявка на аренду отправлена!');
    }

    // --- ОСТАЛЬНЫЕ СТРАНИЦЫ ---
    public function concerts() 
    { 
        $concerts = Concert::where('user_id', Auth::id())->where('status', 'approved')->get();
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
        $tickets = Ticket::where('concert_id', $id)->get(['row', 'seat', 'status']);
        
        return response()->json($tickets);
    }

        public function ticketsSearch(Request $request) 
    { 
        $concerts = Concert::where('user_id', Auth::id())->get();
        $selectedConcertId = $request->query('concert_id');
        $searchQuery = $request->query('search');
        
        // Инициализируем пустую коллекцию
        $tickets = collect();

        if ($selectedConcertId) {
            // Ищем билеты по концерту и фильтруем по поисковому запросу (например, номеру билета или имени)
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

        // Проверка: принадлежит ли этот билет концерту, созданному текущим организатором
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
}