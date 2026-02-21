# Dizajn ispravke greške - Označavanje statusa plaćanja fakture

## Pregled

Ova ispravka vraća nedostajuću akciju u korisničkom interfejsu koja omogućava korisnicima da brzo označe fakturu kao plaćenu ili neplaćenu direktno iz liste faktura. Backend funkcionalnost (`updatePaymentStatus` metoda u `InvoiceController`) već postoji i radi ispravno. Potrebno je samo dodati dugme/ikonu u listu faktura koje poziva postojeću backend metodu.

Ispravka je minimalna - dodaje se nova akcija (ikona) u kolonu "Akcije" u tabeli faktura i u kartice za mobilne uređaje, koja šalje AJAX zahtjev ka postojećem endpointu.

## Rječnik

- **Bug_Condition (C)**: Uslov koji aktivira grešku - korisnik gleda listu faktura i želi promijeniti status plaćanja
- **Property (P)**: Željeno ponašanje - korisnik može kliknuti na ikonu i promijeniti status plaćanja bez napuštanja liste
- **Preservation**: Postojeće akcije (vidi, uredi, obriši, PDF, email) i funkcionalnost liste moraju ostati nepromijenjene
- **updatePaymentStatus**: Metoda u `InvoiceController` (linija 202) koja ažurira status plaćanja fakture
- **placeno**: Boolean polje u bazi podataka koje označava da li je faktura plaćena
- **datum_placanja**: Datum kada je faktura plaćena (postavlja se automatski na trenutni datum ako nije naveden)

## Detalji greške

### Uslov greške

Greška se manifestuje kada korisnik gleda listu faktura i želi promijeniti status plaćanja fakture. U listi faktura nedostaje akcija (dugme/ikona) koja omogućava brzu promjenu statusa plaćanja.

**Formalna specifikacija:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type UserAction
  OUTPUT: boolean
  
  RETURN input.location == 'invoices.index'
         AND input.intent == 'change_payment_status'
         AND NOT paymentStatusActionExists()
