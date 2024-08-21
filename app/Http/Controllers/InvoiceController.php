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

        // Návrat pohľadu s faktúrami
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $companies = Company::all();
        $residential_companies = ResidentialCompany::all();
        $places = Place::with('services')->get();

        return view('invoices.create', compact('companies', 'residential_companies', 'places'));
    }

    public function store(Request $request)
    {
        // Validácia vstupných údajov
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'company_id' => 'required|exists:companies,id',
            'residential_company_id' => 'required|exists:residential_companies,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'billing_month' => 'required|string',
            'services' => 'required|array',
            'services.*.id' => 'exists:services,id',
        ]);

        // Získanie informácií o spoločnosti a bytovom podniku
        $company = Company::findOrFail($validated['company_id']);
        $residential_company = ResidentialCompany::findOrFail($validated['residential_company_id']);
        
        // Vytvorenie faktúry
        $invoice = Invoice::create([
            'invoice_number' => $validated['invoice_number'],
            'company_id' => $company->id,
            'residential_company_id' => $residential_company->id,
            'residential_company_name' => $residential_company->name,
            'residential_company_address' => $residential_company->address,
            'residential_company_city' => $residential_company->city,
            'residential_company_postal_code' => $residential_company->postal_code,
            'residential_company_ico' => $residential_company->ico,
            'residential_company_dic' => $residential_company->dic,
            'residential_company_ic_dph' => $residential_company->ic_dph,
            'residential_company_iban' => $residential_company->iban,
            'residential_company_bank_connection' => $residential_company->bank_connection,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'billing_month' => $validated['billing_month'],
        ]);

        // Uloženie služieb a miesta do pivotnej tabuľky
        foreach ($validated['services'] as $serviceData) {
            $service = Service::findOrFail($serviceData['id']);
            $place = $service->place;

            InvoiceService::create([
                'invoice_id' => $invoice->id,
                'service_description' => $service->service_description,
                'service_price' => $service->service_price,
                'place_name' => $place->name,
                'place_header' => $place->header,
            ]);
        }

        return redirect()->route('invoices.index')->with('status', 'Faktúra bola úspešne vytvorená!');
    }
}
