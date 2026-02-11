# Requirements Document

## Introduction

Kada se faktura šalje emailom, PDF prilog ne koristi PDF-optimizovane view template-e. Umjesto toga, koristi obične view template-e koji su namijenjeni za prikaz u browseru. Ovo znači da EUR fakture koje se šalju emailom ne koriste novi `invoice_eur_pdf.blade.php` template koji smo kreirali.

## Glossary

- **InvoiceMail**: Mailable klasa koja generiše email sa fakturom u prilogu
- **PDF_View**: Blade template optimizovan za PDF generisanje (`invoice_bam_pdf.blade.php`, `invoice_eur_pdf.blade.php`)
- **Browser_View**: Blade template za prikaz u browseru (`invoice_bam.blade.php`, `invoice_eur.blade.php`)
- **Email_Attachment**: PDF fajl koji se prilaže emailu

## Requirements

### Requirement 1: Korištenje PDF-optimizovanih view-ova za email priloge

**User Story:** Kao korisnik, želim da fakture koje šaljem emailom koriste iste PDF template-e kao i download funkcionalnost, tako da klijenti dobiju ispravno formatirane fakture.

#### Acceptance Criteria

1. WHEN InvoiceMail generiše PDF prilog, THE System SHALL koristiti PDF-optimizovane view template-e
2. WHEN faktura je u BAM valuti, THE System SHALL koristiti `invoices.invoice_bam_pdf` view
3. WHEN faktura je u EUR valuti, THE System SHALL koristiti `invoices.invoice_eur_pdf` view
4. WHEN se faktura preuzima (download) i kada se šalje emailom, THE System SHALL koristiti iste view template-e
