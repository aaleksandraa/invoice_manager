<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Invoice::where('user_id', auth()->id())->with('client')->get();
    }

    public function headings(): array
    {
        return [
            'Broj fakture',
            'Datum izdavanja',
            'Klijent',
            'Opis posla',
            'Količina',
            'Cijena',
            'Valuta',
            'Plaćeno',
            'Datum plaćanja',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->broj_fakture,
            $invoice->datum_izdavanja->format('d.m.Y'),
            $invoice->client->naziv_firme,
            $invoice->opis_posla,
            $invoice->kolicina,
            $invoice->cijena,
            $invoice->valuta,
            $invoice->placeno ? 'Da' : 'Ne',
            $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '-',
        ];
    }
}
