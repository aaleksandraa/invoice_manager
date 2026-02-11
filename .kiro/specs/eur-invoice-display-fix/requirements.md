# Requirements Document

## Introduction

Ova specifikacija definiše ispravke za EUR fakture koje trenutno prikazuju neispravne informacije. EUR fakture treba da budu konzistentne sa BAM fakturama u pogledu prikazanih informacija, ali sa odgovarajućim engleskim tekstom i EUR valutom.

## Glossary

- **EUR_Invoice**: Faktura koja koristi EUR valutu i engleski jezik
- **BAM_Invoice**: Faktura koja koristi BAM valutu i bosanski jezik
- **Invoice_Template**: Blade template fajl koji generiše HTML prikaz fakture
- **PDF_Template**: Blade template fajl koji generiše PDF verziju fakture
- **System**: Invoice management sistem

## Requirements

### Requirement 1: Prikaz bankovnih informacija

**User Story:** Kao korisnik, želim da EUR fakture prikazuju sve bankovne informacije, kako bi klijenti znali gdje da izvrše plaćanje.

#### Acceptance Criteria

1. WHEN EUR faktura se prikazuje, THE System SHALL prikazati IBAN broj (BA395676510000114506)
2. WHEN EUR faktura se prikazuje, THE System SHALL prikazati SWIFT kod (SABRBA2B)
3. WHEN EUR faktura se prikazuje, THE System SHALL prikazati broj računa AtosBank (5676512500038858)
4. THE System SHALL prikazati sve bankovne informacije u sekciji izdavaoca fakture

### Requirement 2: Prikaz PDV statusa

**User Story:** Kao korisnik, želim da EUR fakture jasno prikazuju PDV status kompanije, kako bi klijenti razumeli zašto PDV nije obračunat.

#### Acceptance Criteria

1. WHEN EUR faktura se prikazuje, THE System SHALL prikazati napomenu "Wizionar is not a part of the VAT system" u footer sekciji
2. THE System SHALL prikazati PDV napomenu na engleskom jeziku
3. THE System SHALL prikazati PDV napomenu italic stilom

### Requirement 3: Konzistentnost između web i PDF verzija

**User Story:** Kao korisnik, želim da web i PDF verzije EUR faktura prikazuju iste informacije, kako bi bile konzistentne.

#### Acceptance Criteria

1. WHEN EUR faktura se prikazuje u web verziji, THE System SHALL prikazati sve bankovne informacije
2. WHEN EUR faktura se prikazuje u PDF verziji, THE System SHALL prikazati sve bankovne informacije
3. THE System SHALL održavati konzistentnost informacija između web i PDF verzija
4. THE System SHALL koristiti isti format i raspored informacija u obe verzije

### Requirement 4: Formatiranje i layout

**User Story:** Kao korisnik, želim da EUR fakture imaju profesionalan izgled sličan BAM fakturama, kako bi bile prezentabilne klijentima.

#### Acceptance Criteria

1. THE System SHALL koristiti isti font i veličine kao BAM fakture
2. THE System SHALL koristiti isti layout i razmake kao BAM fakture
3. THE System SHALL koristiti iste boje i stilove kao BAM fakture
4. WHEN EUR faktura se prikazuje, THE System SHALL održavati čitljivost i profesionalan izgled
