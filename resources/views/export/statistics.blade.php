<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $company->name }} - Statistics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Header styling */
        .header {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: white;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        /* Content area styling */
        .content {
            padding: 20px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        /* Summary section styling */
        .summary-section {
            margin-top: 20px;
            width: 50%;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .summary-table th, .summary-table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .summary-table th {
            background-color: #003366;
            color: white;
            text-align: left;
        }

        .summary-table td {
            background-color: #f4f4f4;
            text-align: left;

        }

        /* Footer styling */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: white;
            width: 100%;
            bottom: 0;
        }

        .footer p {
            margin: 0;
            font-size: 12px;
        }

    </style>
</head>
<body>

<!-- Header Section -->
<div class="header">
    <h2>{{ $company->name }}</h2>
    <p>IČO: {{ $company->ico }} | Rok: {{ substr($invoices->first()->issue_date, 0, 4) }} | Dňa: {{ now()->format('d.m.Y') }}</p>
</div>

<!-- Main Content Section -->
<div class="content">
    <!-- Invoice Table -->
    <table>
        <thead>
            <tr>
                <th>Dátum vydania</th>
                <th>Číslo faktúry</th>
                <th>Typ</th>
                <th>Firma</th>
                <th>Čiastka</th>
                <th>z toho DPH</th>
                <th>Splatné</th>
                <th>Uhradené</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->issue_date }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>F</td>
                    <td>{{ $invoice->residential_company_name }}</td>
                    <td>{{ number_format($invoice->services->sum('service_price'), 2, ',', ' ') }} €</td>
                    <td>0,00 €</td>
                    <td>{{ $invoice->due_date }}</td>
                    <td>{{ $invoice->payment_date ? $invoice->payment_date : ' ' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section with Mini Table on the Left -->
    <div class="summary-section">
        <h4>Súčet:</h4>
        <table class="summary-table">
            <tr>
                <th>Počet faktúr</th>
                <th>Celkom</th>
            </tr>
            <tr>
                <td>{{ $invoices->count() }}</td>
                <td>{{ number_format($invoices->sum(function ($invoice) { return $invoice->services->sum('service_price'); }), 2, ',', ' ') }} €</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
