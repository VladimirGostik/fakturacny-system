@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 text-center">{{ __('Zoznam faktúr') }}</h1>

    <!-- Display session status or errors -->
    @if (session('status'))
        <div class="bg-green-500 text-white p-4 rounded-lg">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Dropdown to filter by company and residential company -->
    <div class="mb-4 flex space-x-4">
        <!-- Company Filter -->
        <div class="flex-grow">
            <x-input-label for="company_filter" :value="__('Vyberte firmu')" />
            <select id="company_filter" name="company_filter" class="mt-1 block w-full">
                <option value="">{{ __('Všetky firmy') }}</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_filter') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Residential Company Filter -->
        <div class="flex-grow">
            <x-input-label for="residential_company_filter" :value="__('Vyberte bytový podnik')" />
            <select id="residential_company_filter" name="residential_company_filter" class="mt-1 block w-full">
                <option value="">{{ __('Všetky bytové podniky') }}</option>
                @foreach($residentialCompanies as $residentialCompany)
                    <option value="{{ $residentialCompany->id }}" {{ request('residential_company_filter') == $residentialCompany->id ? 'selected' : '' }}>{{ $residentialCompany->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- State Filter Buttons -->
    <div class="mb-4 flex justify-center space-x-4">
        <a href="{{ route('invoices.index', array_merge(request()->query(), ['filter' => 'created'])) }}" class="state-filter-button {{ request('filter', 'created') == 'created' ? 'bg-blue-700' : 'bg-blue-500' }} text-white py-2 px-4 rounded">{{ __('Vytvorené') }}</a>
        <a href="{{ route('invoices.index', array_merge(request()->query(), ['filter' => 'sent'])) }}" class="state-filter-button {{ request('filter') == 'sent' ? 'bg-blue-700' : 'bg-blue-500' }} text-white py-2 px-4 rounded">{{ __('Odoslané') }}</a>
        <a href="{{ route('invoices.index', array_merge(request()->query(), ['filter' => 'expired'])) }}" class="state-filter-button {{ request('filter') == 'expired' ? 'bg-blue-700' : 'bg-blue-500' }} text-white py-2 px-4 rounded">{{ __('Po splatnosti') }}</a>
        <a href="{{ route('invoices.index', array_merge(request()->query(), ['filter' => 'paid'])) }}" class="state-filter-button {{ request('filter') == 'paid' ? 'bg-blue-700' : 'bg-blue-500' }} text-white py-2 px-4 rounded">{{ __('Zaplatené') }}</a>
    </div>

    <!-- Select Button and Search Box -->
    <div class="mb-4 flex justify-between items-center">
        <button id="toggle-select" class="bg-blue-500 text-white py-2 px-4 rounded">{{ __('Označiť faktúry') }}</button>

        <!-- Search box for filtering by 'Miesto' -->
        <input type="text" id="search-box" placeholder="Hľadať podľa miesta..." class="border rounded py-2 px-4 w-1/3" value="{{ request('search') }}">
    </div>

    <!-- Invoice Table -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <form id="bulk-action-form" method="POST" action="{{ route('invoices.bulk_action') }}">
            @csrf
            <input type="hidden" id="bulk-action-input" name="bulk_action" value="">
            <input type="hidden" id="selected_invoices_input" name="selected_invoices_list" value="">

            <!-- Počet zobrazených faktúr na stránku -->
            <div class="mb-4">
                <label for="items-per-page" class="text-white">{{ __('Počet faktúr na stránku:') }}</label>
                <select id="items-per-page" name="perPage" class="ml-2 border rounded text-black">
                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="px-4 py-2"><input type="checkbox" id="select-all" class="hidden"></th>
                        <th class="px-4 py-2 cursor-pointer">
                            <a href="{{ route('invoices.index', array_merge(request()->query(), ['sortBy' => 'invoice_number', 'sortDirection' => request('sortDirection', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">
                                {{ __('Číslo faktúry') }}
                                @if(request('sortBy') == 'invoice_number')
                                    {{ request('sortDirection', 'asc') == 'asc' ? '▲' : '▼' }}
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-2">{{ __('Firma') }}</th>
                        <th class="px-4 py-2">{{ __('Bytový podnik') }}</th>
                        <th class="px-4 py-2">{{ __('Miesto') }}</th>
                        <th class="px-4 py-2">{{ __('Dátum vytvorenia') }}</th>
                        <th class="px-4 py-2">{{ __('Suma faktúry') }}</th>
                        <th class="px-4 py-2">{{ __('Akcie') }}</th>
                    </tr>
                </thead>
                <tbody id="invoice-list">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="px-4 py-2">
                            <input type="checkbox" name="selected_invoices[]" value="{{ $invoice->id }}" class="invoice-checkbox hidden">
                        </td>
                        <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2">{{ $invoice->company->name }}</td>
                        <td class="px-4 py-2">{{ $invoice->residential_company_name }}</td>
                        <td class="px-4 py-2 place-column">{{ $invoice->services->first()->place_name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $invoice->issue_date }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->services->sum('service_price'), 2, ',', ' ') }} €</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-500">{{ __('Zobraziť') }}</a> |
                            <a href="{{ route('invoices.download_pdf', $invoice->id) }}" class="text-green-500">{{ __('Stiahnuť') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Container -->
            <div id="pagination-container" class="mt-4 flex justify-center">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
            <!-- Payment Date Modal -->
            <div id="payment-date-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-bold mb-4">{{ __('Vyberte dátum zaplatenia') }}</h2>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">{{ __('Dátum zaplatenia') }}</label>
                    <input type="date" id="modal-payment-date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closePaymentDateModal()" class="bg-gray-500 text-white py-2 px-4 rounded">{{ __('Zrušiť') }}</button>
                        <button type="button" onclick="confirmPaymentDate()" class="bg-blue-500 text-white py-2 px-4 rounded">{{ __('Uložiť') }}</button>
                    </div>
                </div>
            </div>
            <!-- Bulk Action Buttons -->
            <div class="mt-4 space-x-4 text-center">
                <button type="button" onclick="setBulkAction('mark_sent')" class="bg-green-500 text-white py-2 px-4 rounded hidden" id="mark-sent">{{ __('Označiť ako odoslané') }}</button>
                <button type="button" onclick="openPaymentDateModal()" class="bg-yellow-500 text-white py-2 px-4 rounded hidden" id="mark-paid">{{ __('Označiť ako zaplatené') }}</button>
                <button type="button" onclick="setBulkAction('delete_selected')" class="bg-red-500 text-white py-2 px-4 rounded hidden" id="delete-selected">{{ __('Vymazať označené') }}</button>
                <button type="button" onclick="setBulkAction('download_selected')" class="bg-blue-500 text-white py-2 px-4 rounded hidden" id="download-selected">{{ __('Stiahnuť označené') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
   // Handle bulk actions - Toggle checkboxes and show/hide bulk action buttons
document.getElementById('toggle-select').addEventListener('click', function() {
    // Toggle visibility of checkboxes
    let anyCheckboxVisible = Array.from(document.querySelectorAll('.invoice-checkbox')).some(checkbox => !checkbox.classList.contains('hidden'));

    if (!anyCheckboxVisible) {
        document.getElementById('select-all').classList.remove('hidden');
    } else {
        document.getElementById('select-all').classList.add('hidden');
    }    
    document.querySelectorAll('.invoice-checkbox').forEach(checkbox => {
        checkbox.classList.toggle('hidden');
    });

    // Toggle visibility of bulk action buttons
    document.getElementById('mark-sent').classList.toggle('hidden');
    document.getElementById('mark-paid').classList.toggle('hidden');
    document.getElementById('delete-selected').classList.toggle('hidden');
    document.getElementById('download-selected').classList.toggle('hidden');
});

// Handle select-all checkbox functionality
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.invoice-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('company_filter').addEventListener('change', function() {
    let url = new URL(window.location.href);
    url.searchParams.set('company_filter', this.value);
    window.location.href = url.href;
});

document.getElementById('residential_company_filter').addEventListener('change', function() {
    let url = new URL(window.location.href);
    url.searchParams.set('residential_company_filter', this.value);
    window.location.href = url.href;
});

// Define the function to handle bulk actions
function setBulkAction(action) {
    // Set the bulk action value
    document.getElementById('bulk-action-input').value = action;

    // Collect selected invoices
    let selectedInvoices = [];
    document.querySelectorAll('.invoice-checkbox:checked').forEach(function(checkbox) {
        selectedInvoices.push(checkbox.value);
    });

    if (selectedInvoices.length > 0) {
        document.getElementById('selected_invoices_input').value = JSON.stringify(selectedInvoices);

        if (action === 'mark_paid') {
            // Open the payment date modal for the 'mark_paid' action
            openPaymentDateModal();
        } else {
            // Submit the form for other actions
            document.getElementById('bulk-action-form').submit();
        }
    } else {
        alert('Prosím, vyberte aspoň jednu faktúru.');
    }
}


// Handle changing the number of items per page
document.getElementById('items-per-page').addEventListener('change', function() {
    let url = new URL(window.location.href);
    url.searchParams.set('perPage', this.value);
    window.location.href = url.href;
});

// Handle search functionality for places
document.getElementById('search-box').addEventListener('keyup', function(event) {
    let url = new URL(window.location.href);
    url.searchParams.set('search', this.value);
    window.location.href = url.href;
});

function openPaymentDateModal() {
    document.getElementById('payment-date-modal').classList.remove('hidden');
}

// Define the function to close the payment date modal
function closePaymentDateModal() {
    document.getElementById('payment-date-modal').classList.add('hidden');
}

// Confirm and Set Payment Date, then submit the form
function confirmPaymentDate() {
    let paymentDate = document.getElementById('modal-payment-date').value;

    // Set the bulk action to 'mark_paid'
    document.getElementById('bulk-action-input').value = 'mark_paid';

    // Collect selected invoices
    let selectedInvoices = [];
    document.querySelectorAll('.invoice-checkbox:checked').forEach(function(checkbox) {
        selectedInvoices.push(checkbox.value);
    });

    // Set the selected invoices list
    document.getElementById('selected_invoices_input').value = JSON.stringify(selectedInvoices);

    // Create a hidden input field for the payment date and append it to the form
    let paymentDateInput = document.createElement('input');
    paymentDateInput.type = 'hidden';
    paymentDateInput.name = 'payment_date';
    paymentDateInput.value = paymentDate;
    document.getElementById('bulk-action-form').appendChild(paymentDateInput);

    // Submit the form
    document.getElementById('bulk-action-form').submit();
}
</script>
@endsection
