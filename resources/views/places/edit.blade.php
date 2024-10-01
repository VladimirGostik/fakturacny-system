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

    <section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mt-10">
        <header>
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('Upraviť ulicu') }}</h2>
        </header>
        <form method="POST" action="{{ route('places.update', $place->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="residential_company_id" :value="__('Bytový podnik')" />
                    <select id="residential_company_id" name="residential_company_id" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                        @foreach ($residentialCompanies as $company)
                            <option value="{{ $company->id }}" {{ $company->id == $place->residential_company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="name" :value="__('*Názov ulice')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $place->name }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="header" :value="__('Popis do hlavičky')" />
                    <textarea id="header" name="header" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24">{{ $place->header }}</textarea>
                    <x-input-error :messages="$errors->get('header')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="desc_above_service" :value="__('Popis nad služby: Pre vlozenie datumu vloz: {mesiac} alebo {mesiac/rok}')" />
                    <textarea id="desc_above_service" name="desc_above_service" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24">{{ $place->desc_above_service }}</textarea>
                    <x-input-error :messages="$errors->get('desc_above_service')" class="mt-2" />
                </div>
            </div>

            <!-- Polia pre informácie o Bytovom podniku -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="residential_company_address" :value="__('Adresa')" />
                    <x-text-input id="residential_company_address" name="residential_company_address" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_address }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_city" :value="__('Mesto')" />
                    <x-text-input id="residential_company_city" name="residential_company_city" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_city }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_postal_code" :value="__('PSČ')" />
                    <x-text-input id="residential_company_postal_code" name="residential_company_postal_code" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_postal_code }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_ico" :value="__('IČO')" />
                    <x-text-input id="residential_company_ico" name="residential_company_ico" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_ico }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_dic" :value="__('DIČ')" />
                    <x-text-input id="residential_company_dic" name="residential_company_dic" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_dic }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_ic_dph" :value="__('IČ DPH')" />
                    <x-text-input id="residential_company_ic_dph" name="residential_company_ic_dph" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_ic_dph }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_iban" :value="__('IBAN')" />
                    <x-text-input id="residential_company_iban" name="residential_company_iban" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_iban }}"  />
                </div>
                <div>
                    <x-input-label for="residential_company_bank_connection" :value="__('Bankové spojenie')" />
                    <x-text-input id="residential_company_bank_connection" name="residential_company_bank_connection" type="text" class="mt-1 block w-full" value="{{ $place->residential_company_bank_connection }}"  />
                </div>
            </div>

            <!-- Výber typu faktúry -->
            <div class="mt-4">
                <x-input-label for="invoice_type" :value="__('Typ faktúry')" />
                <select id="invoice_type" name="invoice_type" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-lg">
                    <option value="Hlavicka-Adresa-Nazov" {{ $place->invoice_type == 'Hlavicka-Adresa-Nazov' ? 'selected' : '' }}>Hlavicka-Adresa-Nazov</option>
                    <option value="Hlavicka-Nazov-Adresa" {{ $place->invoice_type == 'Hlavicka-Nazov-Adresa' ? 'selected' : '' }}>Hlavicka-Nazov-Adresa</option>
                    <option value="Adresa-Hlavicka-Nazov" {{ $place->invoice_type == 'Adresa-Hlavicka-Nazov' ? 'selected' : '' }}>Adresa-Hlavicka-Nazov</option>
                    <option value="Adresa-Nazov-Hlavicka" {{ $place->invoice_type == 'Adresa-Nazov-Hlavicka' ? 'selected' : '' }}>Adresa-Nazov-Hlavicka</option>
                    <option value="Nazov-Hlavicka-Adresa" {{ $place->invoice_type == 'Nazov-Hlavicka-Adresa' ? 'selected' : '' }}>Nazov-Hlavicka-Adresa</option>
                    <option value="Nazov-Adresa-Hlavicka" {{ $place->invoice_type == 'Nazov-Adresa-Hlavicka' ? 'selected' : '' }}>Nazov-Adresa-Hlavicka</option>
                </select>
            </div>
            <div>
                <x-input-label for="desc_services" :value="__('Veta nad sluzby:')" />
                <x-text-input id="desc_services" name="desc_services" type="text" class="mt-1 block w-full"  value="{{ $place->desc_services}}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>{{ __('Uložiť') }}</x-primary-button>
            </div>
        </form>
    </section>
</div>
<script>
    let residentialCompanies = @json($residentialCompanies);

// Event Listener pre výber Bytového podniku
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
</script>

@endsection
