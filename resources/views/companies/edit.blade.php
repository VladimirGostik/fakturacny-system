@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Hláška o úspechu -->
        @if (session('status'))
            <div class="bg-green-500 text-white p-4 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulár na úpravu firmy -->
        <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <header>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Upraviť firmu') }}
                </h2>
            </header>

            <form method="POST" action="{{ route('companies.update', $company->id) }}" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Názov firmy -->
                <div>
                    <x-input-label for="company_name" :value="__('*Názov firmy')" />
                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" value="{{ $company->name }}" required autofocus />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <!-- Sídlo firmy -->
                <div>
                    <x-input-label for="company_address" :value="__('*Sídlo firmy')" />
                    <x-text-input id="company_address" name="company_address" type="text" class="mt-1 block w-full" value="{{ $company->address }}" required />
                    <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
                </div>

                <!-- PSČ a Mesto -->
                <div class="flex space-x-4">
                    <div class="w-1/3">
                        <x-input-label for="postal_code" :value="__('*PSČ')" />
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" value="{{ $company->postal_code }}" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>

                    <div class="w-2/3">
                        <x-input-label for="city" :value="__('*Mesto')" />
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ $company->city }}" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>
                </div>

                <!-- IČO a DIČ -->
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <x-input-label for="company_ico" :value="__('*IČO')" />
                        <x-text-input id="company_ico" name="company_ico" type="text" class="mt-1 block w-full" value="{{ $company->ico }}" required />
                        <x-input-error :messages="$errors->get('company_ico')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="company_dic" :value="__('*DIČ')" />
                        <x-text-input id="company_dic" name="company_dic" type="text" class="mt-1 block w-full" value="{{ $company->dic }}" required />
                        <x-input-error :messages="$errors->get('company_dic')" class="mt-2" />
                    </div>
                </div>

                <!-- IBAN a Bankové spojenie -->
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <x-input-label for="company_iban" :value="__('*IBAN')" />
                        <x-text-input id="company_iban" name="company_iban" type="text" class="mt-1 block w-full" value="{{ $company->iban }}" required />
                        <x-input-error :messages="$errors->get('company_iban')" class="mt-2" />
                    </div>

                    <div class="w-1/2">
                        <x-input-label for="bank_connection" :value="__('*Bankové spojenie')" />
                        <x-text-input id="bank_connection" name="bank_connection" type="text" class="mt-1 block w-full" maxlength="10" value="{{ $company->bank_connection }}" required />
                        <x-input-error :messages="$errors->get('bank_connection')" class="mt-2" />
                    </div>
                </div>

                <!-- Tlačidlá na uloženie a návrat -->
                <div class="flex items-center justify-between">
                    <x-primary-button>{{ __('Uložiť zmeny') }}</x-primary-button>
                    <a href="{{ route('companies.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Návrat') }}
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
