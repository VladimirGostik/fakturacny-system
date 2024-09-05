@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 text-center">{{ __('Štatistiky spoločnosti') }}</h1>

    <!-- Filter form for date range and invoice type -->
    <form action="{{ route('export.index') }}" method="GET" class="mb-8 flex justify-center items-center space-x-4">
        <div>
            <label for="from_date" class="block text-sm font-medium text-white">{{ __('Od') }}</label>
            <input type="date" name="from_date" id="from_date" value="{{ request('from_date', now()->startOfYear()->format('Y-m-d')) }}" 
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <label for="to_date" class="block text-sm font-medium text-white">{{ __('Do') }}</label>
            <input type="date" name="to_date" id="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" 
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <!-- Dropdown for invoice type -->
        <div>
            <label for="invoice_type" class="block text-sm font-medium text-white">{{ __('Typ faktúry') }}</label>
            <select name="invoice_type" id="invoice_type" 
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="all" {{ request('invoice_type', 'all') == 'all' ? 'selected' : '' }}>{{ __('Všetky') }}</option>
                <option value="sent" {{ request('invoice_type') == 'sent' ? 'selected' : '' }}>{{ __('Poslané') }}</option>
                <option value="expired" {{ request('invoice_type') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                <option value="paid" {{ request('invoice_type') == 'paid' ? 'selected' : '' }}>{{ __('Zaplatené') }}</option>
            </select>
        </div>
    </form>

    @foreach($companies as $company)
        <div class="mb-8 p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <!-- Company Name and Download Button -->
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $company->name }}</h2>
                <a href="{{ route('export.download_statistics', ['company_id' => $company->id, 'invoiceType' => $invoiceType]) }}" 
                    class="bg-blue-500 text-white py-2 px-4 rounded ml-4">
                     {{ __('Stiahnuť štatistiky') }}
                 </a>
            </div>
            
            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                <div class="stat-box bg-blue-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet bytových podnikov') }}</h2>
                    <p class="text-3xl">{{ $statistics[$company->id]['total_residential_companies'] }}</p>
                </div>
                <div class="stat-box bg-green-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet miest') }}</h2>
                    <p class="text-3xl">{{ $statistics[$company->id]['total_places'] }}</p>
                </div>
                <div class="stat-box bg-yellow-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet faktúr') }}</h2>
                    <p class="text-3xl">{{ $statistics[$company->id]['total_paid_invoices'] }}</p>
                </div>
                <div class="stat-box bg-red-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Súčet všetkých faktúr') }}</h2>
                    <p class="text-3xl">{{ $statistics[$company->id]['total_invoice_sum'] }} €</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    document.getElementById('from_date').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('to_date').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('invoice_type').addEventListener('change', function() {
        this.form.submit();
    });
</script>

@endsection
