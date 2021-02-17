<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Holiday::factory()->create([
            'name' => 'dia do trabalho',
            'date' => Carbon::create(2021, 5, 1)->toDateString(),
        ]);

        Holiday::factory()->create([
            'name' => 'dia da criança',
            'date' => Carbon::create(2021, 10, 12)->toDateString(),
        ]);

        Holiday::factory()->create([
            'name' => 'dia da independência',
            'date' => Carbon::create(2021, 9, 7)->toDateString(),
        ]);
    }
}
