<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\ResidentialCompany; // Pridáme model pre odberateľov
use App\Models\Place;


class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        
        // Získanie všetkých odberateľov
        $residential_companies = ResidentialCompany::all();
        $places = Place::all();
        // Odovzdanie údajov do Blade súboru
        return view('companies.index', compact('companies', 'residential_companies','places'));
    }

    // Formulár na úpravu firmy
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'company_ico' => 'required|string|max:50',
            'company_dic' => 'required|string|max:50',
            'company_iban' => 'required|string|max:34',
            'bank_connection' => 'required|string|max:10',
        ]);

        $company->update([
            'name' => $validatedData['company_name'],
            'address' => $validatedData['company_address'],
            'postal_code' => $validatedData['postal_code'],
            'city' => $validatedData['city'],
            'ico' => $validatedData['company_ico'],
            'dic' => $validatedData['company_dic'],
            'iban' => $validatedData['company_iban'],
            'bank_connection' => $validatedData['bank_connection'],
        ]);

        return redirect()->route('companies.index')->with('status', 'Firma bola úspešne aktualizovaná!');
    }

public function store(Request $request)
{
    // Validácia vstupov
    $validatedData = $request->validate([
        'company_name' => 'required|string|max:255',
        'company_address' => 'required|string|max:255',
        'postal_code' => 'required|string|max:20',
        'city' => 'required|string|max:255',
        'company_ico' => 'required|string|max:50',
        'company_dic' => 'required|string|max:50',
        'company_iban' => 'required|string|max:34',
        'bank_connection' => 'required|string|max:10',  // Pridaná validácia pre Bankové spojenie
    ]);

    // Uloženie novej firmy do databázy
    Company::create([
        'name' => $validatedData['company_name'],
        'address' => $validatedData['company_address'],
        'postal_code' => $validatedData['postal_code'],
        'city' => $validatedData['city'],
        'ico' => $validatedData['company_ico'],
        'dic' => $validatedData['company_dic'],
        'iban' => $validatedData['company_iban'],
        'bank_connection' => $validatedData['bank_connection'],  // Uložíme Bankové spojenie
    ]);

    // Presmerovanie po úspešnom pridaní firmy
    return redirect()->route('companies.index')->with('status', 'Spoločnosť bola úspešne pridaná!');
}
public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('status', 'Firma bola úspešne vymazaná!');
    }

}
