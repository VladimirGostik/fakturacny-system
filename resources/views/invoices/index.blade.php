@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">{{ __('Zoznam faktúr') }}</h1>

        <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="px-4 py-2 text-left">{{ __('Číslo faktúry') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Firma') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Bytový podnik') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Dátum vytvorenia') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Stav') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('Akcie') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2">{{ $invoice->company->name }}</td>
                        <td class="px-4 py-2">{{ $invoice->residential_company->name }}</td>
                        <td class="px-4 py-2">{{ $invoice->issue_date }}</td>
                        <td class="px-4 py-2">{{ $invoice->current_status }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-500">{{ __('Zobraziť') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
