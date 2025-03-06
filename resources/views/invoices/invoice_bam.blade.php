<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Faktura {{ $invoice->broj_fakture }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #414042;
        }
        .container {
            width: 210mm; /* A4 širina */
            min-height: 297mm; /* A4 visina */
            margin: 0 auto;
            padding: 20mm; /* Margine unutar A4 papira */
            box-sizing: border-box;
        }
        .header {
            margin-bottom: 20px;
        }
        .header img {
            width: 175px;
            height: 60px;
            display: block;
            margin: 0 auto;
        }
        .header .step-forward {
            font-size: 11px;
            font-weight: 600; /* Poppins SemiBold */
            text-align: center;
            margin-bottom: 16px;
        }
        .header .invoice-info {
            font-size: 11px;
            font-weight: 600; /* Poppins SemiBold */
            margin-bottom: 16px; /* Razmak između redova */
            text-align: left;
        }
        .header .website {
            font-size: 11px;
            font-weight: 600; /* Poppins SemiBold */
            color: #414042;
            background-color: #e6e7e8;
            width: 376px;
            height: 12px;
            line-height: 12px;
            display: block;
            border-radius: 5px;
            margin: 10px auto;
            text-align: center;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-section .left {
            width: 45%;
        }
        .info-section .right {
            width: 55%;
        }
        .info-section p {
            font-size: 11px;
            margin-bottom: 16px; /* Razmak između redova */
        }
        .info-section .left p:first-child,
        .info-section .right p:first-child {
            font-weight: 600; /* Poppins SemiBold */
        }
        .info-section .left p:not(:first-child),
        .info-section .right p:not(:first-child) {
            font-weight: 400; /* Poppins Regular */
        }
        table {
            width: 502px;
            border-collapse: collapse;
            margin: 0 0 20px 0;
        }
        th, td {
            border: 0.5px solid #e6e7e8;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #e6e7e8;
            font-weight: 600; /* Poppins SemiBold */
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            height: 28px;
            line-height: 28px;
            padding: 0 10px;
        }
        td {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .total {
            width: 502px;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
        }
        .total .label {
            font-size: 11px;
            font-weight: 600; /* Poppins SemiBold */
            color: #414042;
            background-color: #e6e7e8;
            height: 28px;
            line-height: 28px;
            padding: 0 10px;
            border-radius: 5px;
            margin: 0;
        }
        .total .amount {
            font-size: 11px;
            font-weight: 400; /* Poppins Regular */
            color: #414042;
            background-color: #e6e7e8;
            height: 28px;
            line-height: 28px;
            padding: 0 10px;
            border-radius: 5px;
            margin: 0;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature p {
            font-size: 8px;
            font-style: italic;
            font-weight: 500; /* Poppins Medium Italic */
            color: #414042;
            border-top: 1px solid #e6e7e8;
            padding-top: 10px;
            width: 167.4px;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #414042;
            border-top: 1px solid #e6e7e8;
            width: 497px;
            padding-top: 10px;
            margin: 20px 0;
        }
        .footer p {
            margin-bottom: 16px; /* Razmak između redova */
        }
        .footer p:first-child {
            font-weight: 600; /* Poppins SemiBold */
        }
        .footer p:not(:first-child) {
            font-weight: 400; /* Poppins Regular */
        }
        .footer .note {
            font-size: 11px;
            font-style: italic;
            font-weight: 500; /* Poppins Medium Italic */
            margin-top: 10px;
            margin-bottom: 0; /* Zadnji red u footer-u ne treba dodatni razmak */
        }
        .download-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .download-btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://wizionar.com/wp-content/uploads/2023/09/wizionarLogoAsset-7@2x.png" alt="Wizionar Logo">
            <p class="step-forward">Step forward.</p>
            <p class="invoice-info">Račun br.: {{ $invoice->broj_fakture }}</p>
            <p class="invoice-info">Datum i mjesto izdavanja: {{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}., Miloševac</p>
            <p class="website">www.wizionar.com</p>
        </div>

        <div class="info-section">
            <div class="left">
                <p>Računarsko programiranje "Wizionar"</p>
                <p>Aleksandra Davidović s.p. Miloševac</p>
                <p>Adresa: Mali lug 117, 74485 Miloševac</p>
                <p>JIB / JMB: 4512696590007</p>
                <p>Račun AtosBank: 5676512500038858</p>
                <p>Email: info@wizionar.com</p>
                <p>Telefon: +387 66 / 882 - 702</p>
            </div>
            <div class="right">
                <p>Račun za:</p>
                <p>{{ $invoice->client->naziv_firme }}</p>
                <p>{{ $invoice->client->adresa }}</p>
                <p>{{ $invoice->client->postanski_broj_mjesto_drzava }}</p>
                <p>PDV: {{ $invoice->client->pdv_broj }}</p>
                <p>Email: {{ $invoice->client->email }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Detaljan opis posla</th>
                    <th>Količina</th>
                    <th>Cijena (KM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->opis_posla }}</td>
                    <td>{{ $invoice->kolicina }}</td>
                    <td>{{ number_format($invoice->cijena, 2) }} KM</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            <span class="label">Ukupna cijena:</span>
            <span class="amount">{{ number_format($invoice->cijena, 2) }} KM</span>
        </div>

        <div class="signature">
            <p>potpis i pečat izdavaoca računa</p>
            <p>potpis i pečat primaoca računa</p>
        </div>

        <div class="footer">
            <p>Računarsko programiranje "Wizionar" Aleksandra Davidović s.p. Miloševac</p>
            <p>JIB: 4512696590007 | Račun AtosBank: 5676512500038858</p>
            <p class="note">PDV nije obračunat, jer lice nije u PDV sistemu / Valuta plaćanja: konvertibilna marka (KM)</p>
        </div>

        <!-- Dugme za preuzimanje PDF-a -->
        <a href="{{ route('invoices.download', $invoice) }}" class="download-btn">Download</a>
    </div>
</body>
</html>