<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hall;
use App\Models\Concert;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $generateSchema = function($rows, $seatsInRow, $aisleColumn) {
            $schema = [];
            for ($r = 1; $r <= $rows; $r++) {
                for ($s = 1; $s <= $seatsInRow; $s++) {
                    $schema[] = ['row' => $r, 'seat' => $s, 'type' => ($s == $aisleColumn) ? 'aisle' : 'seat'];
                }
            }
            return $schema;
        };

        // --- 1. ПАРТНЕРЫ (Челябинские ДК) ---
        $partnersData = [
            ['name' => 'Директор ДК ЖД', 'email' => 'dkzd@mail.ru', 'company' => 'ДК Железнодорожников', 'address' => 'ул. Цвиллинга, 54'],
            ['name' => 'Администратор Театра Драмы', 'email' => 'drama@mail.ru', 'company' => 'Театр драмы им. Наума Орлова', 'address' => 'пл. Революции, 6'],
            ['name' => 'Директор ДК ЧМК', 'email' => 'dkcmk@mail.ru', 'company' => 'ДК ЧМК', 'address' => 'ул. Ярослава Гашека, 1'],
            ['name' => 'Администратор Gold Arena', 'email' => 'gold@gmail.com', 'company' => 'Gold Event Hall', 'address' => 'ул. Ленина, 10']
        ];

        $halls = [];
        foreach ($partnersData as $data) {
            $user = User::create([
                'name' => $data['name'], 'email' => $data['email'],
                'password' => Hash::make('password'), 'role' => 'partner',
                'company_name' => $data['company'], 'address' => $data['address'], 'phone' => '+7351' . rand(1000000, 9999999)
            ]);
            $halls[] = Hall::create(['user_id' => $user->id, 'name' => 'Основной зал ' . $data['company'], 'address' => $data['address'], 'capacity' => 200, 'schema' => $generateSchema(10, 20, 10)]);
        }

        // --- 2. ОРГАНИЗАТОР ---
        $org = User::create([
            'name' => 'Алексей Иванов', 'email' => 'org@gmail.com',
            'password' => Hash::make('password'), 'role' => 'organizer',
            'company_name' => 'ООО Творческий Союз', 'phone' => '+79001112233'
        ]);

        // --- 3. КОНЦЕРТЫ (Много данных для отчетов) ---
        $paymentMethods = ['Оплата онлайн', 'Оплата в кассе на месте', 'Оплата переводом по номеру'];
        $concertsData = [
            ['title' => 'Отчетный концерт ансамбля "Урал"', 'date' => now()->subDays(20), 'status' => 'completed'],
            ['title' => 'Концерт коллектива Фантазия', 'date' => now()->subDays(5), 'status' => 'completed'],
            ['title' => 'Концерт детской филармонии', 'date' => now()->subDays(2), 'status' => 'completed'],
            ['title' => 'Детский фестиваль "Лучики"', 'date' => now()->subDays(1), 'status' => 'completed'],
            ['title' => 'Большой весенний бал', 'date' => now()->addDays(5), 'status' => 'approved'],
            
        ];

        foreach ($concertsData as $index => $cData) {
            $concert = Concert::create([
                'user_id' => $org->id,
                'hall_id' => $halls[array_rand($halls)]->id,
                'title' => $cData['title'],
                'date_time' => $cData['date'],
                'status' => $cData['status'],
                'base_price' => rand(500, 2000),
                'payment_info' => $paymentMethods[array_rand($paymentMethods)]
            ]);

            // Генерируем 5-10 билетов на каждый концерт
            for ($i = 0; $i < rand(5, 10); $i++) {
                Ticket::create([
                    'concert_id' => $concert->id,
                    'ticket_code' => 'TKT-' . rand(1000, 9999),
                    'row' => rand(1, 5), 'seat' => rand(1, 20),
                    'price' => $concert->base_price,
                    'customer_name' => 'Клиент ' . $i,
                    'customer_email' => 'client' . $i . '@mail.ru',
                    'customer_phone' => '+79000000000',
                    'status' => 'sold'
                ]);
            }
        }
    }
}