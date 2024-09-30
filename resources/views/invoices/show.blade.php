@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 text-center">{{ __('Detaily faktúry') }}</h1>

    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Faktúra') }} #{{ $invoice->invoice_number }}</h2>

        <!-- Informácie o faktúre -->
        <div class="grid grid-cols-2 gap-12">
            <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Informácie o spoločnosti') }}</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>{{ $invoice->company->name }}</strong></p>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->company->address }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->company->city }}, {{ $invoice->company->postal_code }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('IČO:') }} {{ $invoice->company->ico }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('DIČ:') }} {{ $invoice->company->dic }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('IČ DPH:') }} {{ $invoice->company->ic_dph }}</p>
            </div>

            <div class="border border-gray-300 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Informácie o bytovom podniku') }}</h3>
                <p class="text-gray-600 dark:text-gray-400"><strong>{{ $invoice->residential_company_name }}</strong></p>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->residential_company_address }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->residential_company_city }}, {{ $invoice->residential_company_postal_code }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('IČO:') }} {{ $invoice->residential_company_ico }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('DIČ:') }} {{ $invoice->residential_company_dic }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ __('IČ DPH:') }} {{ $invoice->residential_company_ic_dph }}</p>
            </div>
        </div>

        <!-- Dátumy faktúry -->
        <div class="mt-8">
            <div class="flex justify-between">
                <p class="text-lg text-gray-700 dark:text-gray-300"><strong>{{ __('Dátum vystavenia:') }}</strong> {{ $invoice->issue_date }}</p>
                <p class="text-lg text-gray-700 dark:text-gray-300"><strong>{{ __('Dátum splatnosti:') }}</strong> {{ $invoice->due_date }}</p>
                <p class="text-lg text-gray-700 dark:text-gray-300"><strong>{{ __('Fakturačný mesiac:') }}</strong> {{ $invoice->billing_month }}</p>
            </div>
        </div>

        @if(!empty($invoice->services->first()->desc_above_service))
            <div class="mt-6 text-center">
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ $invoice->services->first()->desc_above_service }}</p>
            </div>
        @endif

        <!-- Tabuľka služieb -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('Služby') }}</h3>
            <div class="mt-4">
                <table class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <th class="px-6 py-3 text-left">{{ __('Popis služby') }}</th>
                            <th class="px-6 py-3 text-left">{{ __('Cena služby') }}</th>
                            <th class="px-6 py-3 text-left">{{ __('Názov miesta') }}</th>
                            <th class="px-6 py-3 text-left">{{ __('Popis miesta') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($invoice->services as $service)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">{{ $service->service_description }}</td>
                                <td class="px-6 py-4">{{ number_format($service->service_price, 2) }} €</td>
                                <td class="px-6 py-4">{{ $service->place_name }}</td>
                                <td class="px-6 py-4">{{ $service->place_header }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Celková cena -->
        <div class="mt-8 text-right">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Celková cena:') }} 
                {{ number_format($invoice->services->sum('service_price'), 2) }} €
            </h3>
        </div>

        <!-- Tlačidlo späť -->
        <!-- Tlačidlo na úpravu faktúry -->
    <div class="mt-6 flex justify-between">
        <a href="{{ route('invoices.edit', $invoice) }}" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600">{{ __('Upraviť faktúru') }}</a>
        <button type="button" class="bg-yellow-500 text-white py-2 px-4 rounded preview-button" data-id="{{ $invoice->id }}">
            {{ __('Náhľad') }}
        </button>
        <a href="{{ route('invoices.index') }}" class="bg-blue-500 text-white py-2 px-4 rounded">{{ __('Späť na faktúry') }}</a>
    </div>

    </div>
</div>
<div id="preview-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 max-w-5xl relative transform transition-transform duration-300 scale-95">
        <button id="close-modal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white text-2xl">&times;</button>
        <iframe id="preview-pdf" src="" class="w-full h-[85vh]"></iframe>
    </div>
</div>
<script>
document.querySelectorAll('.preview-button').forEach(button => {
    button.addEventListener('click', function() {
        let invoiceId = this.getAttribute('data-id');
        let pdfUrl = "{{ route('invoices.downloadPDF', ':id') }}".replace(':id', invoiceId);
        document.getElementById('preview-pdf').src = pdfUrl;
        let modal = document.getElementById('preview-modal');
        modal.classList.remove('hidden');
        // Pridajte triedy pre animáciu
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
        }, 10); // Malé oneskorenie pre prechod
    });
});

document.getElementById('close-modal').addEventListener('click', function() {
    let modal = document.getElementById('preview-modal');
    // Pridajte triedy pre animáciu zatvorenia
    modal.classList.remove('opacity-100');
    modal.querySelector('div').classList.add('scale-95');
    modal.querySelector('div').classList.remove('scale-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.getElementById('preview-pdf').src = ''; // Vyčistenie src pre zastavenie načítania PDF
    }, 300); // Trvanie musí zodpovedať transition duration
});

// Close modal when clicking outside the content
document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target == this) {
        let modal = document.getElementById('preview-modal');
        modal.classList.remove('opacity-100');
        modal.querySelector('div').classList.add('scale-95');
        modal.querySelector('div').classList.remove('scale-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('preview-pdf').src = '';
        }, 300);
    }
});
</script>
@endsection
