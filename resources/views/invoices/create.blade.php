@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">{{ __('Vytvoriť novú faktúru') }}</h1>

        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf

            <!-- Číslo faktúry, firma a bytový podnik -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="invoice_number" :value="__('Číslo faktúry')" />
                    <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="company_id" :value="__('Firma')" />
                    <select id="company_id" name="company_id" class="mt-1 block w-full">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="residential_company_id" :value="__('Bytový podnik')" />
                    <select id="residential_company_id" name="residential_company_id" class="mt-1 block w-full">
                        @foreach ($residential_companies as $residential_company)
                            <option value="{{ $residential_company->id }}">{{ $residential_company->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Dátumy a mesiac -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <x-input-label for="issue_date" :value="__('Dátum vytvorenia')" />
                    <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="due_date" :value="__('Dátum splatnosti')" />
                    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" required />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="billing_month" :value="__('Mesiac fakturácie')" />
                <x-text-input id="billing_month" name="billing_month" type="text" class="mt-1 block w-full" required />
            </div>

            <!-- Výber služieb -->
            <div class="mt-6">
                <h2 class="text-lg font-bold">{{ __('Vyberte služby') }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($places as $place)
                        <div class="p-4 border rounded-lg">
                            <h3 class="font-bold">{{ $place->name }}</h3>
                            <p class="text-gray-600">{{ $place->header }}</p>
                            @foreach ($place->services as $service)
                                <div>
                                    <input type="checkbox" id="service-{{ $service->id }}" name="services[{{ $service->id }}][id]" value="{{ $service->id }}">
                                    <label for="service-{{ $service->id }}">{{ $service->service_description }} ({{ $service->service_price }} €)</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button>{{ __('Uložiť') }}</x-primary-button>
            </div>
        </form>
    </div>
@endsection
