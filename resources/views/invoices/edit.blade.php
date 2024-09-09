@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
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

    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('Upraviť faktúru') }} #{{ $invoice->invoice_number }}</h1>

    <form method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf
        @method('PUT')

        <!-- Invoice Number and Billing Month -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <x-input-label for="invoice_number" :value="__('Číslo faktúry')" class="text-lg" />
                <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" value="{{ $invoice->invoice_number }}" required />
            </div>

            <div>
                <x-input-label for="billing_month" :value="__('Mesiac fakturácie')" class="text-lg" />
                <select id="billing_month" name="billing_month" class="mt-1 block w-full" required>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $invoice->billing_month == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Issue and Due Dates -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <x-input-label for="issue_date" :value="__('Dátum vytvorenia')" class="text-lg" />
                <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" value="{{ $invoice->issue_date }}" required />
            </div>

            <div>
                <x-input-label for="due_date" :value="__('Dátum splatnosti')" class="text-lg" />
                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" value="{{ $invoice->due_date }}" required />
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="invoice_type" :value="__('Typ faktúry')" class="text-lg" />
            <select id="invoice_type" name="invoice_type" class="mt-1 block w-full" required>
                <option value="Hlavicka-Adresa-Nazov" {{ $invoice->invoice_type == 'Hlavicka-Adresa-Nazov' ? 'selected' : '' }}>Hlavicka-Adresa-Nazov</option>
                <option value="Hlavicka-Nazov-Adresa" {{ $invoice->invoice_type == 'Hlavicka-Nazov-Adresa' ? 'selected' : '' }}>Hlavicka-Nazov-Adresa</option>
                <option value="Adresa-Hlavicka-Nazov" {{ $invoice->invoice_type == 'Adresa-Hlavicka-Nazov' ? 'selected' : '' }}>Adresa-Hlavicka-Nazov</option>
                <option value="Adresa-Nazov-Hlavicka" {{ $invoice->invoice_type == 'Adresa-Nazov-Hlavicka' ? 'selected' : '' }}>Adresa-Nazov-Hlavicka</option>
                <option value="Nazov-Hlavicka-Adresa" {{ $invoice->invoice_type == 'Nazov-Hlavicka-Adresa' ? 'selected' : '' }}>Nazov-Hlavicka-Adresa</option>
                <option value="Nazov-Adresa-Hlavicka" {{ $invoice->invoice_type == 'Nazov-Adresa-Hlavicka' ? 'selected' : '' }}>Nazov-Adresa-Hlavicka</option>
            </select>
            
        </div>

        <div class="mt-6">
            <x-input-label for="desc_above_service" :value="__('Popis nad služby')" class="text-lg" />
            <textarea id="desc_above_service" name="desc_above_service" class="mt-1 block w-full">{{ $invoice->desc_above_services }}</textarea>
        </div>

        <!-- Company and Residential Company -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <x-input-label for="company_id" :value="__('Vyberte firmu')" class="text-lg" />
                <select id="company_id" name="company_id" class="mt-1 block w-full" required>
                    <option value="">{{ __('Vyberte firmu') }}</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ $invoice->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="residential_company_id" :value="__('Vyberte bytový podnik')" class="text-lg" />
                <select id="residential_company_id" name="residential_company_id" class="mt-1 block w-full" required>
                    <option value="">{{ __('Vyberte bytový podnik') }}</option>
                    @foreach ($residential_companies as $residential_company)
                        <option value="{{ $residential_company->id }}" {{ $invoice->residential_company_id == $residential_company->id ? 'selected' : '' }}>{{ $residential_company->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Residential Company Info (Always Visible) -->
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <x-input-label for="residential_company_name" :value="__('Názov bytového podniku')" class="text-lg" />
                <x-text-input id="residential_company_name" name="residential_company_name" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_name }}" required />
            </div>

            <div>
                <x-input-label for="residential_company_address" :value="__('Adresa bytového podniku')" class="text-lg" />
                <x-text-input id="residential_company_address" name="residential_company_address" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_address }}" required />
            </div>

            <div>
                <x-input-label for="residential_company_city" :value="__('Mesto')" class="text-lg" />
                <x-text-input id="residential_company_city" name="residential_company_city" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_city }}" required />
            </div>

            <div>
                <x-input-label for="residential_company_postal_code" :value="__('PSČ')" class="text-lg" />
                <x-text-input id="residential_company_postal_code" name="residential_company_postal_code" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_postal_code }}" required />
            </div>

            <div>
                <x-input-label for="residential_company_ico" :value="__('IČO')" class="text-lg" />
                <x-text-input id="residential_company_ico" name="residential_company_ico" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_ico }}" />
            </div>

            <div>
                <x-input-label for="residential_company_dic" :value="__('DIČ')" class="text-lg" />
                <x-text-input id="residential_company_dic" name="residential_company_dic" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_dic }}" />
            </div>

            <div>
                <x-input-label for="residential_company_ic_dph" :value="__('IČ DPH')" class="text-lg" />
                <x-text-input id="residential_company_ic_dph" name="residential_company_ic_dph" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_ic_dph }}" />
            </div>

            <div>
                <x-input-label for="residential_company_iban" :value="__('IBAN')" class="text-lg" />
                <x-text-input id="residential_company_iban" name="residential_company_iban" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_iban }}" />
            </div>

            <div>
                <x-input-label for="residential_company_bank_connection" :value="__('Bankové spojenie')" class="text-lg" />
                <x-text-input id="residential_company_bank_connection" name="residential_company_bank_connection" type="text" class="mt-1 block w-full" value="{{ $invoice->residential_company_bank_connection }}" />
            </div>
        </div>

        <!-- Pridávanie ulice -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-6">{{ __('Pridávanie ulice') }}</h2>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="existing_place" :value="__('Vyberte ulicu alebo napíšte novú')" class="text-lg" />
                <select id="existing_place" name="existing_place" class="mt-1 block w-full">
                    <option value="">{{ __('Vyberte ulicu alebo napíšte novú') }}</option>
                    @foreach ($places as $place)
                        <option value="{{ $place->id }}" {{ $invoice->services->first()->place_name == $place->name ? 'selected' : '' }}>{{ $place->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="new_street" :value="__('Zadajte názov ulice')" class="text-lg" />
                <x-text-input id="new_street" name="new_street" type="text" class="mt-1 block w-full" value="{{ $invoice->services->first()->place_name }}" />
            </div>

            <div>
                <x-input-label for="header" :value="__('Popis do hlavičky')" class="text-lg" />
                <textarea id="header" name="header" class="mt-1 block w-full">{{ $invoice->services->first()->place_header }}</textarea>
            </div>
        </div>

        <!-- Služby -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-6">{{ __('Služby') }}</h2>
        <div id="services_section" class="mt-4">
            @foreach($invoice->services as $index => $service)
            <div class="grid grid-cols-3 gap-4 service-row" data-index="{{ $index }}">
                <div>
                    <x-input-label for="service_description_{{ $index }}" :value="'Popis služby'" class="text-lg" />
                    <x-text-input id="service_description_{{ $index }}" name="services[{{ $index }}][description]" type="text" class="mt-1 block w-full" value="{{ $service->service_description }}" />
                </div>

                <div>
                    <x-input-label for="service_price_{{ $index }}" :value="'Cena služby'" class="text-lg" />
                    <x-text-input id="service_price_{{ $index }}" name="services[{{ $index }}][price]" type="number" step="0.01" class="mt-1 block w-full" value="{{ $service->service_price }}" />
                </div>

                <div class="flex items-end">
                    <button type="button" class="bg-red-500 text-white py-2 px-4 rounded remove-service">{{ __('Vymazať službu') }}</button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Tlačidlo na pridanie novej služby -->
        <div class="mt-6">
            <button type="button" id="add_service" class="bg-green-500 text-white py-2 px-4 rounded">{{ __('Pridať službu') }}</button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-6">
            <x-primary-button>{{ __('Aktualizovať faktúru') }}</x-primary-button>
        </div>
    </form>
</div>

<script>
    let companies = @json($companies);
    let residentialCompanies = @json($residential_companies);
    let places = @json($places);

    document.getElementById('company_id').addEventListener('change', function() {
        let selectedCompanyId = this.value;
        let residentialSelect = document.getElementById('residential_company_id');
        
        residentialSelect.innerHTML = '<option value="">{{ __("Vyberte bytový podnik") }}</option>';
        
        if (selectedCompanyId) {
            residentialCompanies.forEach(function(company) {
                if (company.company_id == selectedCompanyId) {
                    let option = document.createElement('option');
                    option.value = company.id;
                    option.text = company.name;
                    residentialSelect.add(option);
                }
            });
        }
    });

    document.getElementById('residential_company_id').addEventListener('change', function() {
        let selectedResidentialId = this.value;

        if (selectedResidentialId) {
            residentialCompanies.forEach(function(company) {
                if (company.id == selectedResidentialId) {
                    document.getElementById('residential_company_name').value = company.name;
                    document.getElementById('residential_company_address').value = company.address;
                    document.getElementById('residential_company_city').value = company.city;
                    document.getElementById('residential_company_postal_code').value = company.postal_code;
                    document.getElementById('residential_company_ico').value = company.ico;
                    document.getElementById('residential_company_dic').value = company.dic;
                    document.getElementById('residential_company_ic_dph').value = company.ic_dph;
                    document.getElementById('residential_company_iban').value = company.iban;
                    document.getElementById('residential_company_bank_connection').value = company.bank_connection;
                }
            });
        }
    });

    document.getElementById('add_service').addEventListener('click', function () {
        let servicesSection = document.getElementById('services_section');
        let index = servicesSection.children.length;

        let newService = `
            <div class="grid grid-cols-3 gap-4 mt-4 service-row" data-index="${index}">
                <div>
                    <x-input-label for="service_description_${index}" :value="'Popis služby'" class="text-lg" />
                    <x-text-input id="service_description_${index}" name="services[${index}][description]" type="text" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="service_price_${index}" :value="'Cena služby'" class="text-lg" />
                    <x-text-input id="service_price_${index}" name="services[${index}][price]" type="number" step="0.01" class="mt-1 block w-full" />
                </div>

                <div class="flex items-end">
                    <button type="button" class="bg-red-500 text-white py-2 px-4 rounded remove-service">{{ __('Vymazať službu') }}</button>
                </div>
            </div>
        `;

        servicesSection.insertAdjacentHTML('beforeend', newService);
    });

    // Event delegation for remove buttons
    document.getElementById('services_section').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('remove-service')) {
            let serviceRow = event.target.closest('.service-row');
            serviceRow.remove();
        }
    });
</script>

@endsection
