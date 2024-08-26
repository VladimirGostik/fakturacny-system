@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 text-center">{{ __('Štatistiky spoločnosti') }}</h1>

    @foreach($companies as $company)
        <div class="mb-8 p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $company->name }}</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                <div class="stat-box bg-blue-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet bytových podnikov') }}</h2>
                    <p class="text-4xl">{{ $statistics[$company->id]['total_residential_companies'] }}</p>
                </div>
                <div class="stat-box bg-green-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet miest') }}</h2>
                    <p class="text-4xl">{{ $statistics[$company->id]['total_places'] }}</p>
                </div>
                <div class="stat-box bg-yellow-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Počet zaplatených faktúr') }}</h2>
                    <p class="text-4xl">{{ $statistics[$company->id]['total_paid_invoices'] }}</p>
                </div>
                <div class="stat-box bg-red-500 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">{{ __('Súčet všetkých faktúr') }}</h2>
                    <p class="text-4xl">{{ $statistics[$company->id]['total_invoice_sum'] }} €</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
