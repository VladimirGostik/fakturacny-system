<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceService;
use App\Models\Company;
use App\Models\ResidentialCompany;
use App\Models\Place;
use App\Models\Service;

class InvoiceController extends Controller
{

    public function index()
    {
        // Načítanie všetkých faktúr z databázy
        $invoices = Invoice::all();
        $companies = Company::all();

        // Návrat pohľadu s faktúrami
        return view('invoices.index', compact('invoices','companies'));
    }

    public function create()
    {
        $companies = Company::all();
        $residential_companies = ResidentialCompany::all();
        $places = Place::with('services')->get();



        return view('invoices.create', compact('companies', 'residential_companies', 'places'));
    }

    public function show(Invoice $invoice)
        {
            // Return a view with the invoice details
            return view('invoices.show', compact('invoice'));
        }

        public function edit(Invoice $invoice)
        {
            // Potrebujeme načítať spoločnosti, bytové podniky a miesta na výber v editačnom formulári
            $companies = Company::all();
            $residential_companies = ResidentialCompany::all();
            $places = Place::with('services')->get();
            dd($invoice)->all();
            return view('invoices.edit', compact('invoice', 'companies', 'residential_companies', 'places'));
        }
        public function update(Request $request, Invoice $invoice)
{
    // Validácia vstupných údajov
    $validated = $request->validate([
        'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
        'company_id' => 'required|exists:companies,id',
        'residential_company_id' => 'required|exists:residential_companies,id',
        'residential_company_name' => 'required|string|max:255',
        'residential_company_address' => 'required|string|max:255',
        'residential_company_city' => 'required|string|max:255',
        'residential_company_postal_code' => 'required|string|max:255',
        'residential_company_ico' => 'nullable|string|max:255',
        'residential_company_dic' => 'nullable|string|max:255',
        'residential_company_ic_dph' => 'nullable|string|max:255',
        'residential_company_iban' => 'nullable|string|max:255',
        'residential_company_bank_connection' => 'nullable|string|max:255',
        'issue_date' => 'required|date',
        'due_date' => 'required|date',
        'billing_month' => 'required|string',
        'services' => 'sometimes|array',
        'services.*.description' => 'required_with:services|string|max:255',
        'services.*.price' => 'required_with:services|numeric|min:0',
        'new_street' => 'nullable|string|max:255',
        'existing_place' => 'nullable|exists:places,id',
        'header' => 'nullable|string|max:255',
    ]);

    // Aktualizácia faktúry
    $invoice->update($validated);

    $placeName = $validated['new_street'] ?: Place::find($validated['existing_place'])->name;

    // Odstránenie starých služieb
    $invoice->services()->delete();

    // Uloženie nových služieb
    if (isset($validated['services'])) {
        foreach ($validated['services'] as $serviceData) {
            InvoiceService::create([
                'invoice_id' => $invoice->id,
                'service_description' => $serviceData['description'],
                'service_price' => $serviceData['price'],
                'place_name' => $placeName,
                'place_header' => $validated['header'],
            ]);
        }
    }

    return redirect()->route('invoices.show', $invoice)->with('status', 'Faktúra bola úspešne aktualizovaná!');
}
    public function store(Request $request)
{
    // Validate the input data
    $validated = $request->validate([
        'invoice_number' => 'required|string|max:255|unique:invoices',
        'company_id' => 'required|exists:companies,id',
        'residential_company_id' => 'required|exists:residential_companies,id',
        'residential_company_name' => 'required|string|max:255',
        'residential_company_address' => 'required|string|max:255',
        'residential_company_city' => 'required|string|max:255',
        'residential_company_postal_code' => 'required|string|max:255',
        'residential_company_ico' => 'nullable|string|max:255',
        'residential_company_dic' => 'nullable|string|max:255',
        'residential_company_ic_dph' => 'nullable|string|max:255',
        'residential_company_iban' => 'nullable|string|max:255',
        'residential_company_bank_connection' => 'nullable|string|max:255',
        'issue_date' => 'required|date',
        'due_date' => 'required|date',
        'billing_month' => 'required|string',
        'services' => 'sometimes|array',
        'services.*.description' => 'required_with:services|string|max:255',
        'services.*.price' => 'required_with:services|numeric|min:0',
        'new_street' => 'nullable|string|max:255',
        'existing_place' => 'nullable|exists:places,id',
        'header' => 'nullable|string|max:255',
    ]);
    //dd($request->all());

    // Create the invoice with the validated data
    $invoice = Invoice::create([
        'invoice_number' => $validated['invoice_number'],
        'company_id' => $validated['company_id'],
        'residential_company_id' => $validated['residential_company_id'],
        'residential_company_name' => $validated['residential_company_name'],
        'residential_company_address' => $validated['residential_company_address'],
        'residential_company_city' => $validated['residential_company_city'],
        'residential_company_postal_code' => $validated['residential_company_postal_code'],
        'residential_company_ico' => $validated['residential_company_ico'],
        'residential_company_dic' => $validated['residential_company_dic'],
        'residential_company_ic_dph' => $validated['residential_company_ic_dph'],
        'residential_company_iban' => $validated['residential_company_iban'],
        'residential_company_bank_connection' => $validated['residential_company_bank_connection'],
        'issue_date' => $validated['issue_date'],
        'due_date' => $validated['due_date'],
        'billing_month' => $validated['billing_month'],
    ]);
    //dd($validated);

    $placeName = $validated['new_street'] ?: Place::find($validated['existing_place'])->name;
    // Save services if any are provided
    if (isset($validated['services'])) {
        foreach ($validated['services'] as $serviceData) {
            InvoiceService::create([
                'invoice_id' => $invoice->id,
                'service_description' => $serviceData['description'],
                'service_price' => $serviceData['price'],
                'place_name' => $placeName,  // Either the new street or the existing place's name
                'place_header' => $validated['header'],
            ]);
        }
    }

    return redirect()->route('invoices.index')->with('status', 'Faktúra bola úspešne vytvorená!');
}

public function destroy($id)
{
    $invoice = Invoice::findOrFail($id);

    // Delete all related invoice services
    $invoice->services()->delete();

    // Delete the invoice itself
    $invoice->delete();

    return redirect()->route('invoices.index')->with('status', 'Faktúra bola úspešne vymazaná!');
}


}
