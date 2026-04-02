<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Section;
use Illuminate\Database\Seeder;

class DemoEventSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::query()->updateOrCreate(
            ['slug' => 'summer-jazz-night-2026'],
            [
                'name' => 'Summer Jazz Night 2026',
                'description' => "An evening of live jazz on the waterfront.\n\nDoors open at 6:30 PM. Show starts at 8:00 PM.",
                'starts_at' => now()->addMonths(2)->setTime(20, 0),
                'location' => 'Riverside Amphitheater, Example City',
            ]
        );

        $sections = [
            ['name' => 'General admission', 'price' => 45.00, 'capacity' => 200, 'sort_order' => 1],
            ['name' => 'Reserved seating', 'price' => 85.00, 'capacity' => 80, 'sort_order' => 2],
            ['name' => 'VIP front row', 'price' => 150.00, 'capacity' => 30, 'sort_order' => 3],
        ];

        foreach ($sections as $row) {
            Section::query()->updateOrCreate(
                [
                    'event_id' => $event->id,
                    'name' => $row['name'],
                ],
                [
                    'price' => $row['price'],
                    'capacity' => $row['capacity'],
                    'sort_order' => $row['sort_order'],
                    'sold' => 0,
                    'held' => 0,
                ]
            );
        }
    }
}
