@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('Zoznam faktúr') }}</h1>

    <!-- Dropdown to filter by company -->
    <div class="mb-4">
        <x-input-label for="company_filter" :value="__('Vyberte firmu')" />
        <select id="company_filter" name="company_filter" class="mt-1 block w-full">
            <option value="">{{ __('Všetky firmy') }}</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- State Filter Buttons -->
    <div class="mb-4 flex space-x-4">
        <button data-state="all" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Všetky') }}</button>
        <button data-state="created" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Vytvorené') }}</button>
        <button data-state="sent" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Odoslané') }}</button>
        <button data-state="expired" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Po splatnosti') }}</button>
        <button data-state="paid" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Zaplatené') }}</button>
    </div>

    <!-- Invoice Table -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="px-4 py-2">{{ __('Číslo faktúry') }}</th>
                    <th class="px-4 py-2">{{ __('Firma') }}</th>
                    <th class="px-4 py-2">{{ __('Bytový podnik') }}</th>
                    <th class="px-4 py-2">{{ __('Dátum vytvorenia') }}</th>
                    <th class="px-4 py-2">{{ __('Akcie') }}</th>
                </tr>
            </thead>
            <tbody id="invoice-list">
                @foreach($invoices as $invoice)
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" data-company="{{ $invoice->company_id }}" data-state="{{ $invoice->status }}">
                    <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                    <td class="px-4 py-2">{{ $invoice->company->name }}</td>
                    <td class="px-4 py-2">{{ $invoice->residential_company_name }}</td>
                    <td class="px-4 py-2">{{ $invoice->issue_date }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-500">{{ __('Zobraziť') }}</a> |
                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Naozaj chcete vymazať túto faktúru?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">{{ __('Vymazať') }}</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // State Filter
    document.querySelectorAll('.state-filter-button').forEach(button => {
        button.addEventListener('click', function() {
            let selectedState = this.getAttribute('data-state');
            document.querySelectorAll('#invoice-list tr').forEach(row => {
                if (selectedState === 'all' || row.getAttribute('data-state') === selectedState) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Company Filter
    document.getElementById('company_filter').addEventListener('change', function() {
        let selectedCompany = this.value;
        document.querySelectorAll('#invoice-list tr').forEach(row => {
            if (selectedCompany === '' || row.getAttribute('data-company') == selectedCompany) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
