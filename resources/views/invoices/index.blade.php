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
    <div class="mb-4 flex justify-center space-x-4">
        <button data-state="created" class="state-filter-button bg-blue-700 text-white py-2 px-4 rounded active">{{ __('Vytvorené') }}</button>
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

            <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="px-4 py-2">
                            <input type="checkbox" id="select-all" class="hidden">
                        </th>
                        <th class="px-4 py-2">{{ __('Firma') }}</th>
                        <th class="px-4 py-2">{{ __('Bytový podnik') }}</th>
                        <th class="px-4 py-2">{{ __('Miesto') }}</th>
                        <th class="px-4 py-2">{{ __('Suma faktúry') }}</th>
                        <th class="px-4 py-2">{{ __('Akcie') }}</th>
                    </tr>
                </thead>
                <tbody id="invoice-list">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" data-company="{{ $invoice->company_id }}" data-state="{{ $invoice->status }}">
                        <td class="px-4 py-2">
                            <input type="checkbox" name="selected_invoices[]" value="{{ $invoice->id }}" class="invoice-checkbox hidden">
                        </td>
                        <td class="px-4 py-2">{{ $invoice->company->name }}</td>
                        <td class="px-4 py-2">{{ $invoice->residential_company_name }}</td>
                        <td class="px-4 py-2 place-column">{{ $invoice->services->first()->place_name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->services->sum('service_price'), 2, ',', ' ') }} €</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-500">{{ __('Zobraziť') }}</a> |
                            <a href="{{ route('invoices.download_pdf', $invoice->id) }}" class="text-green-500">{{ __('Stiahnuť') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Bulk Action Buttons -->
            <div class="mt-4 space-x-4 text-center">
                <button type="button" onclick="setBulkAction('mark_sent')" class="bg-green-500 text-white py-2 px-4 rounded hidden" id="mark-sent">{{ __('Označiť ako odoslané') }}</button>
                <button type="button" onclick="setBulkAction('mark_paid')" class="bg-yellow-500 text-white py-2 px-4 rounded hidden" id="mark-paid">{{ __('Označiť ako zaplatené') }}</button>
                <button type="button" onclick="setBulkAction('delete_selected')" class="bg-red-500 text-white py-2 px-4 rounded hidden" id="delete-selected">{{ __('Vymazať označené') }}</button>
                <button type="button" onclick="setBulkAction('download_selected')" class="bg-blue-500 text-white py-2 px-4 rounded hidden" id="download-selected">{{ __('Stiahnuť označené') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for search and toggle functionality -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let urlParams = new URLSearchParams(window.location.search);
        let currentState = urlParams.get('filter') || 'created';  // Default state filter to 'created'

        // Apply initial filter state from URL
        filterInvoices(currentState);
        setActiveButton(currentState);

        // Search box functionality for filtering by "Miesto" while respecting active state
        const searchBox = document.getElementById('search-box');
        const invoiceList = document.getElementById('invoice-list');

        searchBox.addEventListener('keyup', function() {
            const searchTerm = searchBox.value.toLowerCase();
            filterInvoices(currentState, searchTerm);
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

        // State Filter Buttons
        document.querySelectorAll('.state-filter-button').forEach(button => {
            button.addEventListener('click', function() {
                let selectedState = this.getAttribute('data-state');
                currentState = selectedState;  // Update current state
                filterInvoices(currentState, searchBox.value.toLowerCase());  // Reapply filtering based on the new state and current search term
                setActiveButton(currentState);
                let url = new URL(window.location.href);
                url.searchParams.set('filter', currentState);
                window.history.pushState({}, '', url);
            });
        });

        // Company Filter Dropdown
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

        // Filtering function that respects both state and search term
        function filterInvoices(state, searchTerm = '') {
            document.querySelectorAll('#invoice-list tr').forEach(row => {
                const placeColumn = row.querySelector('.place-column').textContent.toLowerCase();
                const rowState = row.getAttribute('data-state');

                // Show the row only if it matches the current state and search term
                if (rowState === state && placeColumn.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

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
        function setBulkAction(action) {
            document.getElementById('bulk-action-input').value = action;

            let selectedInvoices = [];
            document.querySelectorAll('#invoice-list tr:not([style*="display: none"]) .invoice-checkbox:checked').forEach(function (checkbox) {
                selectedInvoices.push(checkbox.value);
            });

            document.getElementById('selected_invoices_input').value = JSON.stringify(selectedInvoices);
            let activeFilter = document.querySelector('.state-filter-button.bg-blue-700').getAttribute('data-state');
            document.getElementById('filter-input').value = activeFilter;

            document.getElementById('bulk-action-form').submit();
        }
    });

</script>
@endsection
