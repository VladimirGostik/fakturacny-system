<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Vaše existujúce CSS štýly */
        /* ... */
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.6;
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
            padding: 15px;
            text-align: center;
            border-bottom: 4px solid #2f5597;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .invoice-info {
            margin-top: 20px;
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
            margin-bottom: 15px;
        }
        .info-text {
            margin: 3px 0;
            color: #555;
            font-size: 13px;
        }
        .invoice-details {
            width: 100%;
            margin: 10px auto;
            background-color: #2f5597c0;
            border-radius: 8px;
            text-align: center;
            padding: 10px;
        }

        .invoice-details strong span {
            display: inline-block;
            margin: 0 10px;
            font-size: 13px;
        }

        .payment-details {
            background-color: #3576c0a6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: left;
            display: inline-block;
            border: 2px solid #2f5597;
            width: 92%;
        }

        .invoice-details p {
            margin: 5px 0;
            font-size: 18px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        .table th, .table td {
            padding: 10px 12px;
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
        .table td.total {
            text-align: right;
            font-weight: bold;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-section h2 {
            margin: 0;
            font-size: 20px;
            color: #2f5597;
        }
        .footer {
            margin-top: 50px;
            padding: 10px 0;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
        }
        .signature-section {
            width: 100%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-section div {
            margin-top: 70px;
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .signature-right {
            text-align: right;
        }
        .signature-line {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 5px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ __('Faktúra') }} č. {{ $invoice->invoice_number }}</h1>
        </div>
        <div class="invoice-details">
            <strong>
                <span>{{ __('Fakturačný mesiac:') }} {{ __('XXXXXXX:') }}</span>
                <span class="spacer">{{ __('Dátum vystavenia:') }} {{ __('XXXXXXX:') }}</span>
                <span class="spacer">{{ __('Dátum splatnosti:') }} {{ __('XXXXXXX:') }}</span>
            </strong>
        </div>

        <!-- Company & Client Information -->
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
                    @if($item == 'Hlavicka') <!-- Hlavička -->
                        @if($invoice->services->isNotEmpty() && !empty($invoice->services->first()->place_header))
                            <p class="info-text">{{ $invoice->services->first()->place_header }}</p>
                        @else
                            <p class="info-text">{{ __('') }}</p>
                        @endif
                    @elseif($item == 'Nazov') <!-- Názov -->
                        <p class="info-text"><strong>{{ $invoice->residential_company_name }}</strong></p>
                    @elseif($item == 'Adresa') <!-- Adresa -->
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

            </div>
        </div>

        <div class="payment-details">
            <div class="section-title">{{ __('Platobne údaje') }}</div>
            <p class="info-text"><strong>{{ __('IBAN:') }}</strong> {{ $invoice->company->iban }}</p>
            <p class="info-text"><strong>{{ __('Bankové spojenie:') }}</strong> {{ $invoice->company->bank_connection }}</p>
            <p class="info-text"><strong>{{ __('Forma úhrady:') }}</strong> Prevodom</p> <!-- Added payment method -->
        </div>
       
        @php
            $billingMonth = $invoice->billing_month;
            $issueDate = $invoice->issue_date;
            $billingYear = ($billingMonth == 12) ? date('Y', strtotime($issueDate)) - 1 : date('Y', strtotime($issueDate));
        
            // Nahradenie reťazcov
            $descAboveService = $invoice->services->first()->desc_above_service ?? '';
            $descAboveService = str_replace('{mesiac/rok}', $billingMonth . '/' . $billingYear, $descAboveService);
            $descAboveService = str_replace('{mesiac}', $billingMonth, $descAboveService);
        @endphp
        
        <div class="desc-above-service">
            {!! $descAboveService !!}
        </div>

        <!-- Services Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Popis služby') }}</th>
                    <th>{{ __('Množstvo') }}</th>
                    <th>{{ __('Cena služby') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->services as $service)
                <tr>
                    <td>{{ $service->service_description }}</td>
                    <td>{{ __('1x') }}</td>
                    <td>{{ number_format($service->service_price, 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-section">
            <h2>{{ __('Celková cena:') }} {{ number_format($invoice->services->sum('service_price'), 2) }} €</h2>
        </div>

        <!-- Signature Section -->
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

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2024 {{ $invoice->company->name }}. {{ __('Všetky práva vyhradené.') }}</p>
        </div>
    </div>
</body>
</html>
