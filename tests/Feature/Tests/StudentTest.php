<?php

namespace Tests\Feature\Tests;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StudentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDummy()
    {
        $user = factory(User::class)->create();
        echo $user->role;
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testStudentCanNotSubmitOnDraftStatus() {}
    public function testStudentCanNotSubmitOnPublishedStatus() {}
    public function testStudentCanDraftOnStartedStatus() {}
    public function testStudentCanSubmitOnStartedStatus() {}
    public function testStudentCanDraftOnFinishedStatusInPreparationSeconds() {}
    public function testStudentCanSubmitOnFinishedStatusInPreparationSeconds() {}
    public function testStudentCanNotSubmitOnFinishedStatus() {}

}
