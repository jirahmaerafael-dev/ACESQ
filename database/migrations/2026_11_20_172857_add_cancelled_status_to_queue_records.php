<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_records', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'called', 'present', 'completed', 'forfeited', 'cancelled'])
                ->default('waiting')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('queue_records')
            ->where('status', 'cancelled')
            ->update(['status' => 'forfeited']);

        Schema::table('queue_records', function (Blueprint $table) {
            $table->enum('status', ['waiting', 'called', 'present', 'completed', 'forfeited'])
                ->default('waiting')
                ->change();
        });
    }
};
