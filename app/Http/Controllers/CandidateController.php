<?php

namespace App\Http\Controllers;

use App\Models\AprendizForm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function create()
    {
        return view('candidates.create');
    }

    public function store()
    {
        $data = $this->validate(request(), AprendizForm::getRules());

        if ($data['quantas_pessoas_moram_com_voce'] > 0) {
            $data['moradores'] = AprendizForm::parseInputToJson($data['moradores'], 5);
        }

        if (request()->has('cursos_complementares')) {
            $data['cursos_complementares'] = AprendizForm::parseInputToJson($data['cursos_complementares'], 3);
        }

        if (request()->has('experiencia_profissional')) {
            $data['experiencia_profissional'] = AprendizForm::parseInputToJson($data['experiencia_profissional'], 3);
        }

        $data['conhecimentos_em_informatica'] = json_encode($data['conhecimentos_em_informatica']);
        $data['no_espelho_voce_enxerga'] = json_encode($data['no_espelho_voce_enxerga']);

        $data['historico'] = '';

        DB::transaction(function () use ($data) {
            $existingCpfForm = AprendizForm::where('cpf', $data['cpf'])->first();
            if ($existingCpfForm) {
                $existingCpfForm->delete();
            }

            AprendizForm::create($data);
        });

        return view('candidate-subscriptions.store');
    }

    public function update(AprendizForm $entry)
    {
        $this->checkAuthorization();

        $data = collect($this->validate(request(), [
            'esta_empregado' => ['string', 'max:3', 'in:sim,não'],
            'historico' => ['string', 'max:255']
        ]));

        $data->each(function ($value, $field) use ($entry) {
            $entry[$field] = $value;
        });

        if ($entry->save()) {
            session()->flash('status', "O campo foi atualizado com sucesso!");
        } else {
            session()->flash('status', "Algo deu errado: O campo não foi atualizado!");
        }

        return redirect()->route('candidates.show', [
            'entry' => $entry
        ]);
    }

    public function destroy(AprendizForm $entry)
    {
        $this->checkAuthorization();

        if ($entry->delete()) {
            session()->flash('status', 'Cadastro deletado com sucesso!');
        } else {
            session()->flash('status', 'Algo deu errado: cadastrado não deletado!');
        }

        return redirect()->route('candidates.index');
    }

    private function getEntries()
    {
        $entries = AprendizForm::select('id', 'nome', 'data_de_nascimento', 'genero', 'cidade_onde_mora', 'escolaridade', 'created_at');

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
            'agefrom'           => 'data_de_nascimento_from',
            'ageto'             => 'data_de_nascimento_to',
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
            if ($column === 'data_de_nascimento_from') {
                $query->whereDate('data_de_nascimento', '<=', now()->subYears($value)->format('Y/m/d'));

                return;
            }

            if ($column === 'data_de_nascimento_to') {
                $query->whereDate('data_de_nascimento', '>=', now()->subYears($value + 1)->format('Y/m/d'));
                return;
            }

            if (in_array($column, ['data_de_nascimento', 'escolaridade', 'esta_empregado'])) {
                $query->where($column, $value);

                return;
            }

            if ($column === 'cidade_onde_mora') {
                $cities = STR::of($value)->explode(',');
                $query->where(function ($query) use ($cities) {
                    $cities->each(function ($city) use ($query) {
                        $query->orWhere('cidade_onde_mora', $city);
                    });
                });
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
