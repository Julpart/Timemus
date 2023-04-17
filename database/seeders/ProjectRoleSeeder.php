<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            DB::table('project_role')->insert([
                [
                    'name' => 'Admin',
                    'id' => '2',
                ],
                [
                    'name' => 'User',
                    'id' => '1',
                ],
                    ]
            );
    }
}
