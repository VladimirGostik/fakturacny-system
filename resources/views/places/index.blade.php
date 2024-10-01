@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        @if (session('status'))
            <div class="bg-green-500 text-white p-4 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex justify-center mb-4 space-x-2">
            <!-- Navigation buttons -->
            <a href="{{ route('companies.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Firmy') }}
            </a>

            <a href="{{ route('residential-companies.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Odberatelia') }}
            </a>

            <a href="{{ route('places.index') }}" class="bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Ulice') }}
            </a>
        </div>

        <!-- Section for creating a new street -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mt-10">
            <header>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('Vytvoriť novú ulicu') }}</h2>
            </header>
            <form method="POST" action="{{ route('places.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="residential_company_id" :value="__('Bytový podnik')" />
                        <select id="residential_company_id" name="residential_company_id" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                            @foreach ($residential_companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="name" :value="__('*Názov ulice')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="header" :value="__('Popis do hlavičky')" />
                        <textarea id="header" name="header" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24"></textarea>
                        <x-input-error :messages="$errors->get('header')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="desc_above_service" :value="__('Popis nad služby: Pre vlozenie datumu vloz: {mesiac} alebo {mesiac/rok}')" />
                        <textarea id="desc_above_service" name="desc_above_service" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24"></textarea>
                        <x-input-error :messages="$errors->get('desc_above_service')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="residential_company_address" :value="__('Adresa')" />
                        <x-text-input id="residential_company_address" name="residential_company_address" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_city" :value="__('Mesto')" />
                        <x-text-input id="residential_company_city" name="residential_company_city" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_postal_code" :value="__('PSČ')" />
                        <x-text-input id="residential_company_postal_code" name="residential_company_postal_code" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_ico" :value="__('IČO')" />
                        <x-text-input id="residential_company_ico" name="residential_company_ico" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_dic" :value="__('DIČ')" />
                        <x-text-input id="residential_company_dic" name="residential_company_dic" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_ic_dph" :value="__('IČ DPH')" />
                        <x-text-input id="residential_company_ic_dph" name="residential_company_ic_dph" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_iban" :value="__('IBAN')" />
                        <x-text-input id="residential_company_iban" name="residential_company_iban" type="text" class="mt-1 block w-full"  />
                    </div>
                    <div>
                        <x-input-label for="residential_company_bank_connection" :value="__('Bankové spojenie')" />
                        <x-text-input id="residential_company_bank_connection" name="residential_company_bank_connection" type="text" class="mt-1 block w-full" />
                    </div>
                </div>

                <!-- Výber typu faktúry -->
                <div class="mt-4">
                    <x-input-label for="invoice_type" :value="__('Typ faktúry')" />
                    <select id="invoice_type" name="invoice_type" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                        <option value="Hlavicka-Adresa-Nazov">{{ __('Hlavicka-Adresa-Nazov') }}</option>
                        <option value="Hlavicka-Nazov-Adresa">{{ __('Hlavicka-Nazov-Adresa') }}</option>
                        <option value="Adresa-Hlavicka-Nazov">{{ __('Adresa-Hlavicka-Nazov') }}</option>
                        <option value="Adresa-Nazov-Hlavicka">{{ __('Adresa-Nazov-Hlavicka') }}</option>
                        <option value="Nazov-Hlavicka-Adresa">{{ __('Nazov-Hlavicka-Adresa') }}</option>
                        <option value="Nazov-Adresa-Hlavicka">{{ __('Nazov-Adresa-Hlavicka') }}</option>
                    </select>
                </div>

                  <!-- Add at least one service -->
                  <div class="mt-4">
                    <h3 class="font-bold text-white text-xl">{{ __('Služby') }}</h3>
                    <div>
                        <x-input-label for="desc_services" :value="__('Veta nad sluzby:')" />
                        <x-text-input id="desc_services" name="desc_services" type="text" class="mt-1 block w-full"  />
                    </div>
                    
                    <div id="services-section">
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <x-input-label for="service_description[]" :value="__('Popis služby')" />
                                <input type="text" name="service_description[]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg" required />
                            </div>
                            <div>
                                <x-input-label for="service_price[]" :value="__('Cena služby')" />
                                <input type="text" name="service_price[]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg" required />
                            </div>
                        </div>
                    </div>

                    <button type="button" class="mt-4 bg-green-500 text-white py-2 px-4 rounded" onclick="addServiceRow()">
                        {{ __('Pridať ďalšiu službu') }}
                    </button>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>{{ __('Uložiť') }}</x-primary-button>
                </div>
            </form>
        </section>

        <!-- Section for listing streets -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Zoznam ulíc') }}
                </h2>
            
                <!-- Filter Bytového Podniku -->
                <div class="ml-4">
                    <x-input-label for="filter-company" :value="__('Vyberte bytový podnik')" />
                    <select id="filter-company" name="filter-company" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                        <option value="">{{ __('Všetky bytové podniky') }}</option>
                        @foreach ($residential_companies as $company)
                            <option value="{{ $company->id }}" {{ request('filter-company') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- Vyhľadávací box na celú šírku -->
            <div class="mb-6">
                <x-input-label for="street-search" :value="__('Vyhľadávanie ulice')" />
                <input type="text" id="street-search" placeholder="Vyhľadajte ulicu..." class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg" />
            </div>

            <div class="overflow-x-auto mt-4">
                <table id="street-table" class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Názov Ulice') }}</th>
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Popis do hlavičky') }}</th>
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Popis nad služby') }}</th>
                            <th class="px-4 py-2 w-2/3 text-left">{{ __('Služby') }}</th>
                            <th class="px-4 py-2 w-1/16 text-left">{{ __('Akcie') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($places as $place)
                            @if (request('filter-company') == '' || request('filter-company') == $place->residentialCompany->id)
                                <!-- Street Row -->
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 street-row">
                                    <td class="px-4 py-2">{{ $place->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($place->header, 50) }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($place->desc_above_service, 50) }}</td>
                                    <td class="px-4 py-2">
                                        <!-- Services Nested Table -->
                                        <table class="w-full table-auto bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                            <thead>
                                                <tr class="bg-gray-300 dark:bg-gray-600">
                                                    <th class="px-2 py-1 w-1/2 text-left">{{ __('Popis služby') }}</th>
                                                    <th class="px-2 py-1 w-1/4 text-left">{{ __('Cena služby') }}</th>
                                                    <th class="px-2 py-1 w-1/4 text-left">{{ __('Akcie') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($place->services as $service)
                                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <td class="px-2 py-1">{{ $service->service_description }}</td>
                                                        <td class="px-2 py-1">{{ $service->service_price }}</td>
                                                        <td class="px-2 py-1 flex space-x-2">
                                                            <button class="bg-blue-500 text-white py-1 px-2 rounded edit-service" data-id="{{ $service->id }}">{{ __('Upraviť') }}</button>
                                                            <form method="POST" action="{{ route('services.destroy', $service->id) }}" onsubmit="return confirm('Naozaj chcete vymazať túto službu?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-red-500 text-white py-1 px-2 rounded">{{ __('Vymazať') }}</button>
                                                            </form>
                                                        </td>
                                                    </tr>

                                                    <!-- Hidden Edit Service Row -->
                                                    <tr id="edit-service-row-{{ $service->id }}" class="hidden">
                                                        <td colspan="3">
                                                            <form method="POST" action="{{ route('services.update', $service->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <input type="text" name="service_description" value="{{ $service->service_description }}" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">
                                                                    <input type="text" name="service_price" value="{{ $service->service_price }}" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">
                                                                </div>
                                                                <div class="flex space-x-4 mt-2">
                                                                    <button type="submit" class="bg-green-500 text-white py-1 px-4 rounded">{{ __('Uložiť') }}</button>
                                                                    <button type="button" class="cancel-service-edit" data-id="{{ $service->id }}">{{ __('Zrušiť') }}</button>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <!-- Add New Service Row -->
                                                <tr>
                                                    <td colspan="3">
                                                        <form method="POST" action="{{ route('services.store', $place->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="place_id" value="{{ $place->id }}">
                                                            <div class="flex items-center space-x-4">
                                                                <input type="text" name="service_description" placeholder="Popis služby" class="block w-2/3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">
                                                                <input type="text" name="service_price" placeholder="Cena" class="block w-1/4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">
                                                                <button type="submit" class="bg-green-500 text-white py-1 px-2 rounded">{{ __('Pridať') }}</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    <!-- Place Actions -->
                                    <td class="px-4 py-2">
                                        <div class="flex flex-col space-y-2">
                                            <form method="GET" action="{{ route('places.edit', $place->id) }}">
                                                @csrf
                                                <button type="submit" class="bg-blue-500 text-white py-1 px-2 rounded">{{ __('Upraviť') }}</button>
                                            </form>
                                            <button type="button" class="bg-yellow-500 text-white py-1 px-2 rounded preview-button" data-id="{{ $place->id }}">
                                                {{ __('Náhľad') }}
                                            </button>
                                            <form method="POST" action="{{ route('places.destroy', $place->id) }}" onsubmit="return confirm('Naozaj chcete vymazať túto ulicu?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 text-white py-1 px-2 rounded">{{ __('Vymazať') }}</button>
                                            </form>
                                            <!-- Nové tlačidlo "Náhľad" -->
                                            
                                        </div>
                                    </td>
                                </tr>

                                <!-- Hidden Edit Place Row -->
                                <tr id="edit-row-{{ $place->id }}" class="hidden">
                                    <td colspan="4" class="p-4">
                                        <form method="POST" action="{{ route('places.update', $place->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <input type="text" name="name" value="{{ $place->name }}" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">
                                                </div>
                                                <div>
                                                    <textarea name="header" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">{{ $place->header }}</textarea>
                                                </div>
                                                <div>
                                                    <textarea name="desc_above_service" class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-900 text-white">{{ $place->desc_above_service }}</textarea>
                                                </div>
                                            </div>
                                            <div class="flex space-x-4 mt-2">
                                                <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded">{{ __('Uložiť') }}</button>
                                                <button type="button" class="cancel-edit" data-id="{{ $place->id }}">{{ __('Zrušiť') }}</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>            
        </section>
    </div>
<!-- Modal -->
<div id="preview-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 relative">
        <button id="close-modal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl">&times;</button>
        <iframe id="preview-pdf" src="" class="w-full h-[85vh]"></iframe>
    </div>
</div>



<script>
    let residentialCompanies = @json($residential_companies);

    // Dynamické filtrovanie ulíc
    document.getElementById('street-search').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#street-table tbody tr.street-row');

        rows.forEach(row => {
            let firstCell = row.querySelector('td:first-child');

            if (firstCell) {
                let streetName = firstCell.textContent.toLowerCase().trim();

                if (streetName.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
    document.getElementById('residential_company_id').addEventListener('change', function() {
            let companyId = this.value;

            if (companyId) {
                // Vyhľadávanie vybraného bytového podniku z JSON objektu
                let selectedCompany = residentialCompanies.find(company => company.id == companyId);

                if (selectedCompany) {
                    document.getElementById('residential_company_address').value = selectedCompany.address || '';
                    document.getElementById('residential_company_city').value = selectedCompany.city || '';
                    document.getElementById('residential_company_postal_code').value = selectedCompany.postal_code || '';
                    document.getElementById('residential_company_ico').value = selectedCompany.ico || '';
                    document.getElementById('residential_company_dic').value = selectedCompany.dic || '';
                    document.getElementById('residential_company_ic_dph').value = selectedCompany.ic_dph || '';
                    document.getElementById('residential_company_iban').value = selectedCompany.iban || '';
                    document.getElementById('residential_company_bank_connection').value = selectedCompany.bank_connection || '';
                }
            } else {
                // Reset fields if no company is selected
                document.getElementById('residential_company_address').value = '';
                document.getElementById('residential_company_city').value = '';
                document.getElementById('residential_company_postal_code').value = '';
                document.getElementById('residential_company_ico').value = '';
                document.getElementById('residential_company_dic').value = '';
                document.getElementById('residential_company_ic_dph').value = '';
                document.getElementById('residential_company_iban').value = '';
                document.getElementById('residential_company_bank_connection').value = '';
            }
        });

    // Filtrovanie podľa bytového podniku
    document.getElementById('filter-company').addEventListener('change', function () {
        let companyId = this.value;
        window.location.href = "{{ route('places.index') }}" + "?filter-company=" + companyId;
    });

    // Editovanie ulíc a služieb
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            document.getElementById('edit-row-' + id).classList.remove('hidden');
        });
    });

    document.querySelectorAll('.cancel-edit').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            document.getElementById('edit-row-' + id).classList.add('hidden');
        });
    });

    document.querySelectorAll('.edit-service').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            document.getElementById('edit-service-row-' + id).classList.remove('hidden');
        });
    });

    document.querySelectorAll('.cancel-service-edit').forEach(button => {
        button.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            document.getElementById('edit-service-row-' + id).classList.add('hidden');
        });
    });

    // Modal funkcionalita
    document.querySelectorAll('.preview-button').forEach(button => {
        button.addEventListener('click', function() {
            let placeId = this.getAttribute('data-id');
            let pdfUrl = "{{ route('places.invoice', ':id') }}".replace(':id', placeId);
            document.getElementById('preview-pdf').src = pdfUrl;
            document.getElementById('preview-modal').classList.remove('hidden');
        });
    });

    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('preview-modal').classList.add('hidden');
        document.getElementById('preview-pdf').src = ''; // Vyčistenie src pre zastavenie načítania PDF
    });

    // Zavretie modálu kliknutím mimo obsah
    document.getElementById('preview-modal').addEventListener('click', function(e) {
        if (e.target == this) {
            this.classList.add('hidden');
            document.getElementById('preview-pdf').src = '';
        }
    });

    function addServiceRow() {
        const servicesSection = document.getElementById('services-section');
        const newRow = `
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div>
                    <x-input-label for="service_description[]" :value="__('Popis služby')" />
                    <input type="text" name="service_description[]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg" required />
                </div>
                <div>
                    <x-input-label for="service_price[]" :value="__('Cena služby')" />
                    <input type="text" name="service_price[]" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg" required />
                </div>
            </div>
        `;
        servicesSection.insertAdjacentHTML('beforeend', newRow);
    }
</script>
@endsection
