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

        <!-- Tlačidlá na prepínanie -->
        <div class="flex justify-center mb-4 space-x-2">
            <a href="{{ route('companies.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Firmy') }}
            </a>
            <a href="{{ route('residential-companies.index') }}" class="bg-blue-700 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Odberatelia') }}
            </a>
            <a href="{{ route('places.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Ulice') }}
            </a>
        </div>

        <!-- Filter pre firmy -->
        <div class="w-1/2 mb-4">
            <x-input-label for="filter-company" :value="__('Vyberte firmu')" />
            <form method="GET" action="{{ route('residential-companies.index') }}">
                <select id="filter-company" name="company_id" onchange="this.form.submit()" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                    <option value="">{{ __('Všetky firmy') }}</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Tabuľka odberateľov -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Zoznam odberateľov') }}
                </h2>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700 rounded-lg">
                            <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600 rounded-tl-lg">{{ __('Názov odberateľa') }}</th>
                            <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600">{{ __('IČO') }}</th>
                            <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600">{{ __('Adressa') }}</th>
                            <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600 rounded-tr-lg">{{ __('Akcie') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($residential_companies as $residential_company)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $residential_company->name }}</td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $residential_company->ico }}</td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $residential_company->address }}</td>
                                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600 flex justify-end space-x-2">
                                    <a href="{{ route('residential-companies.edit', $residential_company->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Upraviť') }}</a>
                                    <form method="POST" action="{{ route('residential-companies.destroy', $residential_company->id) }}" onsubmit="return confirm('Naozaj chcete vymazať tohto odberateľa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">{{ __('Vymazať') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Formulár na vytvorenie nového odberateľa -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Vytvoriť nového odberateľa') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Vyplňte nižšie uvedené údaje na vytvorenie nového odberateľa.') }}
                </p>
            </header>

            <form method="POST" action="{{ route('residential-companies.store') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Výber firmy -->
                <div class="w-full mb-4">
                    <x-input-label for="company_id" :value="__('*Firma')" />
                    <select id="company_id" name="company_id" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                </div>

                <!-- Názov odberateľa a sídlo -->
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <x-input-label for="name" :value="__('*Názov odberateľa')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="address" :value="__('*Sídlo')" />
                        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" value="{{ old('address') }}" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>

                <!-- PSČ a Mesto -->
                <div class="flex space-x-4">
                    <div class="w-1/3">
                        <x-input-label for="postal_code" :value="__('*PSČ')" />
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" value="{{ old('postal_code') }}" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>

                    <div class="w-2/3">
                        <x-input-label for="city" :value="__('*Mesto')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ old('city') }}" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                </div>

                <!-- IČO a DIČ -->
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <x-input-label for="ico" :value="__('IČO')" />
                        <x-text-input id="ico" name="ico" type="text" class="mt-1 block w-full" value="{{ old('ico') }}" />
                        <x-input-error :messages="$errors->get('ico')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="dic" :value="__('DIČ')" />
                        <x-text-input id="dic" name="dic" type="text" class="mt-1 block w-full" value="{{ old('dic') }}" />
                        <x-input-error :messages="$errors->get('dic')" class="mt-2" />
                    </div>
                </div>

                <!-- IČ DPH, IBAN a Bankové spojenie -->
                <div class="flex space-x-4">
                    <div class="w-1/3">
                        <x-input-label for="ic_dph" :value="__('IČ DPH')" />
                        <x-text-input id="ic_dph" name="ic_dph" type="text" class="mt-1 block w-full" value="{{ old('ic_dph') }}" />
                        <x-input-error :messages="$errors->get('ic_dph')" class="mt-2" />
                    </div>

                    <div class="w-1/3">
                        <x-input-label for="iban" :value="__('IBAN')" />
                        <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" value="{{ old('iban') }}" />
                        <x-input-error :messages="$errors->get('iban')" class="mt-2" />
                    </div>

                    <div class="w-1/3">
                        <x-input-label for="bank_connection" :value="__('Bankové spojenie')" />
                        <x-text-input id="bank_connection" name="bank_connection" type="text" class="mt-1 block w-full" maxlength="10" value="{{ old('bank_connection') }}" />
                        <x-input-error :messages="$errors->get('bank_connection')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Uložiť') }}</x-primary-button>
                </div>
            </form>
        </section>
    </div>
@endsection
