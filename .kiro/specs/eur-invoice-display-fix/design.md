# Design Document: EUR Invoice Display Fix

## Overview

EUR fakture treba da budu identične BAM fakturama u strukturi, layoutu i sadržaju, samo sa engleskim prevodima i EUR valutom umesto KM. Sve što postoji u BAM fakturama mora postojati i u EUR fakturama.

## Architecture

Ažuriranje dva Blade template fajla:
- `resources/views/invoices/invoice_eur.blade.php` - Web verzija
- `resources/views/invoices/invoice_eur_pdf.blade.php` - PDF verzija

Pristup: Kopiraj strukturu i sadržaj iz BAM template-a, zameni tekstove engleskim prevodima.

## Components and Interfaces

### Template Files

**invoice_eur.blade.php**
- Kopira strukturu iz `invoice_bam.blade.php`
- Isti CSS stilovi, isti layout
- Engleski prevodi za sve labele

**invoice_eur_pdf.blade.php**
- Kopira strukturu iz `invoice_bam_pdf.blade.php`
- Isti CSS stilovi, isti layout
- Engleski prevodi za sve labele

## Data Models

### Mapiranje tekstova (BAM → EUR)

**Header sekcija:**
- "Račun br.:" → "Invoice Number:"
- "Datum i mjesto izdavanja:" → "Invoice Date:"

**Issuer sekcija (levo):**
- Sve ostaje isto kao u BAM (isti podaci, isti redosled)
- "Telefon:" → "Tel:"

**Recipient sekcija (desno):**
- "Račun za:" → "Invoice to:"
- "JIB:" → "VAT:"

**Tabela:**
- "Detaljan opis posla" → "Description"
- "Količina" → "Quantity"
- "Cijena (KM)" → "Amount (EUR)"

**Total sekcija:**
- "Ukupna cijena:" → "Total in EUR:"
- "KM" → "EUR"

**Signature sekcija:**
- "potpis i pečat izdavaoca računa" → "Authorized by"
- "potpis i pečat primaoca računa" → "Customer"

**Footer sekcija:**
- "Račun AtosBank:" → "IBAN:" (u footer-u)
- "PDV nije obračunat, jer lice nije u PDV sistemu i Valuta plaćanja: konvertibilna marka (KM)" → "Wizionar is not a part of the VAT system"

### Ključne razlike koje treba ispraviti

**U issuer sekciji (trenutno nedostaje u EUR):**
- Nema "Račun AtosBank: 5676512500038858" - treba dodati
- "Tel:" umesto "Telefon:" - već ispravljeno

**U footer sekciji:**
- BAM ima: "JIB: 4512696590007 | Račun AtosBank: 5676512500038858"
- EUR treba: "JIB: 4512696590007 | IBAN: BA395676510000114506"

## Correctness Properties

A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.

### Property 1: Structural Consistency
*For any* EUR invoice (web or PDF), the HTML structure and CSS classes must match the corresponding BAM invoice template exactly.

**Validates: Requirements 4.1, 4.2, 4.3, 4.4**

### Property 2: Banking Information Completeness
*For any* EUR invoice rendered, all banking fields present in BAM invoices must be present in EUR invoices with correct English labels.

**Validates: Requirements 1.1, 1.2, 1.3, 1.4**

### Property 3: VAT Status Display
*For any* EUR invoice rendered, the footer must contain the VAT status text in English with italic styling, matching the position and style of the BAM invoice.

**Validates: Requirements 2.1, 2.2, 2.3**

### Property 4: Web-PDF Consistency
*For any* invoice data, the content and structure of the web version must match the PDF version exactly (except for PDF-specific styling).

**Validates: Requirements 3.1, 3.2, 3.3, 3.4**

## Error Handling

Isti error handling kao u BAM fakturama:
- Ako `datum_izdavanja` je null, prikaži "-"
- Sva ostala polja su obavezna
- Client relacija je obavezna (database constraint)

## Testing Strategy

### Unit Tests
- Uporedi rendered HTML između BAM i EUR faktura
- Proveri da svi tekstovi iz BAM postoje u EUR sa ispravnim prevodima
- Proveri formatiranje valute (KM → EUR)
- Proveri da footer sadrži ispravnu VAT napomenu

### Property-Based Tests
- *For any* invoice data, EUR template mora imati istu strukturu kao BAM template
- *For any* invoice data, svi banking podaci moraju biti prisutni
- *For any* invoice data, web i PDF verzije moraju biti konzistentne

Minimum 100 iteracija po property testu.

### Testing Framework
- PHPUnit za Laravel
- Laravel view testing
- Test fajl: `tests/Feature/EurInvoiceDisplayTest.php`
- Tag format: **Feature: eur-invoice-display-fix, Property N: [property text]**
