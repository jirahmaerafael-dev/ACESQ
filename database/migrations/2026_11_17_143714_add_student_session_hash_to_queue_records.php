<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queue_records', function (Blueprint $table) {
            $table->string('student_session_hash', 64)
                ->nullable()
                ->index()
                ->after('ticket_prefix');
        });
    }

    public function down(): void
    {
        Schema::table('queue_records', function (Blueprint $table) {
            $table->dropIndex(['student_session_hash']);
            $table->dropColumn('student_session_hash');
        });
    }
};
