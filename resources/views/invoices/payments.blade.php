@extends('layouts.app')

@section('content')
    <!-- Year Filter -->
    <div class="mb-6 flex items-center space-x-4">
        <h1 class="text-2xl font-bold">Plaćanja</h1>
        <div class="flex items-center space-x-2">
            <label for="yearFilter" class="text-gray-700">Godina:</label>
            <select id="yearFilter" class="border p-2 rounded" onchange="filterByYear(this.value)">
                <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Sve godine</option>
                @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

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

    <!-- Ukupan zbir svih mjeseci -->
    @if($monthlyPayments->isNotEmpty())
        <div class="mt-8 mb-12">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">
                        UKUPNO ZA {{ $selectedYear === 'all' ? 'SVE GODINE' : $selectedYear }}. GODINU
                    </h2>
                    <p class="text-3xl font-bold">
                        {{ number_format($totalAllMonths, 2) }} KM
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Sekcija za napomene -->
    <div class="mt-12 bg-white p-6 rounded shadow-md">
        <h2 class="text-xl font-semibold mb-4">Napomene</h2>

        <!-- Forma za dodavanje nove napomene -->
        <form method="POST" action="{{ route('notes.store') }}" class="mb-4">
            @csrf
            <div class="flex flex-col sm:flex-row gap-4">
                <textarea name="content" rows="3" class="w-full border p-2 rounded @error('content') border-red-500 @enderror" placeholder="Unesite napomenu...">{{ old('content') }}</textarea>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Dodaj</button>
            </div>
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @endif
        </form>

        <!-- Lista napomena -->
        @if ($notes->isNotEmpty())
            <ul class="list-disc pl-5">
                @foreach ($notes as $note)
                    <li class="mb-4" id="note-{{ $note->id }}">
                        <!-- Prikaz napomene -->
                        <div class="flex justify-between items-start note-content">
                            <div>
                                <p class="text-gray-800">{{ $note->content }}</p>
                                <small class="text-gray-500">{{ $note->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                            <div class="space-x-2">
                                <button type="button" onclick="showEditForm({{ $note->id }})" class="text-yellow-500 hover:underline">Izmijeni</button>
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline" onsubmit="return confirm('Jeste li sigurni da želite obrisati ovu napomenu?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Obriši</button>
                                </form>
                            </div>
                        </div>

                        <!-- Forma za uređivanje (sakrivena po defaultu) -->
                        <div class="hidden edit-form mt-2" id="edit-form-{{ $note->id }}">
                            <form method="POST" action="{{ route('notes.update', $note) }}">
                                @csrf
                                @method('PUT')
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <textarea name="content" rows="3" class="w-full border p-2 rounded @error('content') border-red-500 @enderror">{{ old('content', $note->content) }}</textarea>
                                    <div class="flex space-x-2">
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Spremi</button>
                                        <button type="button" onclick="hideEditForm({{ $note->id }})" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Otkaži</button>
                                    </div>
                                </div>
                                @error('content')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @endif
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Nema napomena.</p>
        @endif
    </div>

    <!-- Prikaz poruka -->
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded mt-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Skripta za prikaz/sakrivanje forme za uređivanje -->
    <script>
        // Function to filter by year
        function filterByYear(year) {
            const url = new URL(window.location.href);
            if (year === 'all') {
                url.searchParams.delete('year');
            } else {
                url.searchParams.set('year', year);
            }
            window.location.href = url.toString();
        }

        function showEditForm(noteId) {
            // Sakrij sve forme za uređivanje
            document.querySelectorAll('.edit-form').forEach(form => form.classList.add('hidden'));
            document.querySelectorAll('.note-content').forEach(content => content.classList.remove('hidden'));

            // Prikaz forme za određenu napomenu
            document.getElementById('edit-form-' + noteId).classList.remove('hidden');
            document.getElementById('note-' + noteId).querySelector('.note-content').classList.add('hidden');
        }

        function hideEditForm(noteId) {
            document.getElementById('edit-form-' + noteId).classList.add('hidden');
            document.getElementById('note-' + noteId).querySelector('.note-content').classList.remove('hidden');
        }
    </script>
@endsection
