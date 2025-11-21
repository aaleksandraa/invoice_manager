# Vizuelne promjene - Lista faktura

## Prije izmjena

U listi faktura je postojalo samo jedno dugme za slanje emaila:

```
[👁️ Vidi] [✏️ Uredi] [🗑️ Obriši] [📄 PDF] [✉️ Pošalji email]
```

**Problem:** Dugme "Pošalji email" (ikonica ✉️) je slalo opomenu, ali nije bilo jasno da šalje opomenu, niti je postojala opcija za slanje same fakture.

## Nakon izmjena

Sada lista faktura ima **dva odvojena dugmeta**:

```
[👁️ Vidi] [✏️ Uredi] [🗑️ Obriši] [📄 PDF] [✈️ Pošalji fakturu] [🔔 Pošalji opomenu]
```

### Detalji novih dugmadi:

#### 1. Dugme "Pošalji fakturu" (novo)
- **Ikonica:** ✈️ (`fa-paper-plane` - papirni avion)
- **Tooltip:** "Pošalji fakturu"
- **Akcija:** Šalje email sa fakturom klijentu
- **Confirm poruka:** "Jeste li sigurni da želite poslati fakturu emailom klijentu?"
- **Email tip:** Koristi `InvoiceMail` klasu
- **Subject emaila:** "Faktura {broj_fakture}"
- **Sadržaj:** Profesionalna poruka sa fakturom u prilogu

#### 2. Dugme "Pošalji opomenu" (izmijenjeno postojeće)
- **Ikonica:** 🔔 (`fa-bell` - zvonce, **promjena sa** ✉️ `fa-envelope`)
- **Tooltip:** "Pošalji opomenu" (**promjena sa** "Pošalji email")
- **Akcija:** Šalje opomenu/podsjetnik za neplaćenu fakturu
- **Confirm poruka:** "Jeste li sigurni da želite poslati opomenu klijentu?"
- **Email tip:** Koristi `PaymentReminderMail` klasu
- **Subject emaila:** "Opomena - Neplaćena faktura {broj_fakture}"
- **Sadržaj:** Podsjetnik o neplaćenoj fakturi sa fakturom u prilogu

## Razlike u email porukama

### Email za fakturu (`InvoiceMail`)
```
Subject: Faktura #123/2024

Poštovani,

U prilogu ove poruke možete pronaći fakturu broj #123/2024.

Detalji fakture:
- Broj fakture: #123/2024
- Datum izdavanja: 21.11.2024
- Klijent: [Naziv firme]
- Opis: [Opis posla]
- Iznos: [Cijena]

Faktura u PDF formatu nalazi se u prilogu ove poruke.

Hvala vam na saradnji.
```

### Email za opomenu (`PaymentReminderMail`)
```
Subject: Opomena - Neplaćena faktura #123/2024

Poštovani,

Obavještavamo Vas da je faktura br. #123/2024 još uvijek neplaćena.

Detalji fakture:
- Broj fakture: #123/2024
- Datum izdavanja: 21.11.2024
- Klijent: [Naziv firme]
- Opis: [Opis posla]
- Iznos: [Cijena]

Molimo Vas da u najkraćem roku izmirite Vaše obaveze prema priloženom računu.

U prilogu se nalazi kopija fakture za Vaše evidencije.

Hvala na razumijevanju i saradnji.
```

## Prednosti novih izmjena

1. **Jasnoća:** Korisnici sada jasno vide razliku između slanja fakture i slanja opomene
2. **Fleksibilnost:** Mogu odabrati kada da pošalju fakturu, a kada opomenu
3. **Intuitivnost:** Ikone jasno ukazuju na funkciju (avion za slanje, zvonce za opomenu)
4. **Konzistentnost:** Oba dugmeta rade na isti način, samo šalju različite tipove emailova
5. **Tracking:** Sistem loguje oba tipa emailova odvojeno u bazu (`email_logs` tabela)

## Implementacija na mobilnim uređajima

Iste promjene su primijenjene i na mobilnom prikazu (kartice), osiguravajući konzistentno korisničko iskustvo na svim uređajima.

## Tehnički detalji

### Rute:
- `/invoices/{invoice}/send-invoice` (POST) - Šalje fakturu
- `/invoices/{invoice}/send-email` (POST) - Šalje opomenu

### Controller metode:
- `InvoiceController::sendInvoice()` - Nova metoda za slanje fakture
- `InvoiceController::sendEmail()` - Postojeća metoda za slanje opomene

### Mail klase:
- `InvoiceMail` - Za slanje fakture
- `PaymentReminderMail` - Za slanje opomene
