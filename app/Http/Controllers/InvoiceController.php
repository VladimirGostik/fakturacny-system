<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceService;
use App\Models\Company;
use App\Models\ResidentialCompany;
use App\Models\Place;
use App\Models\Service;
use PDF;


class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        // Načítanie všetkých faktúr z databázy
        $filter = $request->query('filter', 'created'); // Predvolene zobrazíme "vytvorené" faktúry, ak nie je filter nastavený
        $companies = Company::all();
        $invoices = Invoice::all();

        // Návrat pohľadu s faktúrami
        return view('invoices.index', compact('invoices','companies','filter'));
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
            //dd($places)->all();
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

public function generateMonthlyInvoices(Request $request)
{
    // Validácia vstupov
    $validated = $request->validate([
        'issue_date' => 'required|date',
        'due_date' => 'required|date',
        'billing_month' => 'required|integer|min:1|max:12',
    ]);

    $issueDate = $request->input('issue_date');
    $dueDate = $request->input('due_date');
    $billingMonth = $request->input('billing_month');
    $lastInvoice = Invoice::orderBy('invoice_number', 'desc')->first();
    $newInvoiceNumber = $lastInvoice ? $lastInvoice->invoice_number + 1 : 1;

    // Získanie všetkých bytových podnikov s miestami a službami
    $residentialCompanies = ResidentialCompany::with('places.services', 'company')->get();

    foreach ($residentialCompanies as $residentialCompany) {
        // Získajte spoločnosť pre tento bytový podnik
        $company = $residentialCompany->company;

        if (!$company) {
            continue; // Ak neexistuje priradená spoločnosť, preskočte tento bytový podnik
        }

        // Získajte všetky ulice pre tento bytový podnik
        $places = $residentialCompany->places()->with('services')->get();
        
        // Preskočte tento bytový podnik, ak nemá žiadne ulice alebo služby
        if ($places->isEmpty()) {
            continue;
        }

        foreach ($places as $place) {
            // Skontrolujte, či miesto má nejaké služby
            if ($place->services->isEmpty()) {
                continue;
            }

            // Generovanie faktúry a priradenie služieb
            $invoice = Invoice::create([
                'invoice_number' => $newInvoiceNumber++,  // Inkrementujte číslo faktúry
                'company_id' => $company->id,  // Použite správne company_id
                'residential_company_id' => $residentialCompany->id,
                'residential_company_name' => $residentialCompany->name,
                'residential_company_address' => $residentialCompany->address,
                'residential_company_city' => $residentialCompany->city,
                'residential_company_postal_code' => $residentialCompany->postal_code,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'billing_month' => $billingMonth,
            ]);

            // Uložte služby do `invoice_services`
            foreach ($place->services as $service) {
                InvoiceService::create([
                    'invoice_id' => $invoice->id,
                    'service_description' => $service->service_description,
                    'service_price' => $service->service_price,
                    'place_name' => $place->name,
                    'place_header' => $place->header,
                ]);
            }
        }
    }

    return redirect()->route('invoices.index')->with('status', 'Mesačné faktúry boli úspešne vygenerované a uložené.');
}



public function downloadSelectedInvoices(array $selectedInvoices)
{
    $zip = new ZipArchive;

    // Assume all invoices are for the same company and billing month
    $firstInvoice = Invoice::find($selectedInvoices[0]);
    $companyName = str_replace(' ', '_', $firstInvoice->company->name);
    $billingMonth = $firstInvoice->billing_month;

    // Set the ZIP file name as company_name_billing_month.zip
    $zipFileName = $companyName . '_mesiac_' . $billingMonth . '.zip';
    $zipFilePath = storage_path($zipFileName);

    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($selectedInvoices as $invoiceId) {
            $invoice = Invoice::find($invoiceId);

            $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            $pdfContent = $pdf->output();
            
            // Set the name of individual invoices within the ZIP archive
            $placeName = str_replace(' ', '_', $invoice->services->first()->place_name);
            $fileName = $placeName . '_mesiac_' . $billingMonth . '.pdf';

            // Add the PDF file to the ZIP archive
            $zip->addFromString($fileName, $pdfContent);
        }
        $zip->close();
    } else {
        return redirect()->route('invoices.index')->with('error', 'Unable to create ZIP file.');
    }

    // Download the ZIP file and delete it after sending
    return response()->download($zipFilePath)->deleteFileAfterSend(true);
}


    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $selectedInvoices = json_decode($request->input('selected_invoices_list'), true);
        $filter = $request->input('filter'); 

        if ($selectedInvoices && is_array($selectedInvoices)) {
            if ($action == 'mark_sent') {
                // Označiť faktúry ako odoslané
                foreach ($selectedInvoices as $invoiceId) {
                    $invoice = Invoice::find($invoiceId);
                    if ($invoice->status != 'paid') {
                        // Skontroluj, či faktúra nie je po splatnosti
                        if ($invoice->due_date < now()) {
                            $invoice->update(['status' => 'expired']);
                        } else {
                            $invoice->update(['status' => 'sent']);
                        }
                    }
                }
                //Invoice::whereIn('id', $selectedInvoices)->update(['status' => 'sent']);
                return redirect()->route('invoices.index', ['filter' => $filter])->with('status', 'Faktúry boli označené ako odoslané.');

            } elseif ($action == 'mark_paid') {
                Invoice::whereIn('id', $selectedInvoices)->update(['status' => 'paid']);
                return redirect()->route('invoices.index', ['filter' => $filter])->with('status', 'Faktúry boli označené ako zaplatené.');
            } elseif ($action == 'download_selected') {
                // Stiahnuť vybrané faktúry
                return $this->downloadSelectedInvoices($selectedInvoices);
            } elseif ($action == 'delete_selected') {
                // Vymazať vybrané faktúry
                Invoice::whereIn('id', $selectedInvoices)->delete();
                return redirect()->route('invoices.index', ['filter' => $filter])->with('status', 'Vybrané faktúry boli vymazané.');
            }
            return redirect()->route('invoices.index', ['filter' => $filter])->with('status', 'Hromadná akcia bola úspešne vykonaná.');
        }

        return redirect()->route('invoices.index', ['filter' => $filter])->with('status', 'Žiadne faktúry neboli vybrané.');
        }

        public function downloadPDF(Invoice $invoice)
    {
        $placeName = str_replace(' ', '_', $invoice->services->first()->place_name);
        $billingMonth = $invoice->billing_month;

        $pdfFileName = $placeName . '_mesiac_' . $billingMonth . '.pdf';

        //$pdf = PDF::loadView('invoices.pdf', compact('invoice'));
        $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download($pdfFileName);
    }

}
