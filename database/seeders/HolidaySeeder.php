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
        $holidays = $this->nationalHolidays()
                         ->concat($this->araraquaraHolidays());

        $holidays->each(function ($holiday) {
            Holiday::factory()->create([
                'name'  => $holiday->name,
                'date'  => $this->date($holiday)->toDateString(),
                'local' => isset($holiday->local) ? $holiday->local : null,
            ]);
        });
    }

    private function nationalHolidays()
    {
       return collect([
            (object) [
                'name'  => 'dia da confraternização universal',
                'day'   => 1,
                'month' => 1,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'segunda-feira de carnaval',
                'day'   => 1,
                'month' => 1,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'carnaval',
                'day'   => 16,
                'month' => 2,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'quarta-feira de cinzas',
                'day'   => 17,
                'month' => 2,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'sexta-feira santa',
                'day'   => 2,
                'month' => 4,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'tiradentes',
                'day'   => 21,
                'month' => 4,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'dia do trabalho',
                'day'   => 1,
                'month' => 5,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'dia da independência do brasil',
                'day'   => 7,
                'month' => 9,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'nossa senhora aparecida',
                'day'   => 12,
                'month' => 10,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'finados',
                'day'   => 2,
                'month' => 11,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'problamação da república',
                'day'   => 15,
                'month' => 11,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'natal',
                'day'   => 25,
                'month' => 12,
                'year'  => 2021,
            ],
            (object) [
                'name'  => 'dia da confraternização universal',
                'day'   => 1,
                'month' => 1,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'segunda-feira de carnaval',
                'day'   => 28,
                'month' => 2,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'carnaval',
                'day'   => 1,
                'month' => 3,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'quarta-feira de cinzas',
                'day'   => 2,
                'month' => 3,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'sexta-feira santa',
                'day'   => 15,
                'month' => 4,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'tiradentes',
                'day'   => 21,
                'month' => 4,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'dia do trabalho',
                'day'   => 1,
                'month' => 5,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'dia da independência do brasil',
                'day'   => 7,
                'month' => 9,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'nossa senhora aparecida',
                'day'   => 12,
                'month' => 10,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'finados',
                'day'   => 2,
                'month' => 11,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'problamação da república',
                'day'   => 15,
                'month' => 11,
                'year'  => 2022,
            ],
            (object) [
                'name'  => 'natal',
                'day'   => 25,
                'month' => 12,
                'year'  => 2022,
            ],
        ]);
    }

    private function araraquaraHolidays()
    {
       return collect([
            (object) [
                'name'  => 'aniversário de araraquara',
                'day'   => 22,
                'month' => 8,
                'year'  => 2021,
                'local' => 'araraquara',
            ],
            (object) [
                'name'  => 'dia da consciência negra',
                'day'   => 20,
                'month' => 11,
                'year'  => 2021,
                'local' => 'araraquara',
            ],
            (object) [
                'name'  => 'aniversário de araraquara',
                'day'   => 22,
                'month' => 8,
                'year'  => 2022,
                'local' => 'araraquara',
            ],
            (object) [
                'name'  => 'dia da consciência negra',
                'day'   => 20,
                'month' => 11,
                'year'  => 2022,
                'local' => 'araraquara',
            ],
       ]);
    }

    private function date($holiday)
    {
        return Carbon::create($holiday->year, $holiday->month, $holiday->day);
    }
}
