<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResidentialCompany;
use App\Models\Company;

class ResidentialCompanyController extends Controller
{
    // Zobrazenie zoznamu odberateľov
    public function index(Request $request)
    {
        // Získame všetky firmy
        $companies = Company::all();

        // Ak bol vybraný filter podľa firmy, aplikujeme filter
        if ($request->has('company_id') && $request->company_id) {
            $residential_companies = ResidentialCompany::where('company_id', $request->company_id)->get();
        } else {
            // Ak nebol vybraný filter, zobrazíme všetky odberateľov
            $residential_companies = ResidentialCompany::all();
        }

        return view('residential.index', compact('residential_companies', 'companies'));
    }
    // Pridanie nového odberateľa
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'ico' => 'nullable|string|max:255',
            'dic' => 'nullable|string|max:255',
            'ic_dph' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',
            'bank_connection' => 'nullable|string|max:10',
            'company_id' => 'required|exists:companies,id',  // Pridáme validáciu pre company_id
        ]);

        ResidentialCompany::create($validatedData);

        return redirect()->route('residential-companies.index')->with('status', 'Odberateľ bol úspešne pridaný!');
    }

    // Formulár na úpravu odberateľa
    public function edit(ResidentialCompany $residentialCompany)
    {
        $companies = Company::all();  // Získame všetky firmy
        return view('residential.edit', ['residential_company' => $residentialCompany, 'companies' => $companies]);
    
    }

    // Aktualizácia odberateľa
    public function update(Request $request, ResidentialCompany $residentialCompany)
    {
        // Validácia dát
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'ico' => 'nullable|string|max:255',
            'dic' => 'nullable|string|max:255',
            'ic_dph' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:34',
            'bank_connection' => 'nullable|string|max:10',
            'company_id' => 'required|exists:companies,id',
        ]);

        // Aktualizácia odberateľa s validovanými dátami
        $residentialCompany->update($validatedData);

        // Presmerovanie s hláškou o úspechu
        return redirect()->route('residential-companies.index')->with('status', 'Odberateľ bol úspešne aktualizovaný!');
    }

    // Vymazanie odberateľa
    public function destroy(ResidentialCompany $residentialCompany)
    {
        $residentialCompany->delete();

        return redirect()->route('residential-companies.index')->with('status', 'Odberateľ bol úspešne vymazaný!');
    }
}

