<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->broj_fakture }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #414042;
            font-size: 13px;
        }

        .container {
            width: 210mm;
            height: 297mm;
            padding: 15mm 20mm;
            margin: 0 auto;
        }

        /* Header - Logo i Invoice info */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 200px;
            height: auto;
        }

        .invoice-info {
            text-align: right;
            font-size: 13px;
            line-height: 1.6;
        }

        .invoice-info strong {
            font-weight: 600;
        }

        /* Website bar */
        .website-bar {
            background-color: #e6e7e8;
            padding: 8px 15px;
            border-radius: 5px;
            text-align: right;
            margin-bottom: 25px;
            font-weight: 600;
        }

        /* Info section - Izdavalac i Primalac */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .info-box {
            width: 48%;
        }

        .info-box-title {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .info-box p {
            line-height: 1.6;
            font-size: 12px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background-color: #e6e7e8;
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #d0d0d0;
        }

        td {
            padding: 10px;
            border: 1px solid #d0d0d0;
            font-size: 13px;
        }

        /* Payment instruction note */
        .payment-note {
            font-size: 11px;
            color: #666;
            font-style: italic;
            margin-bottom: 15px;
        }

        /* Total */
        .total-section {
            background-color: #414042;
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            margin-bottom: 80px;
        }

        /* Signature lines */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 80px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #414042;
            padding-top: 18px;
            font-size: 11px;
            font-style: italic;
            color: #666;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e6e7e8;
            padding-top: 15px;
            text-align: center;
        }

        .footer p {
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        .footer .company-name {
            font-weight: 600;
        }

        .footer .note {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="https://wizionar.com/wp-content/uploads/2023/09/wizionarLogoAsset-7@2x.png" alt="Wizionar Logo">
            </div>
            <div class="invoice-info">
                <p><strong>Invoice Number: {{ str_replace('##', '#', $invoice->broj_fakture) }}</strong></p>
                <p style="margin-top: 12px; margin-bottom: 2px;">Invoice Date:</p>
                <p style="margin-top: 0;"><strong>{{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}, Miloševac</strong></p>
            </div>
        </div>

        <!-- Website bar -->
        <div class="website-bar">
            www.wizionar.com
        </div>

        <!-- Info section -->
        <div class="info-section">
            <div class="info-box">
                <p class="info-box-title">Računarsko programiranje "Wizionar"</p>
                <p>Aleksandra Davidović s.p. Miloševac</p>
                <p>Adresa: Mali lug 117, 74485 Miloševac</p>
                <p>JIB / JMB: 4512696590007</p>
                <p>IBAN: BA395676510000114506</p>
                <p>SWIFT: SABRBA2B</p>
                <p>Email: info@wizionar.com</p>
                <p>Tel: +387 66 / 882 - 702</p>
            </div>
            <div class="info-box">
                <p class="info-box-title">Invoice to:</p>
                <p>{{ $invoice->client->naziv_firme }}</p>
                <p>{{ $invoice->client->adresa }}</p>
                <p>{{ $invoice->client->postanski_broj_mjesto_drzava }}</p>
                <p>VAT: {{ $invoice->client->pdv_broj }}</p>
                <p>Email: {{ $invoice->client->email }}</p>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 60%;">Description</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 25%;">Amount (EUR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->opis_posla }}</td>
                    <td style="text-align: center;">{{ $invoice->kolicina }}</td>
                    <td style="text-align: right;">{{ number_format($invoice->cijena, 2) }} EUR</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment instruction note -->
        <div class="payment-note">
            Please ensure that the payment instruction is set to 'OUR'
        </div>

        <!-- Total -->
        <div class="total-section">
            <span>Total in EUR:</span>
            <span>{{ number_format($invoice->cijena, 2) }} EUR</span>
        </div>

        <!-- Signature lines -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Authorized by</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Customer</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="company-name">Računarsko programiranje "Wizionar" Aleksandra Davidović s.p. Miloševac</p>
            <p>JIB: 4512696590007 | IBAN: BA395676510000114506</p>
            <p class="note">Wizionar is not a part of the VAT system</p>
        </div>
    </div>
</body>
</html>
