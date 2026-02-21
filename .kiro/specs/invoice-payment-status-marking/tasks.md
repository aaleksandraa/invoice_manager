# Plan implementacije

- [ ] 1. Napisati test za istraživanje uslova greške
  - **Property 1: Fault Condition** - Nedostajuća akcija za označavanje statusa plaćanja
  - **KRITIČNO**: Ovaj test MORA PASTI na neispravnom kodu - pad potvrđuje da greška postoji
  - **NE POKUŠAVATI ispraviti test ili kod kada padne**
  - **NAPOMENA**: Ovaj test kodira očekivano ponašanje - validiraće ispravku kada prođe nakon implementacije
  - **CILJ**: Demonstrirati da akcija za promjenu statusa plaćanja ne postoji u listi faktura
  - **Scoped PBT pristup**: Testirati konkretne slučajeve - desktop prikaz i mobilni prikaz liste faktura
  - Testirati da forma ili dugme koje poziva `invoices.update-payment-status` rutu ne postoji u HTML-u liste faktura
  - Testirati da korisnik ne može promijeniti status plaćanja bez otvaranja pojedinačne fakture
  - Pokrenuti test na NEISPRAVNOM kodu
  - **OČEKIVANI ISHOD**: Test PADA (ovo je ispravno - dokazuje da greška postoji)
  - Dokumentovati kontraprimer: akcija ne postoji u `resources/views/invoices/index.blade.php`
  - Označiti zadatak kao završen kada je test napisan, pokrenut, i pad dokumentovan
  - _Requirements: 1.1, 2.1_

- [ ] 2. Napisati property testove za očuvanje (PRIJE implementacije ispravke)
  - **Property 2: Preservation** - Postojeće akcije i funkcionalnost liste
  - **VAŽNO**: Slijediti observation-first metodologiju
  - Posmatrati ponašanje na NEISPRAVNOM kodu za postojeće akcije (vidi, uredi, obriši, PDF, email)
  - Posmatrati da sortiranje, filtriranje, pretraga i prikaz ukupnog iznosa rade ispravno
  - Napisati property-based testove koji hvataju posmatrane obrasce ponašanja iz Zahtjeva za očuvanje
  - Property-based testiranje generiše mnogo test slučajeva za jače garancije
  - Pokrenuti testove na NEISPRAVNOM kodu
  - **OČEKIVANI ISHOD**: Testovi PROLAZE (potvrđuje osnovno ponašanje koje treba očuvati)
  - Označiti zadatak kao završen kada su testovi napisani, pokrenuti, i prolaze na neispravnom kodu
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

- [ ] 3. Ispravka za nedostajuću akciju označavanja statusa plaćanja

  - [ ] 3.1 Implementirati ispravku
    - Dodati formu sa ikonom u kolonu "Akcije" za desktop prikaz (nakon linije 96 u `resources/views/invoices/index.blade.php`)
    - Dodati istu formu u mobilni prikaz kartica (nakon linije 141)
    - Kreirati JavaScript funkciju `togglePaymentStatus(invoiceId)` koja šalje AJAX PUT zahtjev ka `route('invoices.update-payment-status', $invoice)`
    - Dodati dinamičko ažuriranje vizuelnog indikatora (zeleni/crveni krug) bez osvježavanja stranice
    - Dodati error handling i success/error poruke korisniku
    - Osigurati CSRF token u AJAX zahтjevu
    - Dodati tooltip "Označi kao plaćeno/neplaćeno"
    - _Bug_Condition: isBugCondition(input) gdje input.location == 'invoices.index' AND input.intent == 'change_payment_status' AND NOT paymentStatusActionExists()_
    - _Expected_Behavior: Klik na ikonu ažurira status plaćanja, mijenja vizuelni indikator, ne osvježava stranicu, šalje email ako je omogućeno_
    - _Preservation: Sve postojeće akcije (vidi, uredi, obriši, PDF, email), sortiranje, filtriranje, pretraga i prikaz ukupnog iznosa moraju ostati nepromijenjeni_
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6_

  - [ ] 3.2 Verificirati da test za istraživanje uslova greške sada prolazi
    - **Property 1: Expected Behavior** - Akcija za označavanje statusa plaćanja postoji i radi
    - **VAŽNO**: Ponovo pokrenuti ISTI test iz zadatka 1 - NE pisati novi test
    - Test iz zadatka 1 kodira očekivano ponašanje
    - Kada ovaj test prođe, potvrđuje da je očekivano ponašanje zadovoljeno
    - Pokrenuti test za istraživanje uslova greške iz koraka 1
    - **OČEKIVANI ISHOD**: Test PROLAZI (potvrđuje da je greška ispravljena)
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [ ] 3.3 Verificirati da testovi za očuvanje i dalje prolaze
    - **Property 2: Preservation** - Postojeće akcije i funkcionalnost
    - **VAŽNO**: Ponovo pokrenuti ISTE testove iz zadatka 2 - NE pisati nove testove
    - Pokrenuti property testove za očuvanje iz koraka 2
    - **OČEKIVANI ISHOD**: Testovi PROLAZE (potvrđuje da nema regresija)
    - Potvrditi da svi testovi i dalje prolaze nakon ispravke (nema regresija)

- [ ] 4. Checkpoint - Osigurati da svi testovi prolaze
  - Osigurati da svi testovi prolaze, pitati korisnika ako se pojave pitanja.
