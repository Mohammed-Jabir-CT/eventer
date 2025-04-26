<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            'Meeting',
            'Celebration',
            'Seminar'
        ];

        EventType::insert(array_map(function ($eventType) {
            return ['name' => $eventType];
        }, $eventTypes));
    }
}
