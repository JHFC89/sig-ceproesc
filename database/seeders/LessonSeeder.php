<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(10)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);
    }
}