END FUNCTION
```

### Primjeri

- Korisnik gleda listu faktura i vidi neplaćenu fakturu (crveni indikator) - želi je označiti kao plaćenu, ali ne vidi akciju za to
- Korisnik ima 10 faktura koje treba označiti kao plaćene - mora otvoriti svaku pojedinačno umjesto da brzo klikne na ikonu u listi
- Korisnik je greškom označio fakturu kao plaćenu - želi je vratiti na neplaćeno, ali mora otvoriti pojedinačnu fakturu
- Korisnik na mobilnom uređaju gleda kartice faktura - ne vidi akciju za promjenu statusa plaćanja

## Očekivano ponašanje

### Zahtjevi za očuvanje

**Nepromijenjeno ponašanje:**
- Postojeće akcije u koloni "Akcije" moraju nastaviti raditi identično (vidi, uredi, obriši, pregled PDF, preuzmi PDF, pošalji fakturu, pošalji opomenu)
- Ažuriranje statusa plaćanja kroz postojeću formu na stranici pojedinačne fakture mora nastaviti raditi na isti način
- Sortiranje i filtriranje faktura po statusu plaćanja mora nastaviti raditi
- Prikaz ukupno uplaćenog iznosa na dnu liste mora nastaviti prikazivati tačan zbir
- Autorizacija (provjera da li faktura pripada korisniku) mora nastaviti raditi kroz postojeću `updatePaymentStatus` metodu

**Opseg:**
Sve interakcije koje NE uključuju klik na novu ikonu za promjenu statusa plaćanja treba da budu potpuno nepromijenjene. Ovo uključuje:
- Klikove na postojeće akcije
- Sortiranje i filtriranje
- Pretragu faktura
- Navigaciju između stranica

## Hipoteza o uzroku greške

Na osnovu analize koda, uzrok greške je jasan:

1. **Nedostajuća UI akcija**: U `resources/views/invoices/index.blade.php` u koloni "Akcije" (linija 79-96 za desktop, linija 127-141 za mobilne) ne postoji forma ili dugme koje poziva `invoices.update-payment-status` rutu

2. **Postojeća backend funkcionalnost**: Ruta `PUT /invoices/{invoice}/payment-status` postoji u `routes/web.php` (linija 15) i poziva `updatePaymentStatus` metodu koja radi ispravno

3. **Regresija**: Ova akcija je vjerovatno postojala ranije ali je uklonjena ili zaboravljena tokom razvoja

## Svojstva ispravnosti

Property 1: Uslov greške - Brzo označavanje statusa plaćanja iz liste

_Za bilo koji_ korisnički klik na ikonu za promjenu statusa plaćanja u listi faktura, ispravljena funkcionalnost TREBA ažurirati status plaćanja fakture (toggle između plaćeno/neplaćeno) bez napuštanja liste faktura, prikazati vizuelnu potvrdu promjene (promjena boje indikatora), i poslati email potvrdu ako je korisnik omogućio tu opciju.

**Validira: Zahtjevi 2.1, 2.2, 2.3, 2.4, 2.5, 2.6**

Property 2: Očuvanje - Postojeće akcije i funkcionalnost

_Za bilo koju_ interakciju koja NIJE klik na novu ikonu za promjenu statusa plaćanja, ispravljen kod TREBA proizvesti identičan rezultat kao originalni kod, čuvajući sve postojeće akcije (vidi, uredi, obriši, PDF, email), sortiranje, filtriranje, pretragu i prikaz ukupnog iznosa.

**Validira: Zahtjevi 3.1, 3.2, 3.3, 3.4, 3.5, 3.6**

## Implementacija ispravke

### Potrebne izmjene

**Fajl**: `resources/views/invoices/index.blade.php`

**Specifične izmjene**:

1. **Dodavanje ikone za desktop prikaz** (nakon linije 96, prije zatvaranja `<td class="p-3 space-x-2">`):
   - Dodati formu sa metodom PUT koja poziva `route('invoices.update-payment-status', $invoice)`
   - Koristiti ikonu koja vizuelno predstavlja promjenu statusa (npr. `fa-check-circle` ili `fa-money-bill-wave`)
   - Dodati JavaScript za AJAX zahtjev kako bi se izbjeglo osvježavanje stranice
   - Dodati dinamičku promjenu boje indikatora nakon uspješnog ažuriranja

2. **Dodavanje ikone za mobilni prikaz** (nakon linije 141, prije zatvaranja `<div class="flex flex-col...">`):
   - Dodati istu formu kao za desktop
   - Osigurati da ikona radi na touch uređajima

3. **JavaScript funkcionalnost**:
   - Kreirati funkciju `togglePaymentStatus(invoiceId)` koja šalje AJAX PUT zahtjev
   - Funkcija treba da ažurira vizuelni indikator (zeleni/crveni krug) bez osvježavanja stranice
   - Dodati error handling za slučaj neuspješnog zahtjeva
   - Prikazati success/error poruku korisniku

4. **CSRF token**: Osigurati da AJAX zahtjev uključuje CSRF token za Laravel autentifikaciju

5. **Tooltip**: Dodati `title` atribut na ikonu sa tekstom "Označi kao plaćeno/neplaćeno"

## Strategija testiranja

### Pristup validaciji

Strategija testiranja prati dvofazni pristup: prvo, demonstrirati grešku na neispravnom kodu (nedostajuća akcija), zatim verificirati da ispravka radi ispravno i čuva postojeće ponašanje.

### Istraživačka provjera uslova greške

**Cilj**: Demonstrirati grešku PRIJE implementacije ispravke. Potvrditi da akcija za promjenu statusa plaćanja ne postoji u listi faktura.

**Plan testiranja**: Napisati testove koji simuliraju korisničku interakciju sa listom faktura i pokušavaju pronaći akciju za promjenu statusa plaćanja. Pokrenuti ove testove na NEISPRAVNOM kodu da bi se potvrdilo da akcija ne postoji.

**Test slučajevi**:
1. **Test nedostajuće akcije na desktopu**: Provjeriti da li postoji forma ili dugme koje poziva `invoices.update-payment-status` rutu u desktop prikazu (neće pronaći na neispravnom kodu)
2. **Test nedostajuće akcije na mobilnom**: Provjeriti da li postoji forma ili dugme u mobilnom prikazu kartica (neće pronaći na neispravnom kodu)
3. **Test postojeće backend funkcionalnosti**: Direktno pozvati `updatePaymentStatus` metodu da bi se potvrdilo da backend radi (proći će na neispravnom kodu)
4. **Test postojećih akcija**: Provjeriti da sve postojeće akcije postoje i rade (proći će na neispravnom kodu)

**Očekivani kontraprimer**:
- Akcija za promjenu statusa plaćanja ne postoji u HTML-u liste faktura
- Korisnik ne može promijeniti status plaćanja bez otvaranja pojedinačne fakture

### Provjera ispravke

**Cilj**: Verificirati da za sve ulaze gdje uslov greške vrijedi, ispravljena funkcionalnost proizvodi očekivano ponašanje.

**Pseudokod:**
```
FOR ALL input WHERE isBugCondition(input) DO
  result := clickPaymentStatusIcon(input.invoice)
  ASSERT result.statusChanged == true
  ASSERT result.visualIndicatorUpdated == true
  ASSERT result.pageNotReloaded == true
  ASSERT result.emailSentIfEnabled == true
