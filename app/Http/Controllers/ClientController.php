<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function show($id)
    {
        $concert = Concert::where('id', $id)
            ->where('status', 'approved')
            ->with(['hall', 'tickets'])
            ->firstOrFail();
            
        return view('client.booking', compact('concert'));
    }

    public function bookTicket(Request $request, $id)
    {
        try {
            $concert = Concert::findOrFail($id);
            
            $data = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
                'seats' => 'required|array',
            ]);

            // 1. Генерация кода
            do {
                $orderCode = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            } while (Ticket::where('ticket_code', $orderCode)->exists());

            // 2. Использование транзакции, чтобы не создавались "битые" билеты
            DB::beginTransaction();
            try {
                foreach ($data['seats'] as $seat) {
                    Ticket::create([
                        'concert_id'     => $concert->id,
                        'ticket_code'    => $orderCode,
                        'row'            => $seat['row'],
                        'seat'           => $seat['seat'],
                        'price'          => $seat['price'],
                        'customer_name'  => $data['customer_name'],
                        'customer_email' => $data['customer_email'],
                        'customer_phone' => $data['customer_phone'],
                        'status'         => 'pending'
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e; // Пробрасываем дальше для логирования
            }

            // 3. Отправка письма
            try {
                Mail::to($data['customer_email'])->send(new TicketCreated([$orderCode], $data['customer_name'], $concert));
            } catch (\Exception $e) {
                Log::error('Ошибка отправки письма: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'ticket_numbers' => [$orderCode]
            ]);

        } catch (\Exception $e) {
            // Если ошибка возникла, вернем сообщение в JSON, чтобы увидеть его в F12
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }
}