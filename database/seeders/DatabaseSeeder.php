<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            HolidaySeeder::class,
            DisciplineSeeder::class,
            CourseClassSeeder::class,
            CourseSeeder::class,
            UserSeeder::class,
            LessonSeeder::class,
        ]);
    }
}
