<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use MattDaneshvar\Survey\Models\Entry;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use MattDaneshvar\Survey\Models\Answer;

class CandidateSubscriptionController extends Controller
{
    private $form;

    public function __construct()
    {
        $this->form = Subscription::form();
    }

    public function index()
    {
        $this->checkAuthorization();

        $this->setCollectionMacros();

        $entries = $this->getEntries();

        return view('candidate-subscriptions.index', compact('entries'));
    }

    public function show(Entry $entry)
    {
        $this->checkAuthorization();

        $this->setCollectionMacros();

        $sections = $this->form->sections;

        $sections->load('questions');

        $sections = $sections->map(function ($section) {
            return $section->questions->pluck('key')->toArray();
        });

        $entry->load('answers.question');

        $answers = $entry->answers;

        return view('candidate-subscriptions.show', compact('entry', 'sections', 'answers'));
    }

    public function create()
    {
        $form = $this->form;

        $form->load('sections.questions');

        return view('candidate-subscriptions.create', compact('form'));
    }

    public function store()
    {
        // checar se já existe pelo cpf

        $data = $this->validate(request(), $this->rules());

        // histórico
        $data['q63'] = 'Nenhuma anotação';

        $oldEntry = $this->form->entries()->firstWhere('cpf', $data['q28']);

        if ($oldEntry) {
            $oldEntry->answers()->delete();

            Subscription::createEntryFromArray($data, $oldEntry);
        } else {
            Subscription::createEntryFromArray($data);
        }

        return view('candidate-subscriptions.store');
    }

    public function update(Answer $answer)
    {
        $this->checkAuthorization();

        $data = $this->validate(request(), $this->rules());

        $answer->load('question', 'entry');

        $answer->value = $data[$answer->question->key];

        $question = $answer->question->content;

        if ($answer->save()) {
            session()->flash('status', "O campo \"${question}\" foi atualizado com sucesso!");
        } else {
            session()->flash('status', "Algo deu errado: O campo ${question} não foi atualizado!");
        }

        return redirect()->route('candidate-subscriptions.show', [
            'entry' => $answer->entry
        ]);
    }

    public function destroy(Entry $entry)
    {
        $this->checkAuthorization();

        if ($entry->delete()) {
            session()->flash('status', 'Cadastro deletado com sucesso!');
        } else {
            session()->flash('status', 'Algo deu errado: cadastrado não deletado!');
        }

        return redirect()->route('candidate-subscriptions.index');
    }

    private function getEntries()
    {
        if (request()->missing('filter')) {
            return Entry::with('answers.question')->paginate(10);
        }

        $filters = collect(request()->query('filter'));

        $entries = Entry::with('answers');

        $filters->reduce(function ($entries, $value, $field) {
            $entries = $this->applyFilter($entries, $field, $value);
            return $entries;
        }, $entries);

        return $entries->with('answers.question')->paginate(10);
    }

    private function applyFilter($entries, $field, $value)
    {
        $questions = [
            'name'      => 1,
            'age'       => 3,
            'gender'    => 4,
            'schooling' => 37,
            'course'    => 40,
            'district'  => 13,
            'city'      => 8,
            'employed'  => 51,
        ];

        if (!array_key_exists($field, $questions)) {
            return $entries;
        }

        $question_id = $questions[$field];

        return $entries->whereHas('answers', function (Builder $query) use ($question_id, $value) {
            if ($question_id === 3) {
                $query->where('question_id', $question_id)
                    ->whereDate('value', '<=', now()->subYears($value)->format('Y/m/d'));
                $query->where('question_id', $question_id)
                    ->whereDate('value', '>=', now()->subYears($value + 1)->format('Y/m/d'));

                return;
            }

            if (in_array($question_id, [3, 37, 51])) {
                $query->where('question_id', $question_id)
                    ->where('value', $value);

                return;
            }

            $query->where('question_id', $question_id)
                ->where('value', 'like', "%${value}%");
        });
    }

    private function setCollectionMacros()
    {
        collection::macro('for', function ($value) {
            return $this->firstWhere('question_id', substr($value, 1));
        });

        collection::macro('allFor', function ($value) {
            return $this->where('question_id', substr($value, 1));
        });

        collection::macro('allIn', function ($keys) {
            $keys = array_map(function ($key) {
                return substr($key, 1);
            }, $keys);
            return $this->whereIn('question_id', $keys);
        });
    }

    private function rules()
    {
        $rules = $this->form->rules;

        $q21_group = [
            'q21-group' => 'array',
            'q21-group.*.q22' => $rules['q22'],
            'q21-group.*.q23' => $rules['q23'],
            'q21-group.*.q24' => $rules['q24'],
            'q21-group.*.q25' => $rules['q25'],
            'q21-group.*.q26' => $rules['q26'],
        ];

        $q42_group = [
            'q42-group' => 'array',
            'q42-group.*.q42' => $rules['q42']
        ];

        $q43_group = [
            'q43-group' => 'array',
            'q43-group.*.q44' => $rules['q44'],
            'q43-group.*.q45' => $rules['q45'],
            'q43-group.*.q46' => $rules['q46'],
        ];

        $q47_group = [
            'q47-group' => 'array',
            'q47-group.*.q48' => $rules['q48'],
            'q47-group.*.q49' => $rules['q49'],
            'q47-group.*.q50' => $rules['q50'],
        ];

        $q58_group = [
            'q58-group' => 'array',
            'q58-group.*.q58' => $rules['q58']
        ];

        $rules = $this->array_insert_after($rules, 'q21', $q21_group);
        $rules = $this->array_insert_after($rules, 'q42', $q42_group);
        $rules = $this->array_insert_after($rules, 'q43', $q43_group);
        $rules = $this->array_insert_after($rules, 'q47', $q47_group);
        $rules = $this->array_insert_after($rules, 'q58', $q58_group);

        return $rules;
    }

    private function array_insert_after(array $array, $key, array $new)
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($array) : $index + 1;

        return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }

    private function checkAuthorization()
    {
        $user = auth()->user();

        abort_unless(($user->isAdmin() || $user->isCoordinator()), 401);
    }
}
