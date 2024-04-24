<?php

namespace App\Http\Controllers;

use App\Models\AprendizForm;
use Illuminate\Database\Eloquent\Builder;

class CandidateController extends Controller
{
    public function index()
    {
        $this->checkAuthorization();

        $entries = $this->getEntries();

        return view('candidates.index', compact('entries'));
    }

    public function show(AprendizForm $entry)
    {
        $this->checkAuthorization();

        return view('candidates.show', compact('entry'));
    }

    private function getEntries()
    {
        $entries = AprendizForm::select('id','nome', 'data_de_nascimento', 'genero', 'cidade_onde_mora', 'escolaridade');

        if (request()->missing('filter')) {
            return $entries->paginate(10);
        }

        $filters = collect(request()->query('filter'));

        $filters->reduce(function ($entries, $value, $field) {
            $entries = $this->applyFilter($entries, $field, $value);
            return $entries;
        }, $entries);

        return $entries->paginate(10);
    }

    private function applyFilter($entries, $field, $value)
    {
        $fields = [
            'name'              => 'nome',
            'age'               => 'data_de_nascimento',
            'gender'            => 'genero',
            'schooling'         => 'escolaridade',
            'course'            => 'curso',
            'complementary'     => 'cursos_complementares',
            'district'          => 'bairro',
            'city'              => 'cidade_onde_mora',
            'employed'          => 'esta_empregado',
        ];

        if (!array_key_exists($field, $fields)) {
            return $entries;
        }

        $column = $fields[$field];

        return $entries->where(function (Builder $query) use ($column, $value) {
            if ($column === 'data_de_nascimento') {
                $query->whereDate('data_de_nascimento', '<=', now()->subYears($value)->format('Y/m/d'));
                $query->whereDate('data_de_nascimento', '>=', now()->subYears($value + 1)->format('Y/m/d'));

                return;
            }

            if (in_array($column, ['data_de_nascimento', 'escolaridade', 'esta_empregado'])) {
                $query->where($column, $value);

                return;
            }

            $query->where($column, 'like', "%{$value}%");
        });
    }

    private function checkAuthorization()
    {
        $user = auth()->user();

        abort_unless(($user->isAdmin() || $user->isCoordinator()), 401);
    }
}
