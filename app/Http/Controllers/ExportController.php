<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\ResidentialCompany;

class ExportController extends Controller
{

    public function index()
    {
        // Retrieve all companies
        $companies = Company::with('residentialCompanies.places')->get();
        
        $statistics = [];

        foreach ($companies as $company) {
            // Get the total number of residential companies for the company
            $totalResidentialCompanies = $company->residentialCompanies()->count();

            // Get the total number of places across all residential companies
            $totalPlaces = $company->residentialCompanies()->withCount('places')->get()->sum('places_count');

            // Get the total number of paid invoices for the company
            $totalPaidInvoices = Invoice::where('company_id', $company->id)->where('status', 'paid')->count();

            // Calculate the sum of all invoice service prices for the company
            $totalInvoiceSum = Invoice::where('company_id', $company->id)
            ->where('status', 'paid') // Only sum paid invoices
            ->with('services')
            ->get()
            ->reduce(function ($carry, $invoice) {
                return $carry + $invoice->services->sum('service_price');
            }, 0);

            // Store statistics for the company
            $statistics[$company->id] = [
                'total_residential_companies' => $totalResidentialCompanies,
                'total_places' => $totalPlaces,
                'total_paid_invoices' => $totalPaidInvoices,
                'total_invoice_sum' => number_format($totalInvoiceSum, 2, ',', ' '),
            ];
        }

        // Pass both the companies and their statistics to the view
        return view('export.index', compact('companies', 'statistics'));
    }
}
