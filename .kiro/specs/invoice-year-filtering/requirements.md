# Requirements Document

## Introduction

Sistem za upravljanje fakturama trenutno ne omogućava editovanje broja i godine fakture, niti filtriranje faktura po godinama. Ova funkcionalnost će omogućiti korisnicima da:
- Edituju broj i godinu fakture
- Automatski resetuju brojač faktura na početak svake nove godine
- Filtriraju fakture po godinama na listi faktura
- Filtriraju plaćanja po godinama na stranici plaćanja

## Glossary

- **Invoice_System**: Sistem za upravljanje fakturama
- **Invoice_Number**: Broj fakture u formatu #N/YYYY (npr. #1/2026)
- **Invoice_Year**: Godina fakture ekstrahovana iz broja fakture
- **Current_Year**: Trenutna kalendarska godina
- **Invoice_List**: Stranica koja prikazuje sve fakture korisnika
- **Payment_Page**: Stranica koja prikazuje plaćene fakture
- **Year_Filter**: Filter koji omogućava prikaz faktura za određenu godinu
- **User**: Prijavljeni korisnik sistema

## Requirements

### Requirement 1: Editovanje broja i godine fakture

**User Story:** Kao korisnik, želim da mogu editovati broj i godinu fakture, tako da mogu ispraviti greške ili dodati fakture za prethodne godine.

#### Acceptance Criteria

1. WHEN korisnik edituje fakturu, THE Invoice_System SHALL omogućiti izmjenu polja broj fakture
2. WHEN korisnik unese novi broj fakture, THE Invoice_System SHALL validirati format #N/YYYY
3. WHEN korisnik sačuva izmjene, THE Invoice_System SHALL provjeriti da li broj fakture već postoji za tog korisnika
4. IF broj fakture već postoji za tog korisnika, THEN THE Invoice_System SHALL prikazati grešku i spriječiti čuvanje
5. WHEN korisnik uspješno sačuva izmjene, THE Invoice_System SHALL ažurirati fakturu sa novim brojem

### Requirement 2: Automatsko generisanje broja fakture po godinama

**User Story:** Kao korisnik, želim da sistem automatski resetuje brojač faktura na početak svake nove godine, tako da svaka godina počinje sa #1/YYYY.

#### Acceptance Criteria

1. WHEN korisnik kreira novu fakturu, THE Invoice_System SHALL automatski generisati broj fakture
2. WHEN se generiše broj fakture, THE Invoice_System SHALL koristiti trenutnu godinu kao godinu fakture
3. WHEN se generiše broj fakture za novu godinu, THE Invoice_System SHALL početi brojanje od #1/YYYY
4. WHEN se generiše broj fakture za postojeću godinu, THE Invoice_System SHALL nastaviti brojanje od posljednjeg broja te godine
5. THE Invoice_System SHALL tražiti posljednji broj fakture samo za trenutnu godinu korisnika

### Requirement 3: Filtriranje faktura po godinama na listi faktura

**User Story:** Kao korisnik, želim da vidim fakture filtrirane po godinama, tako da mogu lako pregledati fakture iz određene godine.

#### Acceptance Criteria

1. WHEN korisnik otvori listu faktura, THE Invoice_System SHALL prikazati fakture za trenutnu godinu po defaultu
2. THE Invoice_System SHALL prikazati dropdown filter sa dostupnim godinama
3. WHEN korisnik odabere godinu iz filtera, THE Invoice_System SHALL prikazati samo fakture za tu godinu
4. THE Invoice_System SHALL prikazati sve godine za koje korisnik ima fakture u dropdown filteru
5. THE Invoice_System SHALL sortirati godine u dropdown filteru od najnovije ka najstarijoj
6. WHEN korisnik odabere opciju "Sve godine", THE Invoice_System SHALL prikazati sve fakture korisnika
7. THE Invoice_System SHALL sačuvati odabranu godinu u URL parametru za dijeljenje i bookmark

### Requirement 4: Filtriranje plaćanja po godinama

**User Story:** Kao korisnik, želim da vidim plaćanja filtrirana po godinama na stranici plaćanja, tako da mogu lako pregledati prihode iz određene godine.

#### Acceptance Criteria

1. WHEN korisnik otvori stranicu plaćanja, THE Invoice_System SHALL prikazati plaćanja za trenutnu godinu po defaultu
2. THE Invoice_System SHALL prikazati dropdown filter sa dostupnim godinama na stranici plaćanja
3. WHEN korisnik odabere godinu iz filtera, THE Invoice_System SHALL prikazati samo plaćanja za tu godinu
4. THE Invoice_System SHALL prikazati sve godine za koje korisnik ima plaćanja u dropdown filteru
5. THE Invoice_System SHALL sortirati godine u dropdown filteru od najnovije ka najstarijoj
6. WHEN korisnik odabere opciju "Sve godine", THE Invoice_System SHALL prikazati sva plaćanja korisnika
7. THE Invoice_System SHALL ažurirati ukupan iznos plaćanja na osnovu odabrane godine
8. THE Invoice_System SHALL sačuvati odabranu godinu u URL parametru za dijeljenje i bookmark

### Requirement 5: Validacija i integritet podataka

**User Story:** Kao korisnik, želim da sistem osigura integritet podataka, tako da ne mogu postojati duplikati brojeva faktura.

#### Acceptance Criteria

1. WHEN korisnik kreira ili edituje fakturu, THE Invoice_System SHALL validirati da broj fakture ne postoji za tog korisnika
2. THE Invoice_System SHALL dozvoliti isti broj fakture različitim korisnicima
3. IF validacija ne prođe, THEN THE Invoice_System SHALL prikazati jasnu poruku greške
4. THE Invoice_System SHALL spriječiti čuvanje fakture sa duplikatom broja
5. WHEN korisnik edituje fakturu, THE Invoice_System SHALL dozvoliti čuvanje sa istim brojem fakture (bez promjene)

### Requirement 6: Kompatibilnost sa postojećim funkcionalnostima

**User Story:** Kao korisnik, želim da nove funkcionalnosti rade sa postojećim funkcionalnostima sistema, tako da ne izgubim postojeće mogućnosti.

#### Acceptance Criteria

1. WHEN se primijene izmjene, THE Invoice_System SHALL zadržati sve postojeće funkcionalnosti kreiranja faktura
2. WHEN se primijene izmjene, THE Invoice_System SHALL zadržati sve postojeće funkcionalnosti editovanja faktura
3. WHEN se primijene izmjene, THE Invoice_System SHALL zadržati funkcionalnost slanja emailova
4. WHEN se primijene izmjene, THE Invoice_System SHALL zadržati funkcionalnost generisanja PDF-a
5. WHEN se primijene izmjene, THE Invoice_System SHALL zadržati funkcionalnost exporta faktura
