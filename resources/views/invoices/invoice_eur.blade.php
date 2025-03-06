<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->broj_fakture }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .header h2 {
            font-size: 24px;
            margin: 10px 0;
        }
        .header p {
            font-size: 14px;
            color: #555;
        }
        .details {
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }
        .footer p {
            margin: 5px 0;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature p {
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            width: 200px;
            text-align: center;
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
            <h2>Wizionar - Step forward</h2>
            <p>www.wizionar.com</p>
        </div>

        <div class="details">
            <p><strong>Invoice No.:</strong> {{ $invoice->broj_fakture }}</p>
            <p><strong>Date and Place of Issue:</strong> {{ $invoice->datum_izdavanja ? $invoice->datum_izdavanja->format('d.m.Y') : '-' }}, Miloševac</p>
        </div>

        <div class="details">
            <p><strong>Computer Programming "Wizionar"</strong></p>
            <p>Aleksandra Davidović s.p. Miloševac</p>
            <p>Address: Mali lug 117, 74485 Miloševac, Bosnia and Herzegovina</p>
            <p>Tax ID: 4512696590007</p>
            <p>Bank Account: AtosBank 5676512500038858</p>
            <p>Email: info@wizionar.com</p>
            <p>Phone: +387 66 / 882 - 702</p>
        </div>

        <div class="details">
            <p><strong>Invoice for:</strong></p>
            <p>{{ $invoice->client->naziv_firme }}</p>
            <p>{{ $invoice->client->adresa }}</p>
            <p>{{ $invoice->client->postanski_broj_mjesto_drzava }}</p>
            <p>VAT ID: {{ $invoice->client->pdv_broj }}</p>
            <p>Email: {{ $invoice->client->email }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description of Services</th>
                    <th>Quantity</th>
                    <th>Price (EUR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->opis_posla }}</td>
                    <td>{{ $invoice->kolicina }}</td>
                    <td>{{ number_format($invoice->cijena, 2) }} EUR</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Total Price: {{ number_format($invoice->cijena, 2) }} EUR ({{ number_format($invoice->bam_amount, 2) }} KM)</p>
        <p>Exchange rate: 1 EUR = 1.95583 KM</p>

        <div class="signature">
            <p>Signature and Stamp of the Issuer</p>
            <p>Signature and Stamp of the Recipient</p>
        </div>

        <div class="footer">
            <p>Computer Programming "Wizionar" Aleksandra Davidović s.p. Miloševac</p>
            <p>Tax ID: 4512696590007 | Bank Account: AtosBank 5676512500038858</p>
            <p>VAT not applicable as the entity is not in the VAT system</p>
            <p>Payment currency: EUR</p>
        </div>

        <!-- Dugme za preuzimanje PDF-a -->
        <a href="{{ route('invoices.download', $invoice) }}" class="download-btn">Download</a>
    </div>
</body>
</html>