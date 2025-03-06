@extends('layouts.app')

@section('content')
    
    @foreach ($monthlyPayments as $month => $invoices)
        <!-- Prevod mjeseca na bosanski -->
        @php
            $translatedMonth = match($month) {
                'January ' . date('Y') => 'Januar ' . date('Y'),
                'February ' . date('Y') => 'Februar ' . date('Y'),
                'March ' . date('Y') => 'Mart ' . date('Y'),
                'April ' . date('Y') => 'April ' . date('Y'),
                'May ' . date('Y') => 'Maj ' . date('Y'),
                'June ' . date('Y') => 'Juni ' . date('Y'),
                'July ' . date('Y') => 'Juli ' . date('Y'),
                'August ' . date('Y') => 'August ' . date('Y'),
                'September ' . date('Y') => 'Septembar ' . date('Y'),
                'October ' . date('Y') => 'Oktobar ' . date('Y'),
                'November ' . date('Y') => 'Novembar ' . date('Y'),
                'December ' . date('Y') => 'Decembar ' . date('Y'),
                default => $month,
            };
        @endphp

        <h2 class="text-xl font-semibold mt-4">{{ $translatedMonth }}</h2>

        <!-- Tabelarni prikaz za desktop (sm i veći) -->
        <div class="hidden sm:block">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-t">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="p-3 text-left">Broj fakture</th>
                            <th class="p-3 text-left">Datum plaćanja</th>
                            <th class="p-3 text-left">Klijent</th>
                            <th class="p-3 text-left">Iznos (BAM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr class="border-b">
                                <td class="p-3">{{ $invoice->broj_fakture }}</td>
                                <td class="p-3">{{ $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '-' }}</td>
                                <td class="p-3">{{ $invoice->client->naziv_firme }}</td>
                                <td class="p-3">{{ number_format($invoice->paid_bam_amount, 2) }} KM</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Red "Ukupno" bez margine/paddinga prema tabeli -->
            <p class="bg-black text-white p-3 rounded-b"><strong>Ukupno:</strong> {{ number_format($invoices->sum('paid_bam_amount'), 2) }} KM</p>
        </div>
        <br><br>

        <!-- Prikaz za mobilne uređaje (manje od sm) -->
        <div class="block sm:hidden">
            @foreach ($invoices as $invoice)
                <div class="bg-white p-4 rounded-t shadow-md">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <p class="font-semibold">{{ $invoice->broj_fakture }}</p>
                            <p class="text-gray-600">
                                {{ $invoice->datum_placanja ? $invoice->datum_placanja->format('d.m.Y') : '-' }}
                            </p>
                        </div>
                    </div>
                    <p class="mb-2">{{ $invoice->client->naziv_firme }}</p>
                    <p>{{ number_format($invoice->paid_bam_amount, 2) }} KM</p>
                </div>
            @endforeach
            <!-- Red "Ukupno" bez margine/paddinga prema posljednjoj kartici -->
            <p class="bg-black text-white p-3 rounded-b"><strong>Ukupno:</strong> {{ number_format($invoices->sum('paid_bam_amount'), 2) }} KM</p>
        </div>

        <!-- Veći razmak između mjeseci -->
        <div class="mb-12"></div>
    @endforeach
@endsection