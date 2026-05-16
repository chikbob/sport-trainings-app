<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->migrateForSqlite();

            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE registrations
            INNER JOIN participants ON participants.id = registrations.participant_id
            SET registrations.user_id = participants.user_id
        ');

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['participant_id']);
            $table->dropColumn('participant_id');
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            $this->rollbackForSqlite();

            return;
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('participant_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE registrations
            INNER JOIN participants ON participants.user_id = registrations.user_id
            SET registrations.participant_id = participants.id
        ');

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    private function migrateForSqlite(): void
    {
        Schema::create('registrations_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending',
                'approved',
                'cancelled',
                'rejected',
                'attended',
                'no_show',
            ])->default('pending');
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO registrations_new (id, user_id, training_id, status, created_at, updated_at)
            SELECT registrations.id, participants.user_id, registrations.training_id, registrations.status, registrations.created_at, registrations.updated_at
            FROM registrations
            INNER JOIN participants ON participants.id = registrations.participant_id
        ');

        Schema::drop('registrations');
        Schema::rename('registrations_new', 'registrations');
    }

    private function rollbackForSqlite(): void
    {
        Schema::create('registrations_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending',
                'approved',
                'cancelled',
                'rejected',
                'attended',
                'no_show',
            ])->default('pending');
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO registrations_old (id, participant_id, training_id, status, created_at, updated_at)
            SELECT registrations.id, participants.id, registrations.training_id, registrations.status, registrations.created_at, registrations.updated_at
            FROM registrations
            INNER JOIN participants ON participants.user_id = registrations.user_id
        ');

        Schema::drop('registrations');
        Schema::rename('registrations_old', 'registrations');
    }
};
