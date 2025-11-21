<x-mail::message>
# Opomena - Neplaćena faktura {{ $invoice->broj_fakture }}

Poštovani,

Obavještavamo Vas da je faktura br. **{{ $invoice->broj_fakture }}** još uvijek neplaćena.

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

Molimo Vas da u najkraćem roku izmirite Vaše obaveze prema priloženom računu.

U prilogu se nalazi kopija fakture za Vaše evidencije.

Hvala na razumijevanju i saradnji.

S poštovanjem,<br>
@if($invoice->user->companyProfile)
{{ $invoice->user->companyProfile->company_name }}
@else
{{ config('app.name') }}
@endif
</x-mail::message>
