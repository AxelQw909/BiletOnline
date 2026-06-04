<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\User;
use App\Models\Concert;
use App\Models\Row;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    /**
     * Профиль партнера
     */
    public function profile()
    {
        $partner = Auth::user();
        $halls = Hall::where('user_id', $partner->id)->get();
        return view('partner.profile', compact('partner', 'halls'));
    }

    /**
     * Страница заявок
     */
    public function requests()
    {
        $hallIds = Hall::where('user_id', Auth::id())->pluck('id');
        $concerts = Concert::whereIn('hall_id', $hallIds)->get();

        return view('partner.requests', compact('concerts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $concert = Concert::findOrFail($id);
        $request->validate(['status' => 'required|in:approved,rejected']);
        $concert->update(['status' => $request->status]);
        return back()->with('success', 'Статус заявки успешно изменен!');
    }

    public function update(Request $request, $id)
    {
        $partner = User::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
        ]);
        $partner->update($validated);
        return back()->with('success', 'Данные профиля обновлены');
    }

    public function createHall()
    {
        return view('partner.hall_create');
    }

    public function storeHall(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'required|string|max:255',
            'capacity' => 'required|integer',
            'schema'   => 'required|json',
        ]);

        DB::transaction(function () use ($data) {
            $hall = Hall::create([
                'user_id'  => Auth::id(),
                'name'     => $data['name'],
                'address'  => $data['address'],
                'capacity' => $data['capacity'],
            ]);

            $schema = json_decode($data['schema'], true);
            foreach ($schema as $item) {
                $row = Row::firstOrCreate(['hall_id' => $hall->id, 'number' => $item['row']]);
                Seat::create([
                    'row_id' => $row->id,
                    'number' => $item['seat'],
                    'type'   => $item['type']
                ]);
            }
        });

        return redirect()->route('partner.profile')->with('success', 'Зал успешно создан');
    }

    /**
     * Редактирование зала
     */
    public function editHall($id)
    {
        $hall = Hall::where('user_id', Auth::id())->with('rows.seats')->findOrFail($id);
        return view('partner.hall_editor', compact('hall'));
    }

    /**
     * Обновление зала
     */
    public function updateHall(Request $request, $id)
    {
        $hall = Hall::where('user_id', Auth::id())->findOrFail($id);
        
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'required|string|max:255',
            'capacity' => 'required|integer',
            'schema'   => 'required|json',
        ]);

        DB::transaction(function () use ($hall, $data) {
            $hall->update([
                'name'     => $data['name'],
                'address'  => $data['address'],
                'capacity' => $data['capacity'],
            ]);

            // Явное удаление всех связанных мест и рядов перед обновлением
            foreach ($hall->rows as $row) {
                $row->seats()->delete();
            }
            $hall->rows()->delete();
            
            $schema = json_decode($data['schema'], true);
            foreach ($schema as $item) {
                $row = Row::firstOrCreate(['hall_id' => $hall->id, 'number' => $item['row']]);
                Seat::create([
                    'row_id' => $row->id,
                    'number' => $item['seat'],
                    'type'   => $item['type']
                ]);
            }
        });

        return redirect()->route('partner.profile')->with('success', 'Зал успешно обновлен');
    }

    /**
     * Удаление зала
     */
    public function destroy($id)
{
    $hall = Hall::where('user_id', Auth::id())->findOrFail($id);

    // Проверяем, есть ли связанные концерты
    if ($hall->concerts()->exists()) {
        return back()->with('error', 'Этот зал нельзя удалить, так как он уже используется в одном или нескольких концертах.');
    }

    DB::transaction(function () use ($hall) {
        foreach ($hall->rows as $row) {
            $row->seats()->delete();
        }
        $hall->rows()->delete();
        $hall->delete();
    });

    return redirect()->route('partner.profile')->with('success', 'Зал успешно удален');
}
}