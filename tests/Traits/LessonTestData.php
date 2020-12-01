<?php

namespace Tests\Traits;

use App\Models\User;

trait LessonTestData
{
    private function data()
    {
        return new class {
            private $data;

            public function __construct()
            {
                $this->data = [
                    'register' => 'Example lesson register',
                    'presenceList' => [
                        User::factory()->create()->id => [
                            'presence' => 1,
                            'observation' => 'test observation',
                        ],
                        User::factory()->create()->id => [
                            'presence' => 0,
                        ],
                    ],
                ];
            }

            public function lesson($lesson)
            {
                $presenceList = $lesson->novices->reduce(function ($presenceList, $novice) {
                    $presenceList[$novice->id] = 3;
                    return $presenceList;
                }, []);
                $this->change('presenceList', $presenceList);
                return $this;
            }

            public function exclude($key)
            {
                unset($this->data[$key]);
                return $this;
            }

            public function change($key, $value)
            {
                $this->data[$key] = $value;
                return $this;
            }

            public function add($key, $value)
            {
                $this->data[$key] = $value;
                return $this;
            }

            public function get(){
                return $this->data;
            }
        };
    }
}
