<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use Illuminate\Database\Seeder;

class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseClass::factory()->hasNovices(10)->create(['name' => 'janeiro - 2020']);

        CourseClass::factory()->hasNovices(10)->create(['name' => 'julho - 2020']);

        CourseClass::factory()->hasNovices(10)->create(['name' => 'janeiro - 2021']);
    }
}
