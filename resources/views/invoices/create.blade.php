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

    <!-- Box pre Generovanie mesačných faktúr -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('Generovanie mesačných faktúr') }}</h1>
    
        <form method="POST" action="{{ route('invoices.generate_monthly') }}">
            @csrf
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div>
                    <x-input-label for="issue_date" :value="__('Dátum vytvorenia faktúr')" class="text-lg" />
                    <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="due_date" :value="__('Dátum splatnosti faktúr')" class="text-lg" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="billing_month" :value="__('Mesiac fakturácie')" class="text-lg" />
                    <select id="billing_month" name="billing_month" class="mt-1 block w-full" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ __('Mesiac') }} {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <x-primary-button>{{ __('Generovať mesačné faktúry') }}</x-primary-button>
            </div>
        </form>
    </div>

    <!-- Box pre Vytvoriť novú faktúru -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ __('Vytvoriť novú faktúru') }}</h1>

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <!-- Dátum vytvorenia, Dátum splatnosti a Mesiac fakturácie -->
            <!-- Invoice Number Field -->
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="mt-6">
                    <x-input-label for="invoice_number" :value="__('Číslo faktúry')" class="text-lg" />
                    <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" required />
                </div>
                <div class="mt-6">
                    <x-input-label for="billing_month" :value="__('Mesiac fakturácie')" class="text-lg" />
                    <select id="billing_month" name="billing_month" class="mt-1 block w-full" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <x-input-label for="issue_date" :value="__('Dátum vytvorenia')" class="text-lg" />
                    <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="due_date" :value="__('Dátum splatnosti')" class="text-lg" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" required />
                </div>
            </div>

            <!-- Firma a Bytový podnik -->
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <x-input-label for="company_id" :value="__('Vyberte firmu')" class="text-lg" />
                    <select id="company_id" name="company_id" class="mt-1 block w-full" required>
                        <option value="">{{ __('Vyberte firmu') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="residential_company_id" :value="__('Vyberte bytový podnik')" class="text-lg" />
                    <select id="residential_company_id" name="residential_company_id" class="mt-1 block w-full" disabled>
                        <option value="">{{ __('Vyberte najprv firmu') }}</option>
                    </select>
                </div>
            </div>

            <!-- Informácie o bytovom podniku -->
            <div id="residential_info" class="grid grid-cols-2 gap-4 mt-6 hidden">
                <div>
                    <x-input-label for="residential_company_name" :value="__('Názov bytového podniku')" class="text-lg" />
                    <x-text-input id="residential_company_name" name="residential_company_name" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_address" :value="__('Adresa bytového podniku')" class="text-lg" />
                    <x-text-input id="residential_company_address" name="residential_company_address" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_postal_code" :value="__('PSČ')" class="text-lg" />
                    <x-text-input id="residential_company_postal_code" name="residential_company_postal_code" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_city" :value="__('Mesto')" class="text-lg" />
                    <x-text-input id="residential_company_city" name="residential_company_city" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_ico" :value="__('IČO')" class="text-lg" />
                    <x-text-input id="residential_company_ico" name="residential_company_ico" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_dic" :value="__('DIČ')" class="text-lg" />
                    <x-text-input id="residential_company_dic" name="residential_company_dic" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_ic_dph" :value="__('IČ DPH')" class="text-lg" />
                    <x-text-input id="residential_company_ic_dph" name="residential_company_ic_dph" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_iban" :value="__('IBAN')" class="text-lg" />
                    <x-text-input id="residential_company_iban" name="residential_company_iban" type="text" class="mt-1 block w-full" readonly />
                </div>

                <div>
                    <x-input-label for="residential_company_bank_connection" :value="__('Bankové spojenie')" class="text-lg" />
                    <x-text-input id="residential_company_bank_connection" name="residential_company_bank_connection" type="text" class="mt-1 block w-full" readonly />
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
                            <option value="{{ $place->id }}">{{ $place->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Street Name Input Field (will toggle visibility) -->
                <div id="new_street_container">
                    <x-input-label for="new_street" :value="__('Zadajte názov ulice')" class="text-lg" />
                    <x-text-input id="new_street" name="new_street" type="text" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="header" :value="__('Popis do hlavičky')" class="text-lg" />
                    <textarea id="header" name="header" class="mt-1 block w-full"></textarea>
                </div>
            </div>

            <!-- Služby -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-6">{{ __('Služby') }}</h2>
            <div id="services_section" class="mt-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="service_description_0" :value="'Popis služby'" class="text-lg" />
                        <x-text-input id="service_description_0" name="services[0][description]" type="text" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="service_price_0" :value="'Cena služby'" class="text-lg" />
                        <x-text-input id="service_price_0" name="services[0][price]" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-end">
                        <button type="button" class="bg-green-500 text-white py-2 px-4 rounded add-service">{{ __('Pridať službu') }}</button>
                    </div>
                </div>
            </div>

            <!-- Submit button -->
            <div class="flex justify-end mt-6">
                <x-primary-button>{{ __('Vytvoriť faktúru') }}</x-primary-button>
            </div>
        </form>
    </div>
</div>

<script>
    let companies = @json($companies);
    let residentialCompanies = @json($residential_companies);
    let places = @json($places);    

    document.getElementById('company_id').addEventListener('change', function() {
        let selectedCompanyId = this.value;
        let residentialSelect = document.getElementById('residential_company_id');
        let placeSelect = document.getElementById('existing_place');
        
        residentialSelect.innerHTML = '<option value="">{{ __("Vyberte bytový podnik") }}</option>';
        placeSelect.innerHTML = '<option value="">{{ __("Vyberte najprv bytový podnik") }}</option>';
        placeSelect.disabled = true;

        document.getElementById('residential_info').classList.add('hidden');
        
        if (selectedCompanyId) {
            residentialSelect.disabled = false;
            residentialCompanies.forEach(function(company) {
                if (company.company_id == selectedCompanyId) {
                    let option = document.createElement('option');
                    option.value = company.id;
                    option.text = company.name;
                    residentialSelect.add(option);
                }
            });
        } else {
            residentialSelect.disabled = true;
        }
    });

    document.getElementById('residential_company_id').addEventListener('change', function() {
        let selectedResidentialId = this.value;
        let placeSelect = document.getElementById('existing_place');
        let infoSection = document.getElementById('residential_info');
        
        placeSelect.innerHTML = '<option value="">{{ __("Vyberte ulicu alebo napíšte novú") }}</option>';
        infoSection.classList.add('hidden');
        
        if (selectedResidentialId) {
            placeSelect.disabled = false;
            places.forEach(function(place) {
                if (place.residential_company_id == selectedResidentialId) {
                    let option = document.createElement('option');
                    option.value = place.id;
                    option.text = place.name;
                    placeSelect.add(option);
                }
            });

            // Fill residential company info
            residentialCompanies.forEach(function(company) {
                if (company.id == selectedResidentialId) {
                    document.getElementById('residential_company_name').value = company.name;
                    document.getElementById('residential_company_address').value = company.address;
                    document.getElementById('residential_company_postal_code').value = company.postal_code;
                    document.getElementById('residential_company_city').value = company.city;
                    document.getElementById('residential_company_ico').value = company.ico;
                    document.getElementById('residential_company_dic').value = company.dic;
                    document.getElementById('residential_company_ic_dph').value = company.ic_dph;
                    document.getElementById('residential_company_iban').value = company.iban;
                    document.getElementById('residential_company_bank_connection').value = company.bank_connection;

                    infoSection.classList.remove('hidden');
                }
            });
        } else {
            placeSelect.disabled = true;
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        let existingPlaceSelect = document.getElementById('existing_place');
        let newStreetContainer = document.getElementById('new_street_container');
        let newStreetField = document.getElementById('new_street');
        let headerField = document.getElementById('header');
        let servicesSection = document.getElementById('services_section');

        // Initial check when page loads to see if a place is selected
        initializeStreetSelection(existingPlaceSelect.value);

        // Listen for changes to the existing place dropdown
        existingPlaceSelect.addEventListener('change', function () {
            initializeStreetSelection(existingPlaceSelect.value);
        });

        function initializeStreetSelection(selectedPlaceId) {
            // Reset the header field whenever the dropdown changes
            headerField.value = "";

            // Clear services section on selection change
            servicesSection.innerHTML = `
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="service_description_0" :value="'Popis služby'" class="text-lg" />
                        <x-text-input id="service_description_0" name="services[0][description]" type="text" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="service_price_0" :value="'Cena služby'" class="text-lg" />
                        <x-text-input id="service_price_0" name="services[0][price]" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>

                    <div class="flex items-end">
                        <button type="button" class="bg-green-500 text-white py-2 px-4 rounded add-service">{{ __('Pridať službu') }}</button>
                    </div>
                </div>
            `;

            if (selectedPlaceId) {
                // Find the selected place in the places array
                let selectedPlace = places.find(place => place.id == selectedPlaceId);

                if (selectedPlace) {
                    // Hide the input for a new street and auto-fill the header
                    newStreetContainer.classList.add('hidden');
                    newStreetField.value = "";  // Clear the new street field
                    headerField.value = selectedPlace.header || "";  // Fill the header with the selected place's header

                    // Check if the selected place has services
                    if (selectedPlace.services && selectedPlace.services.length > 0) {
                        // Populate services in the services section
                        selectedPlace.services.forEach(function (service, index) {
                            let serviceRow = document.createElement('div');
                            serviceRow.classList.add('grid', 'grid-cols-3', 'gap-4', 'mt-2');
                            serviceRow.innerHTML = `
                                <div>
                                    <x-input-label :value="'Popis služby'" class="text-lg" />
                                    <x-text-input name="services[${index}][description]" type="text" class="mt-1 block w-full" value="${service.service_description}" />
                                </div>
                                <div>
                                    <x-input-label :value="'Cena služby'" class="text-lg" />
                                    <x-text-input name="services[${index}][price]" type="number" step="0.01" class="mt-1 block w-full" value="${service.service_price}" />
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="bg-red-500 text-white py-2 px-4 rounded remove-service">{{ __('Vymazať') }}</button>
                                </div>
                            `;
                            servicesSection.appendChild(serviceRow);

                            // Add remove functionality for each service
                            serviceRow.querySelector('.remove-service').addEventListener('click', function () {
                                servicesSection.removeChild(serviceRow);
                            });
                        });
                    }
                }
            } else {
                // Show the new street input field if no existing street is selected
                newStreetContainer.classList.remove('hidden');
                newStreetField.value = "";  // Clear any previous input in the new street field
            }

            // Add event listener to dynamically add new service rows
            document.querySelector('.add-service').addEventListener('click', function () {
                let serviceCount = servicesSection.children.length;

                let newServiceRow = document.createElement('div');
                newServiceRow.classList.add('grid', 'grid-cols-3', 'gap-4', 'mt-2');
                newServiceRow.innerHTML = `
                    <div>
                        <x-input-label :value="'Popis služby'" class="text-lg" />
                        <x-text-input name="services[${serviceCount}][description]" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label :value="'Cena služby'" class="text-lg" />
                        <x-text-input name="services[${serviceCount}][price]" type="number" step="0.01" class="mt-1 block w-full" />
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="bg-red-500 text-white py-2 px-4 rounded remove-service">{{ __('Vymazať') }}</button>
                    </div>
                `;
                servicesSection.appendChild(newServiceRow);

                // Add remove functionality
                newServiceRow.querySelector('.remove-service').addEventListener('click', function () {
                    servicesSection.removeChild(newServiceRow);
                });
            });
        }
    });
</script>

@endsection
