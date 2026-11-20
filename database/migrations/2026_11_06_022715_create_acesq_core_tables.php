<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('windows', function (Blueprint $table) {
            $table->id('window_id');
            $table->string('window_name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('staff_users', function (Blueprint $table) {
            $table->id('staff_id');
            $table->foreignId('window_id')
                ->nullable()
                ->constrained('windows', 'window_id')
                ->nullOnDelete();
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('password_hash');
            $table->enum('role', ['staff', 'admin']);
            $table->timestamps();
        });

        Schema::create('queue_records', function (Blueprint $table) {
            $table->id('queue_id');
            $table->foreignId('window_id')
                ->constrained('windows', 'window_id')
                ->restrictOnDelete();
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');
            $table->unsignedInteger('queue_position');
            $table->enum('status', ['waiting', 'called', 'present', 'completed', 'forfeited'])
                ->default('waiting');
            $table->unsignedTinyInteger('missed_count')->default(0);
            $table->boolean('terms_agreed')->default(false);
            $table->uuid('public_token')->unique();
            $table->timestamp('called_at')->nullable();
            $table->timestamps();
            $table->unique(
                ['window_id', 'queue_date', 'queue_number'],
                'queue_ticket_daily_unique'
            );
            $table->index(
                ['window_id', 'queue_date', 'status', 'queue_position'],
                'queue_window_status_order_idx'
            );
        });

        Schema::create('missed_calls', function (Blueprint $table) {
            $table->id('missed_id');
            $table->foreignId('queue_id')
                ->constrained('queue_records', 'queue_id')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('call_number');
            $table->unsignedInteger('slots_pushed')->nullable();
            $table->timestamp('missed_at');
            $table->unique(['queue_id', 'call_number'], 'missed_call_number_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missed_calls');
        Schema::dropIfExists('queue_records');
        Schema::dropIfExists('staff_users');
        Schema::dropIfExists('windows');
    }
};
