<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        abort_if(request()->user()->cannot('viewAny', Company::class), 401);

        $companies = Company::with('phones', 'address')->get();

        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        abort_if(request()->user()->cannot('view', $company), 401);

        $company->load('phones', 'address');

        return view('companies.show', compact('company'));
    }

    public function create()
    {
        abort_if(request()->user()->cannot('create', Company::class), 401);

        return view('companies.create');
    }

    public function store()
    {
        abort_if(request()->user()->cannot('create', Company::class), 401);

        request()->validate([
            'name'                  => [
                'required',
                'unique:App\Models\Company,name'
            ],
            'cnpj'                  => [
                'required',
                'unique:App\Models\Company,cnpj',
                'size:18',
            ],
            'phone'                 => ['required'],
            'address'               => ['required'],
            'address.street'        => ['required'],
            'address.number'        => ['required'],
            'address.district'      => ['required'],
            'address.city'          => ['required'],
            'address.state'         => ['required'],
            'address.country'       => ['required'],
            'address.cep'           => ['required', 'size:10'],
        ]);

        $company = Company::create([
            'name'  => request()->name,
            'cnpj'  => request()->cnpj,
        ]);

        $company->phones()->create(['number' => request()->phone]);

        $company->address()->create([
            'street'    => request()->address['street'],
            'number'    => request()->address['number'],
            'district'  => request()->address['district'],
            'city'      => request()->address['city'],
            'state'     => request()->address['state'],
            'country'   => request()->address['country'],
            'cep'       => request()->address['cep'],
        ]);

        return view('companies.show', compact('company'));
    }
}
