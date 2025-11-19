<x-mail::message>
# OPOMENA - Faktura {{ $invoice->broj_fakture }}

Poštovani,

Ovo je **opomena** da faktura broj **{{ $invoice->broj_fakture }}** još uvijek nije plaćena.

## Detalji fakture

- **Broj fakture:** {{ $invoice->broj_fakture }}
- **Datum izdavanja:** {{ $invoice->datum_izdavanja->format('d.m.Y') }}
- **Klijent:** {{ $invoice->client->naziv_firme }}
- **Opis:** {{ $invoice->opis_posla }}
- **Iznos:** 
@if ($invoice->valuta === 'BAM')
{{ number_format($invoice->cijena, 2) }} KM
@else
{{ number_format($invoice->cijena, 2) }} EUR ({{ number_format($invoice->bam_amount, 2) }} KM)
@endif

Faktura u PDF formatu nalazi se u prilogu ove poruke.

**Molimo Vas da izvršite uplatu odmah kako biste izbjegli dodatne troškove i pravne korake.**

Ako ste već izvršili uplatu, molimo Vas da zanemarite ovu poruku i kontaktirate nas sa potvrdom o uplati.

S poštovanjem,<br>
{{ config('app.name') }}
</x-mail::message>
