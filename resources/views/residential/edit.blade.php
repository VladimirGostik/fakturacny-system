@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Hláška o úspechu -->
        @if (session('status'))
            <div class="bg-green-500 text-white p-4 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Hláška o neúspechu (zlyhaní) -->
        @if ($errors->any())
            <div class="bg-red-500 text-white p-4 rounded-lg">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex justify-center mb-4 space-x-2">
            <!-- Tlačidlo pre Firmy -->
            <a href="{{ route('companies.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Firmy') }}
            </a>
        
            <!-- Tlačidlo pre Odberateľov -->
            <a href="{{ route('residential-companies.index') }}" class="bg-blue-700 hover:bg-blue-500 text-white hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Odberatelia') }}
            </a>
        
            <!-- Tlačidlo pre Miesta -->
            <a href="{{ route('places.index') }}" class="bg-transparent hover:bg-blue-500 text-gray-500 hover:text-white font-bold py-2 px-4 rounded transition-colors duration-300">
                {{ __('Ulice') }}
            </a>
        </div>

        <!-- Formulár na úpravu odberateľa -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Upraviť odberateľa') }}
                </h2>
            </header>

            <form method="POST" action="{{ route('residential-companies.update', $residential_company->id) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="company_id" :value="__('*Firma')" />
                    <select id="company_id" name="company_id" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @if($company->id == $residential_company->company_id) selected @endif>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                

                <!-- Názov odberateľa -->
                <div>
                    <x-input-label for="name" :value="__('*Názov odberateľa')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $residential_company->name }}" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Sídlo -->
                <div>
                    <x-input-label for="address" :value="__('*Sídlo')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" value="{{ $residential_company->address }}" required />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <!-- PSČ a Mesto -->
                <div class="flex space-x-4">
                    <div class="w-1/3">
                        <x-input-label for="postal_code" :value="__('*PSČ')" />
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" value="{{ $residential_company->postal_code }}" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>

                    <div class="w-2/3">
                        <x-input-label for="city" :value="__('*Mesto')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ $residential_company->city }}" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                </div>

                <!-- IČO a DIČ -->
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <x-input-label for="ico" :value="__('IČO')" />
                        <x-text-input id="ico" name="ico" type="text" class="mt-1 block w-full" value="{{ $residential_company->ico }}" required />
                        <x-input-error :messages="$errors->get('ico')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="company_dic" :value="__('DIČ')" />
                        <x-text-input id="company_dic" name="company_dic" type="text" class="mt-1 block w-full" value="{{ $residential_company->dic }}" />
                        <x-input-error :messages="$errors->get('company_dic')" class="mt-2" />
                    </div>
                </div>

                <!-- IČ DPH, IBAN a Bankové spojenie -->
                <div class="flex space-x-4">
                    <div class="w-1/3">
                        <x-input-label for="ic_dph" :value="__('IČ DPH')" />
                        <x-text-input id="ic_dph" name="ic_dph" type="text" class="mt-1 block w-full" value="{{ $residential_company->ic_dph }}" />
                        <x-input-error :messages="$errors->get('ic_dph')" class="mt-2" />
                    </div>

                    <div class="w-1/3">
                        <x-input-label for="iban" :value="__('IBAN')" />
                        <x-text-input id="iban" name="iban" type="text" class="mt-1 block w-full" value="{{ $residential_company->iban }}" />
                        <x-input-error :messages="$errors->get('iban')" class="mt-2" />
                    </div>

                    <div class="w-1/3">
                        <x-input-label for="bank_connection" :value="__('Bankové spojenie')" />
                        <x-text-input id="bank_connection" name="bank_connection" type="text" class="mt-1 block w-full" maxlength="10" value="{{ $residential_company->bank_connection }}" />
                        <x-input-error :messages="$errors->get('bank_connection')" class="mt-2" />
                    </div>
                </div>

                <!-- Tlačidlá na uloženie a návrat -->
                <div class="flex items-center justify-between">
                    <x-primary-button>{{ __('Uložiť zmeny') }}</x-primary-button>
                    <a href="{{ route('residential-companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Návrat') }}
                    </a>                    
                </div>
            </form>
        </section>
    </div>
@endsection
