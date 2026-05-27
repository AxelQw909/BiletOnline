<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concert_id')->constrained();
            $table->string('row');
            $table->string('seat');
            $table->string('ticket_code'); 
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone'); // Добавили отсутствующее поле
            $table->string('status')->default('pending');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};