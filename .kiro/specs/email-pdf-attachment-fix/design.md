# Design Document

## Overview

Ispravka bug-a gdje `InvoiceMail` klasa koristi pogrešne view template-e za generisanje PDF priloga. Trenutno koristi browser view-ove umjesto PDF-optimizovanih view-ova.

## Architecture

Promjena je minimalna i lokalizovana u `InvoiceMail` klasi. Potrebno je samo promijeniti logiku odabira view template-a u `attachments()` metodi.

## Components and Interfaces

### InvoiceMail::attachments()

**Trenutna implementacija:**
```php
$view = $this->invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';
```

**Nova implementacija:**
```php
$view = $this->invoice->valuta === 'BAM' ? 'invoices.invoice_bam_pdf' : 'invoices.invoice_eur_pdf';
```

Ova promjena osigurava konzistentnost sa `InvoiceController::download()` metodom koja već koristi PDF-optimizovane view-ove.

## Data Models

Nema promjena u data modelima.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: View Consistency Between Download and Email

*For any* invoice, the view template used for PDF generation in email attachments should be the same as the view template used for PDF download.

**Validates: Requirements 1.4**

### Property 2: Currency-Based View Selection

*For any* invoice with BAM currency, the system should use `invoice_bam_pdf` view for email attachments, and *for any* invoice with EUR currency, the system should use `invoice_eur_pdf` view.

**Validates: Requirements 1.2, 1.3**

## Error Handling

Nema novih error handling zahtjeva. Postojeći error handling u `InvoiceMail` i `MailService` ostaje nepromijenjen.

## Testing Strategy

### Unit Tests

- Test da `InvoiceMail` koristi `invoice_bam_pdf` za BAM fakture
- Test da `InvoiceMail` koristi `invoice_eur_pdf` za EUR fakture
- Test da PDF attachment ima ispravan sadržaj

### Integration Tests

- Test slanja emaila sa BAM fakturom i provjera da PDF koristi ispravan template
- Test slanja emaila sa EUR fakturom i provjera da PDF koristi ispravan template
- Uporediti PDF iz emaila sa PDF-om iz download funkcionalnosti
