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
        <!-- Formulár na pridanie novej firmy -->
        <div class="flex justify-center mb-4 space-x-2">
            <!-- Tlačidlo pre Firmy -->
            <a href="{{ route('companies.index') }}" class="bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Firmy') }}
            </a>
        
            <!-- Tlačidlo pre Odberateľov -->
            <a href="{{ route('residential-companies.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Odberatelia') }}
            </a>
        
            <!-- Tlačidlo pre Miesta -->
            <a href="{{ route('places.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Ulice') }}
            </a>
        </div>


            <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Zoznam firiem') }}
                    </h2>
                </header>
    
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 rounded-lg">
                                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600 rounded-tl-lg">{{ __('Názov firmy') }}</th>
                                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600">{{ __('IČO') }}</th>
                                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600">{{ __('DIC') }}</th>
                                <th class="px-4 py-2 border-b border-gray-300 dark:border-gray-600 rounded-tr-lg">{{ __('Akcie') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                    <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $company->name }}</td>
                                    <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $company->ico }}</td>
                                    <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">{{ $company->dic }}</td>
                                    <td class="border px-4 py-2 border-gray-300 dark:border-gray-600 flex justify-end space-x-2">
                                        <a href="{{ route('companies.edit', $company->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Upraviť') }}</a>
                                        <form method="POST" action="{{ route('companies.destroy', $company->id) }}" onsubmit="return confirm('Naozaj chcete vymazať túto firmu?');">
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

            <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Vytvorenie novej firmy') }}
                    </h2>
                </header>
            <form method="POST" action="{{ route('companies.store') }}" class="mt-6 space-y-6">
                @csrf

                <!-- Flex kontajner pre Názov firmy a Sídlo -->
                <div class="flex space-x-4">
                    <!-- Názov firmy -->
                    <div class="w-1/2">
                        <x-input-label for="company_name" :value="__('*Názov firmy')" />
                        <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                    </div>

                    <!-- Sídlo firmy -->
                    <div class="w-1/2">
                        <x-input-label for="company_address" :value="__('*Sídlo firmy')" />
                        <x-text-input id="company_address" name="company_address" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
                    </div>
                </div>

                <!-- Flex kontajner pre PSČ a Mesto -->
                <div class="flex space-x-4">
                    <!-- Poštové smerovacie číslo -->
                    <div class="w-1/3">
                        <x-input-label for="postal_code" :value="__('*PSČ')" />
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>

                    <!-- Mesto -->
                    <div class="w-2/3">
                        <x-input-label for="city" :value="__('*Mesto')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                </div>

                <!-- Flex kontajner pre IČO a DIČ -->
                <div class="flex space-x-4">
                    <!-- IČO firmy -->
                    <div class="w-1/2">
                        <x-input-label for="company_ico" :value="__('*IČO')" />
                        <x-text-input id="company_ico" name="company_ico" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('company_ico')" class="mt-2" />
                    </div>

                    <!-- DIČ firmy -->
                    <div class="w-1/2">
                        <x-input-label for="company_dic" :value="__('*DIČ')" />
                        <x-text-input id="company_dic" name="company_dic" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('company_dic')" class="mt-2" />
                    </div>
                </div>

                <!-- IBAN a Bankové spojenie -->
                <div class="flex space-x-4">
                    <!-- IBAN firmy -->
                    <div class="w-1/2">
                        <x-input-label for="company_iban" :value="__('*IBAN')" />
                        <x-text-input id="company_iban" name="company_iban" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('company_iban')" class="mt-2" />
                    </div>

                    <!-- Bankové spojenie -->
                    <div class="w-1/2">
                        <x-input-label for="bank_connection" :value="__('*Bankové spojenie')" />
                        <x-text-input id="bank_connection" name="bank_connection" type="text" class="mt-1 block w-full" maxlength="10" required />
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
