<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 0 10px;
        }
        .header {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: center;
            border-bottom: 4px solid #2f5597;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .invoice-info {
            margin-top: 10px;
        }
        .company-info, .client-info {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .client-info {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            color: #2f5597;
            font-size: 15px;
            margin-bottom: 10px;
        }
        .info-text {
            margin: 2px 0;
            color: #555;
            font-size: 13px;
        }
        .invoice-details {
            width: 100%;
            margin: 5px auto;
            background-color: #2f5597c0;
            border-radius: 8px;
            text-align: center;
            padding: 5px;
        }
        .invoice-details strong span {
            display: inline-block;
            margin: 0 5px;
            font-size: 13px;
        }
        .payment-details {
            background-color: #3576c0a6;
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            text-align: left;
            border: 2px solid #2f5597;
            width: 92%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 13px;
        }
        .table th, .table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #2f5597;
            color: white;
        }
        .table td {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 10px;
            text-align: right;
        }
        .total-section h2 {
            margin: 0;
            font-size: 20px;
            color: #2f5597;
        }
        /* Nové štýly pre umiestnenie podpisu a pätičky */
        .push {
            height: auto;
        }
        .signature-section {
            width: 100%;
            margin-top: 20px;
            display: block;
        }
        .signature-section div {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .signature-left {
            text-align: left;
        }
        .signature-right {
            text-align: right;
        }
        .signature-line {
            margin-top: 20px;
            border-top: 1px solid #333;
            padding-top: 5px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding: 5px 0;
            margin-top: 20px;
        }
        /* Pridané pre tlačenie podpisu a pätičky na spodok */
        .wrapper {
            min-height: 100%;
            position: relative;
        }
        .content {
            padding-bottom: 150px; /* Výška podpisu a pätičky */
        }
        .signature-section, .footer {
            position: absolute;
            align-items: center;
            bottom: 0;
            left: 0;
            width: 100%;
        }
        .signature-section {
            bottom: 50px; /* Výška pätičky */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container content">
            <!-- Hlavička -->
            <div class="header">
                <h1>{{ __('Faktúra') }} č.        </h1>
            </div>
            <div class="invoice-details">
                <strong>
                    <span>{{ __('Fakturačný mesiac:') }} {{ $invoice->billing_month }}</span>
                    <span class="spacer">{{ __('Dátum vystavenia:') }} {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d-m-Y') }}</span>
                    <span class="spacer">{{ __('Dátum splatnosti:') }} {{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</span>
                </strong>
            </div>

            <!-- Informácie o firme a klientovi -->
            <div class="invoice-info">
                <div class="company-info">
                    <div class="section-title">{{ __('Dodávateľ') }}</div>
                    <p class="info-text"><strong>{{ $invoice->company->name }}</strong></p>
                    <p class="info-text">{{ $invoice->company->address }}</p>
                    <p class="info-text">{{ $invoice->company->city }}, {{ $invoice->company->postal_code }}</p>
                    <p class="info-text">{{ __('IČO:') }} {{ $invoice->company->ico }}</p>
                    <p class="info-text">{{ __('DIČ:') }} {{ $invoice->company->dic }}</p>
                </div>

                <div class="client-info">
                    <div class="section-title">{{ __('Odoberateľ') }}</div>
                    @php
                    $order = explode('-', $invoice->invoice_type);
                    @endphp

                    @foreach($order as $item)
                        @if($item == 'Hlavicka')
                            @if($invoice->services->isNotEmpty() && !empty($invoice->services->first()->place_header))
                                <p class="info-text">{{ $invoice->services->first()->place_header }}</p>
                            @endif
                        @elseif($item == 'Nazov')
                            <p class="info-text"><strong>{{ $invoice->residential_company_name }}</strong></p>
                        @elseif($item == 'Adresa')
                            <p class="info-text">{{ $invoice->residential_company_address }}, {{ $invoice->residential_company_postal_code }}, {{ $invoice->residential_company_city }}</p>
                        @endif
                    @endforeach

                    @if(!empty($invoice->residential_company_ico))
                        <p class="info-text">{{ __('IČO:') }} {{ $invoice->residential_company_ico }}</p>
                    @endif

                    @if(!empty($invoice->residential_company_dic))
                        <p class="info-text">{{ __('DIČ:') }} {{ $invoice->residential_company_dic }}</p>
                    @endif

                    @if(!empty($invoice->residential_company_ic_dph))
                        <p class="info-text">{{ __('IČ DPH:') }} {{ $invoice->residential_company_ic_dph }}</p>
                    @endif
                    @if(!empty($invoice->residential_company_iban))
                        <p class="info-text">{{ __('IBAN:') }} {{ $invoice->residential_company_iban }}</p>
                    @endif
                </div>
            </div>

            <div class="payment-details">
                <div class="section-title">{{ __('Platobné údaje') }}</div>
                <p class="info-text"><strong>{{ __('IBAN:') }}</strong> {{ $invoice->company->iban }}</p>
                <p class="info-text"><strong>{{ __('Bankové spojenie:') }}</strong> {{ $invoice->company->bank_connection }}</p>
                <p class="info-text"><strong>{{ __('Forma úhrady:') }}</strong> Prevodom</p>
            </div>

            @php
                $billingMonth = $invoice->billing_month;
                $issueDate = $invoice->issue_date;
                $billingYear = ($billingMonth == 12) ? date('Y', strtotime($issueDate)) - 1 : date('Y', strtotime($issueDate));

                $descAboveService = $invoice->services->first()->desc_above_service ?? '';
                $descAboveService = str_replace('{mesiac/rok}', $billingMonth . '/' . $billingYear, $descAboveService);
                $descAboveService = str_replace('{mesiac}', $billingMonth, $descAboveService);
            @endphp

            <div class="desc-above-service">
                {!! $descAboveService !!}
            </div>

            <!-- Tabuľka služieb -->
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Popis služby') }}</th>
                        <th>{{ __('Množstvo') }}</th>
                        <th>{{ __('Cena služby') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($invoice->desc_services))
                        <tr>
                            <td colspan="3" style="text-align: left;">
                                {{ $invoice->desc_services }}
                            </td>
                        </tr>
                    @endif
                    @foreach ($invoice->services as $service)
                    <tr>
                        <td>{{ $service->service_description }}</td>
                        <td>{{ __('1x') }}</td>
                        <td>{{ number_format($service->service_price, 2) }} €</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Celková cena -->
            <div class="total-section">
                <h2>{{ __('Celková cena:') }} {{ number_format($invoice->services->sum('service_price'), 2) }} €</h2>
            </div>
        </div>

        <!-- Sekcia s podpisom -->
        <div class="signature-section">
            <div class="signature-left">
                <p>{{ __('Vyhotovil:') }} <strong>{{ $user->name }}</strong></p>
                <div class="signature-line">{{ __('Podpis') }}</div>
            </div>
            <div class="signature-right">
                <p>{{ __('Prevzal:') }}</p>
                <div class="signature-line">{{ __('Podpis') }}</div>
            </div>
        </div>

        <!-- Pätička -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $invoice->company->name }}. {{ __('Všetky práva vyhradené.') }}</p>
        </div>
    </div>
</body>
</html>
