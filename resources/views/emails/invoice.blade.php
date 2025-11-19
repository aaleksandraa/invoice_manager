<x-mail::message>
# Faktura {{ $invoice->broj_fakture }}

Poštovani,

U prilogu ove poruke možete pronaći fakturu broj **{{ $invoice->broj_fakture }}**.

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

Hvala vam na saradnji.

S poštovanjem,<br>
{{ config('app.name') }}
</x-mail::message>
