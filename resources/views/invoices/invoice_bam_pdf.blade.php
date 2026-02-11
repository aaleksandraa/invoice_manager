<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Faktura</title>
    <style>
        @page { margin: 15mm 20mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #414042; margin: 0; padding: 0; line-height: 1.2; position: relative; }
        table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; padding: 0; border: none; }
        .logo-text { font-size: 22pt; font-weight: bold; color: #414042; }
        .logo-img { max-height: 50pt; width: auto; display: block; }
        .invoice-info { font-size: 10pt; text-align: right; line-height: 1.3; }
        .invoice-info-label { font-weight: normal; }
        .invoice-info-value { font-weight: bold; }
        .website-bar { background-color: #e6e7e8; padding: 6pt; text-align: right; margin: 8pt 0; font-weight: bold; font-size: 10pt; }
        .info-table td { vertical-align: top; padding: 3pt 8pt 3pt 0; border: none; font-size: 10pt; line-height: 1.3; }
        .info-title { font-weight: bold; font-size: 10pt; margin-bottom: 2pt; }
        .invoice-table th { background-color: #e6e7e8; padding: 6pt; text-align: left; font-weight: bold; border: 1pt solid #d0d0d0; font-size: 10pt; }
        .invoice-table td { padding: 6pt; border: 1pt solid #d0d0d0; font-size: 10pt; line-height: 1.2; }
        .total-box { background-color: #414042; color: white; padding: 8pt; margin: 8pt 0 15pt 0; font-weight: bold; font-size: 11pt; }
        .total-box table { width: 100%; }
        .total-box td { color: white; border: none; padding: 0; }
        .page-wrapper { position: relative; min-height: 100vh; }
        .content-wrapper { padding-bottom: 180pt; }
        .signature-section { margin-top: 40pt; page-break-inside: avoid; }
        .signature-table { margin: 0; width: 100%; }
        .signature-table td { text-align: center; border: none; padding: 0 5pt; vertical-align: bottom; }
        .signature-line { padding-top: 4pt; font-size: 9pt; font-style: italic; color: #666; text-align: center; line-height: 1.2; }
        .footer { margin-top: 15pt; border-top: 1pt solid #e6e7e8; padding-top: 10pt; text-align: center; font-size: 9pt; line-height: 1.3; page-break-inside: avoid; }
        .footer-bold { font-weight: bold; }
        .footer-italic { font-style: italic; color: #666; margin-top: 5pt; }
    </style>
</head>
<body>
    <div class="page-wrapper">
    <div class="content-wrapper">
    <!-- Header -->
    <table class="header-table" style="margin-bottom: 15pt;">
        <tr>
            <td style="width: 50%;">
                <img src="{{ public_path('wizionar-logo.png') }}" class="logo-img" alt="Wizionar Logo">
            </td>
            <td style="width: 50%;">
                <div class="invoice-info">
                    <span class="invoice-info-label">Račun br.:</span> <span class="invoice-info-value">{{ str_replace('##', '#', $invoice->broj_fakture) }}</span><br>
                    <span class="invoice-info-value">{{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}, Miloševac</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Website bar -->
    <div class="website-bar">www.wizionar.com</div>

    <!-- Info section -->
    <table class="info-table" style="margin-bottom: 15pt;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-title">Računarsko programiranje "Wizionar"</div>
                <div>Aleksandra Davidović s.p. Miloševac</div>
                <div>Adresa: Mali lug 117, 74485 Miloševac</div>
                <div>JIB / JMB: 4512696590007</div>
                <div>Račun AtosBank: 5676512500038858</div>
                <div>Email: info@wizionar.com</div>
                <div>Telefon: +387 66 / 882 - 702</div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top; text-align: right;">
                <div class="info-title">Račun za:</div>
                <div>{{ $invoice->client->naziv_firme }}</div>
                <div>{{ $invoice->client->adresa }}</div>
                <div>{{ $invoice->client->postanski_broj_mjesto_drzava }}</div>
                <div>JIB: {{ $invoice->client->pdv_broj }}</div>
                <div>Email: {{ $invoice->client->email }}</div>
            </td>
        </tr>
    </table>

    <!-- Invoice table -->
    <table class="invoice-table" style="margin: 10pt 0;">
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
    <div class="total-box">
        <table>
            <tr>
                <td style="text-align: left;">Ukupna cijena:</td>
                <td style="text-align: right;">{{ number_format($invoice->cijena, 2) }} KM</td>
            </tr>
        </table>
    </div>
    </div>

    <!-- Signature lines -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 45%;">
                    <div style="border-top: 1pt solid #414042; margin-top: 30pt;"></div>
                    <div class="signature-line">potpis i pečat izdavaoca računa</div>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%;">
                    <div style="border-top: 1pt solid #414042; margin-top: 30pt;"></div>
                    <div class="signature-line">potpis i pečat primaoca računa</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-bold">Računarsko programiranje "Wizionar" Aleksandra Davidović s.p. Miloševac</div>
        <div style="margin-top: 3pt;">JIB: 4512696590007 | Račun AtosBank: 5676512500038858</div>
        <div class="footer-italic">PDV nije obračunat, jer lice nije u PDV sistemu i Valuta plaćanja: konvertibilna marka (KM)</div>
    </div>
    </div>
</body>
</html>
