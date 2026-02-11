# Implementation Plan: EUR Invoice Display Fix

## Overview

Ažurirati EUR fakture da budu identične BAM fakturama u strukturi i sadržaju, samo sa engleskim prevodima i EUR valutom.

## Tasks

- [x] 1. Ažurirati web verziju EUR fakture (invoice_eur.blade.php)
  - Kopiraj strukturu iz invoice_bam.blade.php
  - Zameni sve tekstove engleskim prevodima
  - Proveri da su svi banking podaci prisutni u issuer sekciji
  - Ažuriraj footer da koristi IBAN umesto "Račun AtosBank"
  - Proveri da je VAT napomena na engleskom
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 3.1, 3.2, 4.1, 4.2, 4.3, 4.4_

- [x] 2. Ažurirati PDF verziju EUR fakture (invoice_eur_pdf.blade.php)
  - Kopiraj strukturu iz invoice_bam_pdf.blade.php
  - Zameni sve tekstove engleskim prevodima
  - Proveri da su svi banking podaci prisutni u issuer sekciji
  - Ažuriraj footer da koristi IBAN umesto "Račun AtosBank"
  - Proveri da je VAT napomena na engleskom
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 3.3, 3.4, 4.1, 4.2, 4.3, 4.4_

- [ ]* 3. Kreirati testove za EUR fakture
  - [ ]* 3.1 Kreirati test fajl EurInvoiceDisplayTest.php
    - Setup test sa test invoice podacima
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

  - [ ]* 3.2 Test za banking informacije
    - Proveri da web verzija sadrži sve banking podatke
    - Proveri da PDF verzija sadrži sve banking podatke
    - _Requirements: 1.1, 1.2, 1.3, 1.4_

  - [ ]* 3.3 Test za VAT napomenu
    - Proveri da footer sadrži VAT napomenu na engleskom
    - Proveri italic styling
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ]* 3.4 Test za web-PDF konzistentnost
    - Uporedi sadržaj između web i PDF verzija
    - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [ ] 4. Checkpoint - Proveri da sve radi
  - Testiraj rendering EUR faktura u browseru
  - Testiraj PDF generisanje
  - Uporedi sa BAM fakturama vizuelno
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster implementation
- Fokus je na kopiranju strukture iz BAM faktura
- Svi prevodi su već definisani u design dokumentu
- Payment instruction note ("Please ensure that the payment instruction is set to 'OUR'") ostaje u EUR fakturama
