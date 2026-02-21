# Dokument zahtjeva za ispravku greške

## Uvod

Korisnici ne mogu označiti fakture kao plaćene direktno iz liste faktura. Iako backend funkcionalnost za ažuriranje statusa plaćanja postoji (metoda `updatePaymentStatus` u `InvoiceController`), nedostaje akcija u korisničkom interfejsu koja omogućava korisnicima da brzo označe fakturu kao plaćenu ili neplaćenu iz liste faktura.

Trenutno, korisnici moraju otvoriti pojedinačnu fakturu da bi ažurirali status plaćanja, što usporava radni tok, posebno kada je potrebno ažurirati više faktura.

## Analiza greške

### Trenutno ponašanje (defekt)

1.1 KADA korisnik gleda listu faktura TADA sistem ne prikazuje akciju za označavanje fakture kao plaćene

1.2 KADA korisnik želi promijeniti status plaćanja fakture TADA sistem zahtijeva da korisnik otvori pojedinačnu fakturu umjesto da omogući brzu akciju iz liste

1.3 KADA korisnik ima više faktura koje treba označiti kao plaćene TADA sistem zahtijeva višestruke navigacije između liste i pojedinačnih faktura

### Očekivano ponašanje (ispravno)

2.1 KADA korisnik gleda listu faktura TADA sistem TREBA prikazati akciju (dugme/ikonu) za označavanje fakture kao plaćene direktno iz liste

2.2 KADA korisnik klikne na akciju za označavanje kao plaćeno TADA sistem TREBA ažurirati status plaćanja fakture bez napuštanja liste faktura

2.3 KADA korisnik označi fakturu kao plaćenu iz liste TADA sistem TREBA postaviti datum plaćanja na trenutni datum ako nije već postavljen

2.4 KADA korisnik označi fakturu kao plaćenu i korisnik ima omogućenu opciju slanja email obavještenja TADA sistem TREBA poslati email potvrdu o plaćanju

2.5 KADA korisnik označi fakturu kao plaćenu TADA sistem TREBA prikazati vizuelnu potvrdu promjene statusa (zeleni indikator)

2.6 KADA korisnik klikne na akciju za označavanje kao neplaćeno (ako je faktura već plaćena) TADA sistem TREBA ukloniti status plaćanja i očistiti datum plaćanja

### Nepromijenjeno ponašanje (prevencija regresije)

3.1 KADA korisnik ažurira status plaćanja kroz postojeću formu na stranici pojedinačne fakture TADA sistem TREBA NASTAVITI raditi na isti način kao i prije

3.2 KADA korisnik gleda listu faktura TADA sistem TREBA NASTAVITI prikazivati sve postojeće akcije (vidi, uredi, obriši, preuzmi PDF, pošalji fakturu, pošalji opomenu)

3.3 KADA korisnik sortira ili filtrira fakture po statusu plaćanja TADA sistem TREBA NASTAVITI raditi na isti način kao i prije

3.4 KADA korisnik gleda ukupno uplaćeni iznos na dnu liste TADA sistem TREBA NASTAVITI prikazivati tačan zbir plaćenih faktura

3.5 KADA korisnik nema autorizaciju za fakturu TADA sistem TREBA NASTAVITI vraćati 403 grešku

3.6 KADA se ažurira status plaćanja TADA sistem TREBA NASTAVITI koristiti postojeću `updatePaymentStatus` metodu u `InvoiceController`
