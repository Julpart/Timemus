<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'test tag',
            'test tag 2',
            'test tag 3',
        ];
        foreach ($tags as $item) {
            DB::table('tags')->insert([
                'name' => $item,
            ]);
        }
    }
}
