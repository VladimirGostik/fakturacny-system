<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 700px; /* Pôvodne 800px, zmenšené na 700px */
            margin: 0 auto;
            padding: 0 10px;
        }
        .header {
            background-color: #f5f5f5;
            padding: 15px; /* Pôvodne 20px */
            text-align: center;
            border-bottom: 4px solid #2f5597;
        }
        .header h1 {
            margin: 0;
            font-size: 22px; /* Pôvodne 24px */
            color: #333;
        }
        .invoice-info {
            margin-top: 20px;
        }
        .company-info, .client-info {
            width: 48%; /* Pôvodne 49% */
            display: inline-block;
            vertical-align: top;
        }
        .client-info {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            color: #2f5597;
            font-size: 15px; /* Pôvodne 18px */
            margin-bottom: 15px; /* Pôvodne 20px */
        }
        .info-text {
            margin: 3px 0; /* Pôvodne 5px */
            color: #555;
            font-size: 13px; /* Zmenšené písmo */
        }
        .invoice-details {
            width: 100%;
            margin: 10px auto; /* Pôvodne 15px */
            background-color: #2f5597c0;
            border-radius: 8px;
            text-align: center;
            padding: 10px; /* Pridaný padding */
        }

        .invoice-details strong span {
            display: inline-block;
            margin: 0 10px; /* Pôvodne 15px */
            font-size: 13px; /* Zmenšené písmo */
        }

        .payment-details {
            background-color: #3576c0a6;
            padding: 15px; /* Pôvodne 20px */
            margin: 20px 0; /* Pôvodne 30px */
            border-radius: 10px;
            text-align: left;
            display: inline-block;
            border: 2px solid #2f5597;
            width: 92%; /* Zmenšená šírka */
        }

        .invoice-details p {
            margin: 5px 0;
            font-size: 18px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0; /* Pôvodne 30px */
            font-size: 13px; /* Zmenšené písmo */
        }
        .table th, .table td {
            padding: 10px 12px; /* Pôvodne 12px 15px */
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
            margin-top: 20px; /* Pôvodne 30px */
            text-align: right;
        }
        .total-section h2 {
            margin: 0;
            font-size: 20px; /* Pôvodne 22px */
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
            margin-top: 70px; /* Pôvodne 100px */
            width: 48%; /* Zmenšené */
            display: inline-block;
            vertical-align: top;
        }
        .signature-right {
            text-align: right;
        }
        .signature-line {
            margin-top: 30px; /* Pôvodne 40px */
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
            <h1>{{ __('Faktúra') }} č.            </h1>
        </div>
        <div class="invoice-details">
            <strong>
                <span>{{ __('Fakturačný mesiac:') }} {{ $invoice->billing_month }}</span>
                <span class="spacer">{{ __('Dátum vystavenia:') }} {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d-m-Y') }}</span>
                <span class="spacer">{{ __('Dátum splatnosti:') }} {{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</span>
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
                <p class="info-text">{{ __('IBAN:') }} {{ $invoice->company->iban }}</p>
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
            <div class="section-title">{{ __('Platobne udaje') }}</div>
            <p class="info-text"><strong>{{ __('IBAN:') }}</strong> {{ $invoice->company->iban }}</p>
            <p class="info-text"><strong>{{ __('Bankove spojenie:') }}</strong> {{ $invoice->company->bank_connection }}</p>
            <p class="info-text"><strong>{{ __('Forma úhrady:') }}</strong> Prevodom</p> <!-- Added payment method -->
        </div>
       
        @if(!empty($invoice->services->first()->desc_above_service))
            <div class="desc-above-service">
                {{ $invoice->services->first()->desc_above_service }}
            </div>
        @endif

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
