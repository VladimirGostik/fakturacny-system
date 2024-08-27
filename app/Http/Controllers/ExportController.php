<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\ResidentialCompany;
use Carbon\Carbon;
use PDF;


class ExportController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve date range from the request or set default to start of the year to today
        $fromDate = $request->input('from_date', now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        // Convert to Carbon instances for easier comparison
        $fromDate = Carbon::parse($fromDate)->startOfDay();
        $toDate = Carbon::parse($toDate)->endOfDay();
        // Retrieve the selected invoice type or default to 'all'
        $invoiceType = $request->input('invoice_type', 'all');

        // Retrieve all companies
        $companies = Company::with('residentialCompanies.places')->get();
        
        $statistics = [];

        foreach ($companies as $company) {
            // Get the total number of residential companies for the company
            $totalResidentialCompanies = $company->residentialCompanies()->count();

            // Get the total number of places across all residential companies
            $totalPlaces = $company->residentialCompanies()->withCount('places')->get()->sum('places_count');

            // Filter invoices by date range and invoice type
            $invoiceQuery = Invoice::where('company_id', $company->id)
                ->whereBetween('issue_date', [$fromDate, $toDate]);

            if ($invoiceType != 'all') {
                $invoiceQuery->where('status', $invoiceType);
            }

            // Get the total number of filtered invoices
            $totalPaidInvoices = $invoiceQuery->count();

            // Calculate the sum of all invoice service prices for the filtered invoices
            $totalInvoiceSum = $invoiceQuery->with('services')
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

        // Pass both the companies, their statistics, and the current filters to the view
        return view('export.index', compact('companies', 'statistics', 'fromDate', 'toDate', 'invoiceType'));
    }

    public function downloadStatistics(Request $request)
    {
        //dd($request->all());
        $company_id = $request->input('company_id');
        $invoiceType = $request->input('invoiceType', 'all'); // Default to 'all' if not provided

        // Fetch the company details
        $company = Company::findOrFail($company_id);

        // Start building the query
        $invoiceQuery = Invoice::where('company_id', $company_id);

        // Apply filter if it's not 'all'
        if ($invoiceType != 'all') {
            $invoiceQuery->where('status', $invoiceType);
        }

        // Get the filtered invoices
        $invoices = $invoiceQuery->get();

        // Get the date range from the request, or use defaults
        $fromDate = $request->input('from_date', now()->startOfYear()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        // Pass the data to the Blade view and generate the PDF
        $pdf = \PDF::loadView('export.statistics', compact('company', 'invoices', 'fromDate', 'toDate'));

        // Download the PDF with a specific filename
        return $pdf->download('statistics-' . $company->name . '-' . now()->format('Y-m-d') . '.pdf');
    }


}
