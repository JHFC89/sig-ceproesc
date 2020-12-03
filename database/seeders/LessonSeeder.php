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
        // Lessons for instructor
        $lessons = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'discipline' => 'português',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->forTomorrow()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'discipline' => 'administração',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->forYesterday()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'discipline' => 'financeiro',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->lastWeek()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'discipline' => 'inglês',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        Lesson::factory()
            ->nextWeek()
            ->notRegistered()
            ->hasNovices(10)
            ->count(3)
            ->create([
                'discipline' => 'ética',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);
        
        // Lessons for instructor 2
        Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->hasNovices(10)
            ->count(2)
            ->create([
                'instructor_id' => User::where('email', 'instrutor2@sig.com.br')->first(),
                'discipline' => 'matemática',
            ]);

        $lessons->first()->enroll(User::where('email', 'aprendiz@sig.com.br')->first());
        $lessons->first()->enroll(User::where('email', 'aprendiz2@sig.com.br')->first());
        $lessons->first()->enroll(User::where('email', 'aprendiz3@sig.com.br')->first());
    }
}
