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
            ->count(3)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->forTomorrow()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->forYesterday()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->lastWeek()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->nextWeek()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);
    }
}
