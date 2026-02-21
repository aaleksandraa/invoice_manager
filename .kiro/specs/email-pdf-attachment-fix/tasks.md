# Implementation Plan: Email PDF Attachment Fix

## Overview

Ispravka bug-a u `InvoiceMail` klasi gdje se koriste pogrešni view template-i za PDF priloge u emailovima.

## Tasks

- [x] 1. Ispravi view selection u PaymentReminderMail klasi
  - Promijeni `attachments()` metodu da koristi PDF-optimizovane view-ove
  - Koristi `invoice_bam_pdf` umjesto `invoice_bam`
  - Koristi `invoice_eur_pdf` umjesto `invoice_eur`
  - _Requirements: 1.1, 1.2, 1.3, 1.5_

- [x] 2. Ispravi view selection u FirstReminderMail klasi
  - Promijeni `attachments()` metodu da koristi PDF-optimizovane view-ove
  - Koristi `invoice_bam_pdf` umjesto `invoice_bam`
  - Koristi `invoice_eur_pdf` umjesto `invoice_eur`
  - _Requirements: 1.1, 1.2, 1.3, 1.6_

- [x] 3. Ispravi view selection u SecondReminderMail klasi
  - Promijeni `attachments()` metodu da koristi PDF-optimizovane view-ove
  - Koristi `invoice_bam_pdf` umjesto `invoice_bam`
  - Koristi `invoice_eur_pdf` umjesto `invoice_eur`
  - _Requirements: 1.1, 1.2, 1.3, 1.7_

- [x]* 4. Dodaj unit testove za sve Mailable klase
  - Test da PaymentReminderMail koristi ispravan view za BAM i EUR
  - Test da FirstReminderMail koristi ispravan view za BAM i EUR
  - Test da SecondReminderMail koristi ispravan view za BAM i EUR
  - _Requirements: 1.2, 1.3, 1.5, 1.6, 1.7_
