<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('windows', function (Blueprint $table) {
            $table->string('queue_prefix', 8)->nullable()->unique();
        });

        Schema::table('queue_records', function (Blueprint $table) {
            $table->string('student_name')->nullable()->after('window_id');
            $table->string('ticket_prefix', 8)->nullable()->after('student_name');
        });

        $usedPrefixes = [];

        foreach (DB::table('windows')->orderBy('window_id')->get(['window_id', 'window_name']) as $window) {
            if (preg_match('/^window\s*(\d+)/i', $window->window_name, $matches) === 1) {
                $prefix = 'W'.$matches[1];
            } else {
                preg_match('/[A-Z0-9]/', Str::upper(Str::ascii($window->window_name)), $matches);
                $prefix = $matches[0] ?? 'W';
            }

            $basePrefix = $prefix;
            $suffix = 1;
            while (in_array($prefix, $usedPrefixes, true)) {
                $suffixText = (string) $suffix++;
                $prefix = Str::substr($basePrefix, 0, 8 - Str::length($suffixText)).$suffixText;
            }

            DB::table('windows')
                ->where('window_id', $window->window_id)
                ->update(['queue_prefix' => $prefix]);

            DB::table('queue_records')
                ->where('window_id', $window->window_id)
                ->update(['ticket_prefix' => $prefix]);

            $usedPrefixes[] = $prefix;
        }
    }

    public function down(): void
    {
        Schema::table('queue_records', function (Blueprint $table) {
            $table->dropColumn(['student_name', 'ticket_prefix']);
        });

        Schema::table('windows', function (Blueprint $table) {
            $table->dropUnique(['queue_prefix']);
            $table->dropColumn('queue_prefix');
        });
    }
};
