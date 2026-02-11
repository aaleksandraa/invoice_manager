<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Faktura</title>
    <style>
        @page { margin: 15mm 20mm; }
        body { font-family: DejaVu Sans; font-size: 9pt; color: #414042; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; padding: 0; border: none; }
        .logo-text { font-size: 20pt; font-weight: bold; }
        .invoice-number { font-size: 10pt; font-weight: bold; text-align: right; }
        .website-bar { background-color: #e6e7e8; padding: 5pt; text-align: right; margin: 10pt 0; font-weight: bold; }
        .info-table td { vertical-align: top; padding: 5pt 10pt 5pt 0; border: none; font-size: 8pt; line-height: 1.4; }
        .info-title { font-weight: bold; font-size: 9pt; margin-bottom: 3pt; }
        .invoice-table th { background-color: #e6e7e8; padding: 6pt; text-align: left; font-weight: bold; border: 1pt solid #d0d0d0; font-size: 9pt; }
        .invoice-table td { padding: 6pt; border: 1pt solid #d0d0d0; font-size: 9pt; }
        .total-box { background-color: #414042; color: white; padding: 8pt; margin: 10pt 0 40pt 0; font-weight: bold; }
        .total-box table { width: 100%; }
        .total-box td { color: white; border: none; padding: 0; }
        .signature-table { margin: 40pt 0; }
        .signature-table td { width: 45%; text-align: center; border: none; padding: 0 5pt; }
        .signature-line { border-top: 1pt solid #414042; padding-top: 4pt; font-size: 7pt; font-style: italic; color: #666; }
        .footer { border-top: 1pt solid #e6e7e8; padding-top: 8pt; text-align: center; font-size: 8pt; line-height: 1.4; margin-top: 20pt; }
        .footer-bold { font-weight: bold; }
        .footer-italic { font-style: italic; color: #666; margin-top: 4pt; }
    </style>
</head>
<body>
    <table class="header-table" style="margin-bottom: 15pt;">
        <tr>
            <td style="width: 50%;">
                <div class="logo-text">wizionar</div>
                <div style="font-size: 8pt; color: #666;">Step forward.</div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="invoice-number">Račun br.: {{ $invoice->broj_fakture }}</div>
                <div style="font-size: 9pt; margin-top: 3pt;">
                    Datum i mjesto izdavanja:<br><strong>{{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}., Miloševac</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="website-bar">www.wizionar.com</div>

    <table class="info-table" style="margin-bottom: 15pt;">
        <tr>
            <td style="width: 48%;">
                <div class="info-title">Računarsko programiranje "Wizionar"</div>
                <div>Aleksandra Davidović s.p. Miloševac</div>
                <div>Adresa: Mali lug 117, 74485 Miloševac</div>
                <div>JIB / JMB: 4512696590007</div>
                <div>Račun AtosBank: 5676512500038858</div>
                <div>Email: info@wizionar.com</div>
                <div>Telefon: +387 66 / 882 - 702</div>
            </td>
            <td style="width: 48%;">
                <div class="info-title">Račun za:</div>
                <div>{{ $invoice->client->naziv_firme }}</div>
                <div>{{ $invoice->client->adresa }}</div>
                <div>{{ $invoice->client->postanski_broj_mjesto_drzava }}</div>
                <div>JIB: {{ $invoice->client->pdv_broj }}</div>
                <div>Email: {{ $invoice->client->email }}</div>
            </td>
        </tr>
    </table>

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

    <div class="total-box">
        <table>
            <tr>
                <td style="text-align: left;">Ukupna cijena:</td>
                <td style="text-align: right;">{{ number_format($invoice->cijena, 2) }} KM</td>
            </tr>
        </table>
    </div>

    <table class="signature-table">
        <tr>
            <td><div class="signature-line">potpis i pečat izdavaoca računa</div></td>
            <td style="width: 10%;"></td>
            <td><div class="signature-line">potpis i pečat primaoca računa</div></td>
        </tr>
    </table>

    <div class="footer">
        <div class="footer-bold">Računarsko programiranje "Wizionar" Aleksandra Davidović s.p. Miloševac</div>
        <div>JIB: 4512696590007 | Račun AtosBank: 5676512500038858</div>
        <div class="footer-italic">PDV nije obračunat, jer lice nije u PDV sistemu i Valuta plaćanja: konvertibilna marka (KM)</div>
    </div>
</body>
</html>
