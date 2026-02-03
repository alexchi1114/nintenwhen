<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DirectTag;

class DirectTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['code' => 'nintendo-direct', 'display_name' => 'Nintendo Direct', 'color_hex' => 'E60012', 'display_order' => 1],
            ['code' => 'nintendo-direct-mini', 'display_name' => 'Nintendo Direct Mini', 'color_hex' => 'F57C01', 'display_order' => 2],
            ['code' => 'partner-showcase', 'display_name' => 'Partner Showcase', 'color_hex' => '5B9BD5', 'display_order' => 3],
            ['code' => 'indie-direct', 'display_name' => 'Indie World', 'color_hex' => '7AC74F', 'display_order' => 4],
            ['code' => 'game-specific', 'display_name' => 'Game-Specific', 'color_hex' => '9B59B6', 'display_order' => 5],
        ];

        foreach ($tags as $tag) {
            DirectTag::updateOrCreate(
                ['code' => $tag['code']],
                $tag
            );
        }
    }
}
