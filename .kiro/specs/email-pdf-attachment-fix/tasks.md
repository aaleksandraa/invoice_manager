# Implementation Plan: Email PDF Attachment Fix

## Overview

Ispravka bug-a u `InvoiceMail` klasi gdje se koriste pogrešni view template-i za PDF priloge u emailovima.

## Tasks

- [ ] 1. Ispravi view selection u InvoiceMail klasi
  - Promijeni `attachments()` metodu da koristi PDF-optimizovane view-ove
  - Koristi `invoice_bam_pdf` umjesto `invoice_bam`
  - Koristi `invoice_eur_pdf` umjesto `invoice_eur`
  - _Requirements: 1.1, 1.2, 1.3, 1.4_

- [ ]* 2. Dodaj unit test za InvoiceMail
  - Test da se koristi ispravan view za BAM fakturu
  - Test da se koristi ispravan view za EUR fakturu
  - _Requirements: 1.2, 1.3_

- [ ]* 3. Dodaj integration test za email slanje
  - Test slanja emaila sa BAM fakturom
  - Test slanja emaila sa EUR fakturom
  - Provjeri da PDF attachment koristi ispravan template
  - _Requirements: 1.1, 1.4_
