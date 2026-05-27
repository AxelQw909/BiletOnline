<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CalendarController; // Добавили импорт

// --- АВТОРИЗАЦИЯ И ГЛАВНАЯ ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Регистрация
Route::get('/register/organizer', [AuthController::class, 'showOrganizerRegister'])->name('register.organizer');
Route::post('/register/organizer', [AuthController::class, 'registerOrganizer'])->name('register.organizer.submit');

Route::get('/register/partner', [AuthController::class, 'showPartnerRegister'])->name('register.partner');
Route::post('/register/partner', [AuthController::class, 'registerPartner'])->name('register.partner.submit');

// --- ОРГАНИЗАТОРЫ ---
Route::prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/profile', [OrganizerController::class, 'profile'])->name('profile');
    Route::post('/profile/update/{id}', [OrganizerController::class, 'update'])->name('profile.update');
    
    Route::get('/platforms', [OrganizerController::class, 'platforms'])->name('platforms');
    Route::get('/concerts', [OrganizerController::class, 'concerts'])->name('concerts');
    Route::get('/tickets-search', [OrganizerController::class, 'ticketsSearch'])->name('tickets.search');
    
    // МАРШРУТ ДЛЯ СМЕНЫ СТАТУСА БИЛЕТА
    Route::post('/tickets/{id}/status', [OrganizerController::class, 'updateTicketStatus'])->name('tickets.status');
    
    // Аренда
    Route::get('/hall/rent/{id}', [OrganizerController::class, 'rentHall'])->name('hall.rent');
    Route::post('/hall/rent/{id}/submit', [OrganizerController::class, 'submitRent'])->name('hall.rent.submit');

    // Просмотр концерта и статус мест (для реального времени)
    Route::get('/concert/view/{id}', [OrganizerController::class, 'viewConcert'])->name('concert.view');
    Route::get('/concert/{id}/seats', [OrganizerController::class, 'getSeatsStatus'])->name('concert.seats');
    Route::get('/concert/{id}/seats-data', [OrganizerController::class, 'getSeatsData'])->name('concert.seats.data');

    Route::get('/calendar', [OrganizerController::class, 'calendar'])->name('calendar');
});

// --- ПАРТНЕРЫ ---
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('/profile', [PartnerController::class, 'profile'])->name('profile');
    
    // ИСПРАВЛЕНО: теперь имя будет partner.profile.update
    Route::post('/profile/update/{id}', [PartnerController::class, 'update'])->name('profile.update');
    
    Route::get('/requests', [PartnerController::class, 'requests'])->name('requests');
    
    // Статус заявки
    Route::post('/requests/{id}/status', [PartnerController::class, 'updateStatus'])->name('requests.status');
    
    // Работа с залами
    Route::get('/hall/create', [PartnerController::class, 'createHall'])->name('hall.create');
    Route::post('/hall/store', [PartnerController::class, 'storeHall'])->name('hall.store');
    Route::get('/hall/edit/{id}', [PartnerController::class, 'editHall'])->name('hall.edit');
    Route::post('/hall/update/{id}', [PartnerController::class, 'updateHall'])->name('hall.update');
});

// --- ПУБЛИЧНЫЕ МАРШРУТЫ (Клиенты) ---
Route::get('/concert/{id}', [ClientController::class, 'show'])->name('client.concert');
Route::post('/concert/{id}/book', [ClientController::class, 'bookTicket'])->name('client.book');

// --- КАЛЕНДАРЬ ЗАНЯТОСТИ ---
Route::get('/api/concerts', [CalendarController::class, 'getConcerts'])->name('api.concerts');