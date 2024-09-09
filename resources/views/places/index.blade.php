@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
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
                        <x-input-label for="desc_above_service" :value="__('Popis nad služby')" />
                        <textarea id="desc_above_service" name="desc_above_service" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24"></textarea>
                        <x-input-error :messages="$errors->get('desc_above_service')" class="mt-2" />
                    </div>
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
                <div class="ml-4"> <!-- Removed flex for label above select -->
                    <x-input-label for="filter-company" :value="__('Vyberte bytový podnik')" /> <!-- Label above select -->
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

            <!-- Filtrovanie podľa bytového podniku -->
            

            <div class="overflow-x-auto mt-4">
                <table id="street-table" class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Názov Ulice') }}</th>
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Popis do hlavičky') }}</th>
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Popis nad služby') }}</th> <!-- New column -->
                            <th class="px-4 py-2 w-2/3 text-left">{{ __('Služby') }}</th>
                            <th class="px-4 py-2 w-1/12 text-left">{{ __('Akcie') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($places as $place)
                            @if (request('filter-company') == '' || request('filter-company') == $place->residentialCompany->id)
                                <!-- Street Row -->
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 street-row"> <!-- Added class "street-row" -->
                                    <td class="px-4 py-2">{{ $place->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($place->header, 50) }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($place->desc_above_service, 50) }}</td> <!-- New field -->
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
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex flex-col space-y-2">
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded edit-button">{{ __('Upraviť') }}</button>
                                            <form method="POST" action="{{ route('places.destroy', $place->id) }}" onsubmit="return confirm('Naozaj chcete vymazať toto miesto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded">{{ __('Vymazať') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>            
        </section>
    </div>

<script>
    // Dynamické filtrovanie ulíc
    document.getElementById('street-search').addEventListener('input', function() {
    let filter = this.value.toLowerCase(); // Hľadaný výraz
    let rows = document.querySelectorAll('#street-table tbody tr.street-row'); // Výber riadkov s triedou "street-row"

    rows.forEach(row => {
        let firstCell = row.querySelector('td:first-child'); // Výber prvého stĺpca (názov ulice)
        
        // Skontroluj, či bunka existuje pred prístupom k textContent
        if (firstCell) {
            let streetName = firstCell.textContent.toLowerCase().trim(); // Názov ulice

            if (streetName.includes(filter)) {
                row.style.display = ''; // Zobraziť riadok a súvisiace služby
            } else {
                row.style.display = 'none'; // Skryť riadok
            }
        }
    });
});

    // Filtrovanie podľa bytového podniku
    document.getElementById('filter-company').addEventListener('change', function () {
        let companyId = this.value;
        window.location.href = "{{ route('places.index') }}" + "?filter-company=" + companyId;
    });

    // Editovanie ulíc a služieb (JavaScript je rovnaký ako vo vašom pôvodnom kóde)
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
</script>
@endsection
