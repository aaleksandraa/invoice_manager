<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Faktura {{ $invoice->broj_fakture }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #414042;
            font-size: 10px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            padding: 10mm 15mm;
        }

        /* Header - Logo i Račun info */
        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            border: none;
        }

        .logo-cell {
            width: 50%;
            text-align: left;
        }

        .logo-cell img {
            width: 120px;
            height: auto;
        }

        .invoice-info-cell {
            width: 50%;
            text-align: right;
            font-size: 10px;
            line-height: 1.6;
        }

        .invoice-info-cell strong {
            font-weight: bold;
        }

        /* Website bar */
        .website-bar {
            background-color: #e6e7e8;
            padding: 6px 10px;
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 10px;
        }

        /* Info section - Izdavalac i Primalac */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            border: none;
            padding: 0 10px 0 0;
            font-size: 9px;
            line-height: 1.5;
        }

        .info-table .left-cell {
            width: 48%;
        }

        .info-table .right-cell {
            width: 48%;
        }

        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .invoice-table thead {
            background-color: #e6e7e8;
        }

        .invoice-table th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #d0d0d0;
        }

        .invoice-table td {
            padding: 8px;
            border: 1px solid #d0d0d0;
            font-size: 10px;
        }

        /* Total */
        .total-section {
            background-color: #414042;
            color: white;
            padding: 10px;
            margin-bottom: 60px;
            font-weight: bold;
            font-size: 10px;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            border: none;
            color: white;
        }

        .total-table .label {
            text-align: left;
            width: 50%;
        }

        .total-table .amount {
            text-align: right;
            width: 50%;
        }

        /* Signature lines */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
        }

        .signature-table td {
            width: 45%;
            text-align: center;
            border: none;
            padding: 0 10px;
        }

        .signature-line {
            border-top: 1px solid #414042;
            padding-top: 5px;
            font-size: 8px;
            font-style: italic;
            color: #666;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #e6e7e8;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            line-height: 1.5;
        }

        .footer .company-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .footer .note {
            font-style: italic;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-cell">
                        <img src="https://wizionar.com/wp-content/uploads/2023/09/wizionarLogoAsset-7@2x.png" alt="Wizionar Logo">
                    </td>
                    <td class="invoice-info-cell">
                        <strong>Račun br.: {{ $invoice->broj_fakture }}</strong><br>
                        Datum i mjesto izdavanja: <strong>{{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}., Miloševac</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Website bar -->
        <div class="website-bar">
            www.wizionar.com
        </div>

        <!-- Info section -->
        <table class="info-table">
            <tr>
                <td class="left-cell">
                    <div class="info-title">Računarsko programiranje "Wizionar"</div>
                    Aleksandra Davidović s.p. Miloševac<br>
                    Adresa: Mali lug 117, 74485 Miloševac<br>
                    JIB / JMB: 4512696590007<br>
                    Račun AtosBank: 5676512500038858<br>
                    Email: info@wizionar.com<br>
                    Telefon: +387 66 / 882 - 702
                </td>
                <td class="right-cell">
                    <div class="info-title">Račun za:</div>
                    {{ $invoice->client->naziv_firme }}<br>
                    {{ $invoice->client->adresa }}<br>
                    {{ $invoice->client->postanski_broj_mjesto_drzava }}<br>
                    JIB: {{ $invoice->client->pdv_broj }}<br>
                    Email: {{ $invoice->client->email }}
                </td>
            </tr>
        </table>

        <!-- Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Detaljan opis posla</th>
                    <th style="width: 15%; text-align: center;">Količina</th>
                    <th style="width: 25%; text-align: right;">Cijena (KM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->opis_posla }}</td>
                    <td style="text-align: center;">{{ $invoice->kolicina }}</td>
                    <td style="text-align: right;">{{ number_format($invoice->cijena, 2) }} KM</td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="label">Ukupna cijena:</td>
                    <td class="amount">{{ number_format($invoice->cijena, 2) }} KM</td>
                </tr>
            </table>
        </div>

        <!-- Signature lines -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line">potpis i pečat izdavaoca računa</div>
                </td>
                <td style="width: 10%;"></td>
                <td>
                    <div class="signature-line">potpis i pečat primaoca računa</div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="company-name">Računarsko programiranje "Wizionar" Aleksandra Davidović s.p. Miloševac</div>
            <div>JIB: 4512696590007 | Račun AtosBank: 5676512500038858</div>
            <div class="note">PDV nije obračunat, jer lice nije u PDV sistemu i Valuta plaćanja: konvertibilna marka (KM)</div>
        </div>
    </div>
</body>
</html>
