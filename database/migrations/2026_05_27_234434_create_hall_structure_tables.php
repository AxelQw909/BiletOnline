<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ряды
        Schema::create('rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->integer('number');
            $table->timestamps();
        });

        // Места
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('row_id')->constrained()->onDelete('cascade');
            $table->integer('number');
            $table->string('type')->default('seat');
            $table->timestamps();
        });
        
        // Удаляем старое поле schema из halls, если оно больше не нужно
        Schema::table('halls', function (Blueprint $table) {
            $table->dropColumn('schema');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
        Schema::dropIfExists('rows');
    }
};