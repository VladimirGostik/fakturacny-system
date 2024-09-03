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
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Residential Company Filter -->
        <div class="flex-grow">
            <x-input-label for="residential_company_filter" :value="__('Vyberte bytový podnik')" />
            <select id="residential_company_filter" name="residential_company_filter" class="mt-1 block w-full">
                <option value="">{{ __('Všetky bytové podniky') }}</option>
                @foreach($residentialCompanies as $residentialCompany)
                    <option value="{{ $residentialCompany->id }}">{{ $residentialCompany->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- State Filter Buttons -->
    <div class="mb-4 flex justify-center space-x-4">
        <button data-state="created" class="state-filter-button bg-blue-700 text-white py-2 px-4 rounded">{{ __('Vytvorené') }}</button>
        <button data-state="sent" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Odoslané') }}</button>
        <button data-state="expired" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Po splatnosti') }}</button>
        <button data-state="paid" class="state-filter-button bg-blue-500 text-white py-2 px-4 rounded">{{ __('Zaplatené') }}</button>
    </div>

    <!-- Select Button and Search Box -->
    <div class="mb-4 flex justify-between items-center">
        <button id="toggle-select" class="bg-blue-500 text-white py-2 px-4 rounded">{{ __('Označiť faktúry') }}</button>

        <!-- Search box for filtering by 'Miesto' -->
        <input type="text" id="search-box" placeholder="Hľadať podľa miesta..." class="border rounded py-2 px-4 w-1/3">
    </div>

    <!-- Invoice Table -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <!-- Hlavný formulár pre bulk akcie -->
        <form id="bulk-action-form" method="POST" action="{{ route('invoices.bulk_action') }}">
            @csrf
            <input type="hidden" id="bulk-action-input" name="bulk_action" value="">
            <input type="hidden" id="selected_invoices_input" name="selected_invoices_list" value="">
            <input type="hidden" id="filter-input" name="filter" value="{{ $filter }}"> <!-- Skrytý input pre filter -->

            <!-- Počet zobrazených faktúr na stránku -->
            <div class="mb-4">
                <label for="items-per-page" class="text-white">{{ __('Počet faktúr na stránku:') }}</label>
                <select id="items-per-page" class="ml-2 border rounded text-black">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="px-4 py-2">
                            <input type="checkbox" id="select-all" class="hidden">
                        </th>
                        <th class="px-4 py-2">{{ __('Číslo faktúry') }}</th>
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
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" data-company="{{ $invoice->company_id }}" data-residential="{{ $invoice->residential_company_id }}" data-state="{{ $invoice->status }}" data-invoice-number="{{ $invoice->invoice_number }}">
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
            <div id="pagination-container" class="mt-4 flex justify-center"></div>

            <!-- Bulk Action Buttons -->
            <div class="mt-4 space-x-4 text-center">
                <button type="button" onclick="setBulkAction('mark_sent')" class="bg-green-500 text-white py-2 px-4 rounded hidden" id="mark-sent">{{ __('Označiť ako odoslané') }}</button>
                <button type="button" onclick="openPaymentDateModal()" class="bg-yellow-500 text-white py-2 px-4 rounded hidden" id="mark-paid">{{ __('Označiť ako zaplatené') }}</button>
                <button type="button" onclick="setBulkAction('delete_selected')" class="bg-red-500 text-white py-2 px-4 rounded hidden" id="delete-selected">{{ __('Vymazať označené') }}</button>
                <button type="button" onclick="setBulkAction('download_selected')" class="bg-blue-500 text-white py-2 px-4 rounded hidden" id="download-selected">{{ __('Stiahnuť označené') }}</button>
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
        </form>
    </div>
</div>

<!-- JavaScript for search, pagination, toggle, filter, and bulk action functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsPerPageSelect = document.getElementById('items-per-page');
    const invoiceList = document.getElementById('invoice-list');
    const paginationContainer = document.getElementById('pagination-container');
    const rows = Array.from(invoiceList.querySelectorAll('tr'));
    let itemsPerPage = parseInt(itemsPerPageSelect.value, 10);
    let currentState = 'created';  // Default state filter to 'created'
    let currentPage = 1;

    // Function to handle filtering, pagination, and displaying the rows
    function updateInvoices() {
        const searchTerm = document.getElementById('search-box').value.toLowerCase();
        const selectedCompany = document.getElementById('company_filter').value;
        const selectedResidentialCompany = document.getElementById('residential_company_filter').value;

        const filteredRows = rows.filter(row => {
            const placeColumn = row.querySelector('.place-column').textContent.toLowerCase();
            const rowState = row.getAttribute('data-state');
            const rowCompany = row.getAttribute('data-company');
            const rowResidential = row.getAttribute('data-residential');

            const matchesState = !currentState || rowState === currentState;
            const matchesSearch = placeColumn.includes(searchTerm);
            const matchesCompany = !selectedCompany || rowCompany === selectedCompany;
            const matchesResidential = !selectedResidentialCompany || rowResidential === selectedResidentialCompany;

            return matchesState && matchesSearch && matchesCompany && matchesResidential;
        });

        setupPagination(filteredRows);
        displayPage(currentPage, filteredRows);
    }

    function setupPagination(filteredRows) {
        paginationContainer.innerHTML = '';
        const pageCount = Math.ceil(filteredRows.length / itemsPerPage);

        for (let i = 1; i <= pageCount; i++) {
            const button = document.createElement('button');
            button.innerText = i;
            button.classList.add('pagination-button', 'px-4', 'py-2', 'bg-blue-500', 'text-white', 'rounded', 'mx-1');
            button.setAttribute('data-page', i);

            button.addEventListener('click', () => {
                displayPage(i, filteredRows);
            });

            paginationContainer.appendChild(button);
        }

        currentPage = 1;  // Reset to the first page
        displayPage(currentPage, filteredRows);
    }

    function displayPage(page, filteredRows) {
        rows.forEach(row => {
            row.style.display = 'none'; // Najprv skry všetky riadky
        });

        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        filteredRows.slice(start, end).forEach(row => {
            row.style.display = ''; // Potom zobraz iba tie, ktoré patria na danú stránku
        });

        document.querySelectorAll('.pagination-button').forEach(button => {
            button.classList.remove('active');
        });
        const activeButton = document.querySelector(`.pagination-button[data-page="${page}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
        currentPage = page;
    }


    itemsPerPageSelect.addEventListener('change', function() {
        itemsPerPage = parseInt(this.value, 10);
        updateInvoices();
    });

    // Apply search filter
    document.getElementById('search-box').addEventListener('keyup', function() {
        updateInvoices();
    });

    // Company Filter Dropdown
    document.getElementById('company_filter').addEventListener('change', function() {
        updateInvoices();
    });

    // Residential Company Filter Dropdown
    document.getElementById('residential_company_filter').addEventListener('change', function() {
        updateInvoices();
    });

    // State Filter Buttons
    document.querySelectorAll('.state-filter-button').forEach(button => {
        button.addEventListener('click', function() {
            currentState = this.getAttribute('data-state');
            setActiveButton(currentState);
            updateInvoices();
        });
    });

    // Toggle checkboxes
    document.getElementById('toggle-select').addEventListener('click', function() {
        document.querySelectorAll('.invoice-checkbox').forEach(checkbox => {
            checkbox.classList.toggle('hidden');
        });
        document.getElementById('mark-sent').classList.toggle('hidden');
        document.getElementById('mark-paid').classList.toggle('hidden');
        document.getElementById('delete-selected').classList.toggle('hidden');
        document.getElementById('download-selected').classList.toggle('hidden');
        document.getElementById('select-all').classList.toggle('hidden');
    });

    // Select all checkboxes for visible rows
    document.getElementById('select-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('#invoice-list tr:not([style*="display: none"]) .invoice-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Initial setup
    updateInvoices();

    // Set active button style for the current filter state
    function setActiveButton(state) {
        document.querySelectorAll('.state-filter-button').forEach(button => {
            if (button.getAttribute('data-state') === state) {
                button.classList.remove('bg-blue-500');
                button.classList.add('bg-blue-700');
            } else {
                button.classList.remove('bg-blue-700');
                button.classList.add('bg-blue-500');
            }
        });
    }

    // Set bulk action based on selected invoices
    window.setBulkAction = function(action) {
        document.getElementById('bulk-action-input').value = action;

        let selectedInvoices = [];
        document.querySelectorAll('#invoice-list tr:not([style*="display: none"]) .invoice-checkbox:checked').forEach(function (checkbox) {
            selectedInvoices.push(checkbox.value);
        });

        if (selectedInvoices.length > 0) {
            document.getElementById('selected_invoices_input').value = JSON.stringify(selectedInvoices);

            if (action === 'mark_paid') {
                openPaymentDateModal();
            } else {
                document.getElementById('bulk-action-form').submit();
            }
        } else {
            alert('{{ __("Please select at least one invoice.") }}');
        }
    }

    window.openPaymentDateModal = function() {
        document.getElementById('payment-date-modal').classList.remove('hidden');
    }

    window.confirmPaymentDate = function() {
        let paymentDate = document.getElementById('modal-payment-date').value;
        document.getElementById('bulk-action-input').value = 'mark_paid';
        let selectedInvoices = [];
        document.querySelectorAll('#invoice-list .invoice-checkbox:checked').forEach(function (checkbox) {
            selectedInvoices.push(checkbox.value);
        });
        document.getElementById('selected_invoices_input').value = JSON.stringify(selectedInvoices);
        let paymentDateInput = document.createElement('input');
        paymentDateInput.type = 'hidden';
        paymentDateInput.name = 'payment_date';
        paymentDateInput.value = paymentDate;
        document.getElementById('bulk-action-form').appendChild(paymentDateInput);
        document.getElementById('bulk-action-form').submit();
    }

    window.closePaymentDateModal = function() {
        document.getElementById('payment-date-modal').classList.add('hidden');
    }
});

</script>
@endsection
