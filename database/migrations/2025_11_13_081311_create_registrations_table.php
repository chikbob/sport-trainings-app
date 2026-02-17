<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('participant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('training_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', [
                'pending',    // заявка подана
                'approved',   // підтверджено
                'cancelled',  // скасовано користувачем
                'rejected',   // відхилено адміністратором
                'attended',   // відвідав тренування
                'no_show',    // не зʼявився
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
