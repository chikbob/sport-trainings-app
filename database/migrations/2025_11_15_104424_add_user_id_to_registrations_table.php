<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Добавляем user_id
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();

            // Удаляем participant_id
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Откат (если нужно)
            $table->foreignId('participant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
