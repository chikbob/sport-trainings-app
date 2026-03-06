<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();

            // Привязываем к users
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
