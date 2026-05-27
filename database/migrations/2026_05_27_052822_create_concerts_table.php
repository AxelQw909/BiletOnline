<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Организатор
            $table->foreignId('hall_id')->constrained()->onDelete('cascade'); // Зал
            $table->string('title');
            $table->string('dk_title')->nullable(); // От какого ДК
            $table->dateTime('date_time');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->json('custom_prices')->nullable(); // Исключения по ценам для конкретных мест
            $table->text('payment_info')->nullable(); // Инфо об оплате
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concerts');
    }
};