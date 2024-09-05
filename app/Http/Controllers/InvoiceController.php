<?php

namespace App\Http\Controllers;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\InvoiceService;
use App\Models\Company;
use App\Models\ResidentialCompany;
use App\Models\Place;
use App\Models\Service;
use PDF;
ini_set('memory_limit', '512M');  // Adjust as needed


class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        $filter = $request->query('filter', 'created');
        $perPage = $request->query('perPage', 10); // Default to 10 items per page if not specified
        $residentialCompanies = ResidentialCompany::all();
        $companies = Company::all();
    
        // Paginate invoices with the number of items per page selected by the user
        $invoices = Invoice::where('status', $filter)->paginate($perPage);
    
        return view('invoices.index', compact('invoices', 'companies', 'filter', 'residentialCompanies', 'perPage'));
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
        'invoice_number' => [
            'required',
            'string',
            'max:255',
            // Kontrola unikátnosti čísla faktúry v rámci danej firmy
            Rule::unique('invoices')->where(function ($query) use ($request, $invoice) {
                return $query->where('company_id', $request->company_id)
                             ->where('invoice_number', $request->invoice_number)
                             ->where('id', '!=', $invoice->id);
            }),
        ],
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
    // Validácia vstupných údajov
    $validated = $request->validate([
        //'invoice_number' => 'nullable|string|max:255', // Bude validované len ak nie je automaticky generované
        //'auto_generate' => 'required|in:true,false', // Check that it is either "true" or "false"
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
    // Ak je checkbox zaškrtnutý, generujeme číslo faktúry automaticky
    if ($request['auto_generate'] === 'true') {
        $invoiceNumber = $this->generateInvoiceNumber($validated['company_id'], $validated['billing_month'], $validated['issue_date']);
    } else {
        // Manually entered invoice number with uniqueness validation
        $validated = $request->validate([
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('invoices')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                                 ->where('invoice_number', $request->invoice_number);
                }),
            ],
        ]);
        $invoiceNumber = $request['invoice_number'];
    }

    // Vytvorenie novej faktúry
    $invoice = Invoice::create([
        'invoice_number' => $invoiceNumber,
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

    $placeName = $validated['new_street'] ?: Place::find($validated['existing_place'])->name;

    // Uloženie služieb ak sú poskytnuté
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

            // Get the latest invoice number and ensure it's unique
            $newInvoiceNumber = $this->generateInvoiceNumber($company->id, $billingMonth, $issueDate);

            // Generovanie faktúry a priradenie služieb
            $invoice = Invoice::create([
                'invoice_number' => $newInvoiceNumber,  // Inkrementujte číslo faktúry
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

private function generateInvoiceNumber($companyId, $billingMonth, $issueDate)
{
    // Určte rok fakturácie
    $billingYear = ($billingMonth == 12) ? date('Y', strtotime($issueDate)) - 1 : date('Y', strtotime($issueDate));

    // Získajte najväčšie číslo faktúry pre daný rok a firmu
    $lastInvoice = Invoice::where('company_id', $companyId)
                    ->whereYear('issue_date', $billingYear)
                    ->orderBy('invoice_number', 'desc')
                    ->first();

    // Získajte nové číslo faktúry
    if ($lastInvoice) {
        // Extrahujte časť čísla faktúry pred "/"
        $lastInvoiceNumber = explode('/', $lastInvoice->invoice_number)[0];
        $newInvoiceNumber = intval($lastInvoiceNumber) + 1;
    } else {
        $newInvoiceNumber = 1;
    }

    // Vytvorte nové číslo faktúry s rokom fakturácie
    return $newInvoiceNumber . '/' . $billingYear;
}



public function downloadSelectedInvoices(array $selectedInvoices)
{
    $user = auth()->user();
    
    // Načítame všetky faktúry a služby pre vybrané faktúry
    $invoices = Invoice::with('services', 'company')->whereIn('id', $selectedInvoices)->get();

    if ($invoices->isEmpty()) {
        return redirect()->route('invoices.index')->with('error', 'Žiadne faktúry na spracovanie.');
    }

    // Začneme HTML výstup pre všetky faktúry
    $htmlContent = '';

    foreach ($invoices as $invoice) {
        // Pre každú faktúru vygenerujeme obsah HTML (zvýraznené neskoršie zlúčenie do jedného PDF)
        $htmlContent .= view('invoices.pdf', compact('invoice', 'user'))->render();
        $htmlContent .= '<div style="page-break-after: always;"></div>'; // Pridáme zalomenie stránky
    }

    // Vytvoríme jedno veľké PDF z HTML obsahu všetkých faktúr
    $pdf = PDF::loadHTML($htmlContent);

    // Generovanie názvu PDF súboru
    $firstInvoice = $invoices->first();
    $companyName = str_replace(' ', '_', $firstInvoice->company->name);
    $billingMonth = $firstInvoice->billing_month;
    $pdfFileName = $companyName . '_mesiac_' . $billingMonth . '.pdf';

    // Stiahnutie PDF súboru obsahujúceho všetky faktúry
    return $pdf->download($pdfFileName);
}


    public function bulkAction(Request $request)
    {
        $action = $request->input('bulk_action');
        $selectedInvoices = json_decode($request->input('selected_invoices_list'), true);
        $filter = $request->input('filter'); 
        //dd($request->all());

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
                $paymentDate = $request->input('payment_date');
                if (!$paymentDate) {
                    return redirect()->route('invoices.index', ['filter' => $filter])->withErrors('Dátum zaplatenia je povinný.');
                }
    
                // Mark selected invoices as paid with the provided payment date
                foreach ($selectedInvoices as $invoiceId) {
                    $invoice = Invoice::find($invoiceId);
                    if ($invoice) {
                        $invoice->update([
                            'status' => 'paid',
                            'payment_date' => $paymentDate,
                        ]);
                    }
                }
                return redirect()->route('invoices.index', ['filter' => 'paid'])->with('status', 'Faktúry boli označené ako zaplatené s dátumom.');
    
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
        $user = auth()->user();
        $placeName = str_replace(' ', '_', $invoice->services->first()->place_name);
        $billingMonth = $invoice->billing_month;

        $pdfFileName = $placeName . '_mesiac_' . $billingMonth . '.pdf';

        try {
            $pdf = \PDF::loadView('invoices.pdf', compact('invoice', 'user'));
            return $pdf->download($pdfFileName);
        } catch (\Exception $e) {
            Log::error('PDF download error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to download PDF'], 500);
        }
    }

}