END FOR
```

### Provjera očuvanja

**Cilj**: Verificirati da za sve ulaze gdje uslov greške NE vrijedi, ispravljena funkcionalnost proizvodi isti rezultat kao originalna funkcionalnost.

**Pseudokod:**
```
FOR ALL input WHERE NOT isBugCondition(input) DO
  ASSERT originalBehavior(input) == fixedBehavior(input)
END FOR
```

**Pristup testiranju**: Property-based testiranje je preporučeno za provjeru očuvanja jer:
- Automatski generiše mnogo test slučajeva kroz cijeli domen ulaza
- Hvata edge case-ove koje manuelni unit testovi mogu propustiti
- Pruža jake garancije da ponašanje ostaje nepromijenjeno za sve ne-buggy ulaze

**Plan testiranja**: Posmatrati ponašanje na NEISPRAVNOM kodu prvo za postojeće akcije, zatim napisati property-based testove koji hvataju to ponašanje.

**Test slučajevi**:
1. **Očuvanje postojećih akcija**: Posmatrati da sve postojeće akcije (vidi, uredi, obriši, PDF, email) rade na neispravnom kodu, zatim napisati test da verificira da nastavljaju raditi nakon ispravke
2. **Očuvanje sortiranja**: Verificirati da sortiranje po svim kolonama nastavlja raditi identično
3. **Očuvanje filtriranja**: Verificirati da filtriranje po godini i datumu nastavlja raditi
4. **Očuvanje pretrage**: Verificirati da pretraga faktura nastavlja raditi
5. **Očuvanje ukupnog iznosa**: Verificirati da ukupan uplaćeni iznos na dnu liste ostaje tačan

### Unit testovi

- Test da nova akcija postoji u HTML-u liste faktura (desktop i mobilni)
- Test da AJAX zahtjev šalje ispravne podatke (invoice ID, CSRF token)
- Test da vizuelni indikator se ažurira nakon uspješnog odgovora
- Test da error handling radi ispravno za neuspješne zahtjeve
- Test da sve postojeće akcije i dalje postoje u HTML-u

### Property-based testovi

- Generisati nasumične fakture i verificirati da klik na ikonu ažurira status plaćanja
- Generisati nasumične konfiguracije korisnika (sa/bez email notifikacija) i verificirati ispravno ponašanje
- Testirati da sve ne-payment-status interakcije nastavljaju raditi kroz mnoge scenarije

### Integracioni testovi

- Test punog toka: otvoriti listu faktura, kliknuti na ikonu, verificirati promjenu statusa u bazi
- Test email notifikacije: označiti fakturu kao plaćenu, verificirati da email bude poslan ako je omogućeno
- Test autorizacije: pokušati ažurirati fakturu koja ne pripada korisniku, verificirati 403 grešku
- Test vizuelnog feedbacka: verificirati da indikator mijenja boju nakon klika
