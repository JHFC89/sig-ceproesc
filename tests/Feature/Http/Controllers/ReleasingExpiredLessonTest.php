<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Models\RegisterLessonRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReleasingExpiredLessonTest extends TestCase
{
    use RefreshDatabase;

    protected $lesson;

    protected $request;

    protected $coordinator;

    protected function setUp():void
    {
        parent::setUp();

        $this->lesson = Lesson::factory()->expired()->hasRequests(1)->create();
        $this->request = $this->lesson->openRequest();
        $this->coordinator = User::factory()->hasRoles(1, ['name' => 'coordinator'])->create();
    }

    /** @test */
    public function releasing_a_request_for_an_expired_lesson()
    {
        $request = $this->lesson->openRequest();

        $response = $this->actingAs($this->coordinator)->patch(route('requests.update', ['request' => $request]));
        $request->refresh();

        $response
            ->assertOk()
            ->assertViewIs('requests.show')
            ->assertSessionHas('status', 'Aula liberada para registro com sucesso!')
            ->assertSee('Aula liberada para registro com sucesso!');
        $this->assertNotNull($request->released_at);
        $this->assertEquals(now()->format('d-m-Y'), $request->released_at->format('d-m-Y'));
    }

    /** @test */
    public function cannot_release_a_request_that_is_already_released()
    {
        $this->request->release();

        $response = $this->actingAs($this->coordinator)->patch(route('requests.update', ['request' => $this->request]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function guest_cannot_release_a_request()
    {
        $response = $this->patch(route('requests.update', ['request' => $this->request]));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function instrutor_cannot_release_a_request()
    {
        $instructor = User::factory()->hasRoles(1, ['name' => 'instructor'])->create();

        $response = $this->actingAs($instructor)->patch(route('requests.update', ['request' => $this->request]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function novice_cannot_release_a_request()
    {
        $novice = User::factory()->hasRoles(1, ['name' => 'novice'])->create();

        $response = $this->actingAs($novice)->patch(route('requests.update', ['request' => $this->request]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function employer_cannot_release_a_request()
    {
        $employer = User::factory()->hasRoles(1, ['name' => 'employer'])->create();

        $response = $this->actingAs($employer)->patch(route('requests.update', ['request' => $this->request]));

        $response->assertUnauthorized();
    }
}
