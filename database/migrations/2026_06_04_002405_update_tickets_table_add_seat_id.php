<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Добавляем связь с таблицей seats
            // Это решит вашу ошибку Column not found
            $table->foreignId('seat_id')->nullable()->after('concert_id')->constrained('seats')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['seat_id']);
            $table->dropColumn('seat_id');
        });
    }
};