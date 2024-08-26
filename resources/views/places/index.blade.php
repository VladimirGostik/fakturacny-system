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

        <!-- Section for listing streets -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Zoznam ulíc') }}
                </h2>
                <div class="w-1/2">
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

            <div class="overflow-x-auto mt-4">
                <table class="min-w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Názov Ulice') }}</th>
                            <th class="px-4 py-2 w-1/6 text-left">{{ __('Popis do hlavičky') }}</th>
                            <th class="px-4 py-2 w-2/3 text-left">{{ __('Služby') }}</th>
                            <th class="px-4 py-2 w-1/12 text-left">{{ __('Akcie') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($places as $place)
                            @if (request('filter-company') == '' || request('filter-company') == $place->residentialCompany->id)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" data-company-id="{{ $place->residentialCompany->id }}">
                                    <!-- Place Data -->
                                    <td class="px-4 py-2">{{ $place->name }}</td>
                                    <td class="px-4 py-2">{{ Str::limit($place->header, 50) }}</td>
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
                                            <button class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded edit-button" data-id="{{ $place->id }}">{{ __('Upraviť') }}</button>
                                            <form method="POST" action="{{ route('places.destroy', $place->id) }}" onsubmit="return confirm('Naozaj chcete vymazať toto miesto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded">{{ __('Vymazať') }}</button>
                                            </form>
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

                <div>
                    <x-input-label for="header" :value="__('Popis do hlavičky')" />
                    <textarea id="header" name="header" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24"></textarea>
                    <x-input-error :messages="$errors->get('header')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>{{ __('Uložiť') }}</x-primary-button>
                </div>
            </form>
        </section>
    </div>

<script>
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

    // Filtrovanie miest podľa bytového podniku
    document.getElementById('filter-company').addEventListener('change', function () {
        let companyId = this.value;
        window.location.href = "{{ route('places.index') }}" + "?filter-company=" + companyId;
    });
</script>
@endsection
