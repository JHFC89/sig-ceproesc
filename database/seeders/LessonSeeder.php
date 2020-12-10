<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lesson;
use App\Models\CourseClass;
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
        $lessonForToday = Lesson::factory()
            ->forToday()
            ->notRegistered()
            ->create([
                'discipline' => 'português',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        $lessonForYesterday = Lesson::factory()
            ->forYesterday()
            ->notRegistered()
            ->create([
                'discipline' => 'administração',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        $lessonForTomorrow = Lesson::factory()
            ->forTomorrow()
            ->notRegistered()
            ->create([
                'discipline' => 'finanças',
                'instructor_id' => User::where('email', 'instrutor@sig.com.br')->first(),
            ]);

        CourseClass::where('name', 'janeiro - 2020')->first()->novices->each(function ($novice) use ($lessonForToday, $lessonForYesterday, $lessonForTomorrow) {
            $lessonForToday->enroll($novice);
            $lessonForYesterday->enroll($novice);
            $lessonForTomorrow->enroll($novice);
        });

        CourseClass::where('name', 'julho - 2020')->first()->novices->each(function ($novice) use ($lessonForToday, $lessonForYesterday, $lessonForTomorrow) {
            $lessonForToday->enroll($novice);
            $lessonForYesterday->enroll($novice);
            $lessonForTomorrow->enroll($novice);
        });

        CourseClass::where('name', 'janeiro - 2021')->first()->novices->each(function ($novice) use ($lessonForToday, $lessonForYesterday, $lessonForTomorrow) {
            $lessonForToday->enroll($novice);
            $lessonForYesterday->enroll($novice);
            $lessonForTomorrow->enroll($novice);
        });
    }
}
