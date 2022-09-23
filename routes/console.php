<?php

use App\Models\{Registration, Role};
use MattDaneshvar\Survey\Models\Question;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('invite-admin {name} {email}', function ($name, $email) {
        $registration = Registration::create([
            'name'      => $name,
            'role_id'   => Role::whereRole(Role::ADMIN)->id,
        ]);

        $registration->sendInvitationEmail($email);
})->purpose('Invite a new admin to create an account');

Artisan::command('candidate-form:add-option-to-question {question_id} {option}', function ($question_id, $option) {
    $question = Question::find($question_id);
    $options = $question->options;
    array_push($options, $option);
    $question->options = $options;

    $question->save();
})->purpose('Add option to a question by passing it´s ID and the new option');
