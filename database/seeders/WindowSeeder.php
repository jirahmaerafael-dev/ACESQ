<?php

namespace Database\Seeders;

use App\Models\Window;
use Illuminate\Database\Seeder;

class WindowSeeder extends Seeder
{
    public function run(): void
    {
        $windows = [
            'Registrar' => 'R',
            'Cashier' => 'C',
            'Window 3' => 'W3',
            'Window 4' => 'W4',
            'Window 5' => 'W5',
        ];

        foreach ($windows as $windowName => $queuePrefix) {
            Window::query()->firstOrCreate(
                ['window_name' => $windowName],
                [
                    'queue_prefix' => $queuePrefix,
                    'is_active' => true,
                ]
            );
        }
    }
}
