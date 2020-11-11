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
                        User::factory()->create()->id => 3,
                        User::factory()->create()->id => 2,
                        User::factory()->create()->id => 1,
                        User::factory()->create()->id => 0,
                    ],
                ];
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

            public function get(){
                return $this->data;
            }
        };
    }
}
