<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\ResidentialCompany;
use App\Models\Service;
use App\Models\Company;
use Illuminate\Http\Request;
use PDF;


class PlaceController extends Controller
{
    public function index()
    {
        $residential_companies = ResidentialCompany::all();
        $places = Place::with('services')->get(); // Všetky miesta so službami
        return view('places.index', compact('residential_companies', 'places'));
    }

    public function edit($id)
    {
        $place = Place::findOrFail($id);
        $residentialCompanies = ResidentialCompany::all(); // Načítanie všetkých bytových podnikov, ak ich potrebujete na úpravu
        return view('places.edit', compact('place', 'residentialCompanies'));
    }


    public function create()
    {
        $residentialCompanies = ResidentialCompany::all();
        return view('places.create', compact('residentialCompanies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'residential_company_id' => 'required|exists:residential_companies,id',
            'header' => 'nullable|string',
            'desc_above_service' => 'nullable|string|max:255',
            'residential_company_address' => 'nullable|string|max:255',
            'residential_company_city' => 'nullable|string|max:255',
            'residential_company_postal_code' => 'nullable|string|max:255',
            'residential_company_ico' => 'nullable|string|max:255',
            'residential_company_dic' => 'nullable|string|max:255',
            'residential_company_ic_dph' => 'nullable|string|max:255',
            'residential_company_iban' => 'nullable|string|max:255',
            'residential_company_bank_connection' => 'nullable|string|max:255',
            'invoice_type' => 'required|string|max:255',
            'desc_services' => 'nullable|string|max:255',
        ]);

        // Vytvorenie novej ulice
        $place = Place::create($validatedData);

        foreach ($request->service_description as $index => $description) {
            Service::create([
                'place_id' => $place->id,
                'service_description' => $description,
                'service_price' => $request->service_price[$index],
            ]);
        }

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne vytvorená!');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'header' => 'nullable|string',
            'desc_above_service' => 'nullable|string|max:255',
            'residential_company_address' => 'nullable|string|max:255',
            'residential_company_city' => 'nullable|string|max:255',
            'residential_company_postal_code' => 'nullable|string|max:255',
            'residential_company_ico' => 'nullable|string|max:255',
            'residential_company_dic' => 'nullable|string|max:255',
            'residential_company_ic_dph' => 'nullable|string|max:255',
            'residential_company_iban' => 'nullable|string|max:255',
            'residential_company_bank_connection' => 'nullable|string|max:255',
            'invoice_type' => 'required|string|max:255',
            'desc_services' => 'nullable|string|max:255',
        ]);

        $place = Place::find($id);
        $place->update($validatedData);

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne aktualizovaná!');
    }

    public function destroy(Place $place)
    {
        // Vymazanie všetkých služieb priradených k danej ulici
        $place->services()->delete();

        $place->delete();

        return redirect()->route('places.index')->with('status', 'Ulica bola úspešne vymazaná!');
    }

    public function generateInvoice($id)
    {
        // Načítanie ulice so službami a bytovým podnikom
        $place = Place::with(['services', 'residentialCompany'])->findOrFail($id);

        // Predpokladáme, že máte autentifikovaného používateľa a priradenú spoločnosť
        $user = auth()->user();
        $company = $place->residentialCompany->company;
        $residentialCompany = $place->residentialCompany;


        // Generovanie jedinečného čísla faktúry (napr. pomocou ID a aktuálneho času)
        $invoiceNumber = 'F' . strtoupper(uniqid());
        //dd($residentialCompany->all());
        // Vytvorenie dát pre faktúru
        $invoice = (object) [
            'invoice_number' => $invoiceNumber,
            'billing_month' => now()->format('F Y'),
            'issue_date' => now()->format('d-m-Y'),
            'due_date' => now()->addDays(30)->format('d-m-Y'),
            'company' => $company,
            'services' => $place->services,
            'invoice_type' => $place->invoice_type,
            'residential_company_name' => $residentialCompany->name,
            'residential_company_address' => $place->residential_company_address,
            'residential_company_postal_code' => $place->residential_company_city,
            'residential_company_city' => $place->residential_company_postal_code,
            'residential_company_ico' => $place->residential_company_ico,
            'residential_company_dic' => $place->residential_company_dic,
            'residential_company_ic_dph' => $place->residential_company_ic_dph,
            'residential_company_iban' => $place->residential_company_iban,
            'residential_company_bank_connection' => $place->residential_company_bank_connection,
            'header' => $place->header,
            'desc_above_service' => $place->desc_above_service,
            'desc_services' => $place->desc_services,
        ];

        // Generovanie PDF z Blade šablóny
        $pdf = \PDF::loadView('invoices.invoice', compact('invoice', 'user'));

        // Návrat PDF ako stream
        return $pdf->inline('faktura_' . $invoiceNumber . '.pdf');
    }
}
