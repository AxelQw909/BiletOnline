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
        // Вспомогательная функция для генерации схемы зала
        $generateSchema = function($rows, $seatsInRow, $aisleColumn) {
            $schema = [];
            for ($r = 1; $r <= $rows; $r++) {
                for ($s = 1; $s <= $seatsInRow; $s++) {
                    $type = ($s == $aisleColumn) ? 'aisle' : 'seat';
                    $schema[] = ['row' => $r, 'seat' => $s, 'type' => $type];
                }
            }
            return $schema;
        };

        // 1. МИДиС (Главный партнер)
        $midis = User::create([
            'name' => 'Администрация МИДиС',
            'email' => 'midis@partner.com',
            'password' => Hash::make('password'),
            'role' => 'partner',
            'company_name' => 'Образовательный комплекс МИДиС',
            'address' => 'ул. Ворошилова, д. 4',
            'phone' => '+73512251010'
        ]);

        // Список челябинских площадок
        $partnersData = [
            ['name' => 'Администратор филармонии', 'email' => 'filarmonia@partner.com', 'company' => 'Челябинская государственная филармония', 'address' => 'ул. Труда, 92А'],
            ['name' => 'Администратор ДК ЖД', 'email' => 'dkzd@partner.com', 'company' => 'ДК Железнодорожников', 'address' => 'ул. Цвиллинга, 54'],
            ['name' => 'Администратор Театра Драмы', 'email' => 'drama@partner.com', 'company' => 'Театр драмы им. Наума Орлова', 'address' => 'пл. Революции, 6'],
            ['name' => 'Администратор МТС Live Холл', 'email' => 'mts@partner.com', 'company' => 'МТС Live Холл', 'address' => 'ул. Труда, 181'],
            ['name' => 'Администратор ДК ЧМК', 'email' => 'dkcmk@partner.com', 'company' => 'ДК ЧМК', 'address' => 'ул. Ярослава Гашека, 1'],
        ];

        $partners = [$midis];
        foreach ($partnersData as $data) {
            $partners[] = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'partner',
                'company_name' => $data['company'],
                'address' => $data['address'],
                'phone' => '+7351' . rand(1000000, 9999999)
            ]);
        }

        // 2. Создаем залы
        $midisBig = Hall::create([
            'user_id' => $midis->id,
            'name' => 'Конгресс-холл МИДиС',
            'address' => 'ул. Ворошилова, 4',
            'capacity' => 440,
            'schema' => $generateSchema(20, 23, 12)
        ]);

        foreach ($partners as $partner) {
            if ($partner->id === $midis->id) continue;
            Hall::create([
                'user_id' => $partner->id,
                'name' => 'Основной зал ' . $partner->company_name,
                'address' => $partner->address,
                'capacity' => 200,
                'schema' => $generateSchema(10, 20, 10)
            ]);
        }

        // 3. Создаем Организаторов
        $org1 = User::create([
            'name' => 'Ивент Агентство "Челябинск-Шоу"',
            'email' => 'chelshow@org.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'company_name' => 'ООО Челябинск-Шоу',
            'phone' => '+79510001122'
        ]);

        // 4. Добавляем концерт
        $concert = Concert::create([
            'user_id' => $org1->id,
            'hall_id' => $midisBig->id,
            'title' => 'Большой весенний концерт в МИДиС',
            'dk_title' => 'Конгресс-холл МИДиС',
            'date_time' => now()->addDays(14)->setTime(18, 0),
            'status' => 'approved',
            'base_price' => 800.00,
            'custom_prices' => [],
            'payment_info' => 'Оплата онлайн или в кассе конгресс-холла'
        ]);

        // Генерируем тестовый билет с обязательным полем customer_phone
        Ticket::create([
            'concert_id'     => $concert->id,
            'ticket_code'    => 'CH74001',
            'row'            => 1,
            'seat'           => 1,
            'price'          => 800.00,
            'customer_name'  => 'Иван Иванов',
            'customer_email' => 'test@example.com',
            'customer_phone' => '+79001234567', // Добавлено поле
            'status'         => 'paid'
        ]);
    }
}