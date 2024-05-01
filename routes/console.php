<?php

use App\Models\{AprendizForm, Registration, Role};
use Illuminate\Database\Eloquent\Collection;
use MattDaneshvar\Survey\Models\Question;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Entry;

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

Artisan::command('candidate-form:import-canidate-form-to-new-model', function () {
    $errors = 0;
    Entry::with('answers', 'answers.question')->chunk(50, function (Collection $entries) use (&$errors) {
        $entries->each(function (Entry $entry) use (&$errors) {
            try {
                AprendizForm::importFromOldModel($entry);
            } catch (\Throwable $th) {
                $errors++;
                Log::warning($th->getMessage());
            }
        });
    });
    $this->info('Candidate forms created: ' . AprendizForm::count());
    $this->warn('Errors count: ' . $errors);
})->purpose('Import the old candidate form model data to the new model');

Artisan::command('candidate-form:delete-old-forms', function () {
    $forms = AprendizForm::whereDate('created_at', '<', now()->subYears(1)->format('Y/m/d'))->delete();
    $this->info('Candidate forms deleted: ' . $forms);
})->purpose('Delete forms older than a year');
