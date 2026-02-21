<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Note;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('year', now()->year);

        // Get all available years from user's invoices
        $availableYears = Invoice::where('user_id', $user->id)
            ->selectRaw('DISTINCT YEAR(datum_izdavanja) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Filter invoices by selected year
        $query = Invoice::where('user_id', $user->id)->with('client');

        if ($selectedYear !== 'all') {
            $query->whereYear('datum_izdavanja', $selectedYear);
        }

        $invoices = $query->get();
        $totalPaid = $invoices->where('placeno', true)->sum('paid_bam_amount');

        $clients = Client::where('user_id', $user->id)->orderBy('naziv_firme')->get();

        return view('invoices.index', compact('invoices', 'totalPaid', 'availableYears', 'selectedYear', 'clients'));
    }

    public function create()
    {
        $user = Auth::user();
        $clients = Client::where('user_id', $user->id)->get();

        if ($clients->isEmpty()) {
            return redirect()->route('clients.create')->with('warning', 'Morate kreirati klijenta prije nego što možete kreirati fakturu.');
        }

        $currentYear = now()->year;

        // Get last invoice for current year only
        $lastInvoice = Invoice::where('user_id', $user->id)
            ->whereYear('datum_izdavanja', $currentYear)
            ->orderBy('id', 'desc')
            ->first();

        $broj_fakture = $lastInvoice ? '#'.((int) str_replace('#', '', explode('/', $lastInvoice->broj_fakture)[0]) + 1).'/'.$currentYear : '#1/'.$currentYear;

        return view('invoices.create', compact('clients', 'broj_fakture'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $clients = Client::where('user_id', $user->id)->get();
        if ($clients->isEmpty()) {
            return redirect()->route('clients.create')->with('warning', 'Morate kreirati klijenta prije nego što možete kreirati fakturu.');
        }

        $validated = $request->validate([
            'klijent_id' => [
                'required',
                'exists:clients,id',
                function ($attribute, $value, $fail) use ($user) {
                    $client = Client::find($value);
                    if (! $client || $client->user_id !== $user->id) {
                        $fail('Odabrani klijent ne pripada trenutnom korisniku.');
                    }
                },
            ],
            'broj_fakture' => [
                'required',
                Rule::unique('invoices')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                }),
            ],
            'datum_izdavanja' => 'required|date',
            'opis_posla' => 'required',
            'kolicina' => 'required|integer|min:1',
            'cijena' => 'required|numeric',
            'valuta' => 'required|in:BAM,EUR',
        ]);

        try {
            $validated['user_id'] = $user->id;
            Invoice::create($validated);

            return redirect()->route('invoices.index')->with('success', 'Faktura kreirana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške prilikom kreiranja fakture: '.$e->getMessage())->withInput();
        }
    }

    public function show(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if currency parameter is provided for direct invoice view
        $currency = $request->get('currency');
        if ($currency === 'eur') {
            $invoice->load('client');
            return view('invoices.invoice_eur', compact('invoice'));
        } elseif ($currency === 'bam') {
            $invoice->load('client');
            return view('invoices.invoice_bam', compact('invoice'));
        }

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::where('user_id', $user->id)->get();

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'klijent_id' => [
                'required',
                'exists:clients,id',
                function ($attribute, $value, $fail) use ($user) {
                    $client = Client::find($value);
                    if (! $client || $client->user_id !== $user->id) {
                        $fail('Odabrani klijent ne pripada trenutnom korisniku.');
                    }
                },
            ],
            'broj_fakture' => [
                'required',
                'regex:/^#\d+\/\d{4}$/',
                Rule::unique('invoices')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })->ignore($invoice->id),
            ],
            'datum_izdavanja' => 'required|date',
            'opis_posla' => 'required',
            'kolicina' => 'required|integer|min:1',
            'cijena' => 'required|numeric',
            'valuta' => 'required|in:BAM,EUR',
            'placeno' => 'sometimes|boolean',
            'datum_placanja' => 'sometimes|nullable|date',
            'uplaceni_iznos_eur' => 'sometimes|nullable|numeric',
        ]);

        // If marked as paid but no payment date provided, use today's date
        if (isset($validated['placeno']) && $validated['placeno'] && empty($validated['datum_placanja'])) {
            $validated['datum_placanja'] = now()->format('Y-m-d');
        }

        // If marked as unpaid, clear payment date and amount
        if (isset($validated['placeno']) && !$validated['placeno']) {
            $validated['datum_placanja'] = null;
            $validated['uplaceni_iznos_eur'] = null;
        }

        try {
            $invoice->update($validated);
            if ($request->has('placeno') && $user->send_payment_email) {
                try {
                    \Mail::to($user->email)->send(new \App\Mail\PaymentConfirmation($invoice));
                    \Log::info('Email sent successfully to: '.$user->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send email: '.$e->getMessage());
                }
            }

            return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura ažurirana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške prilikom ažuriranja fakture: '.$e->getMessage())->withInput();
        }
    }

    public function updatePaymentStatus(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        // For AJAX toggle - toggle the current status
        if ($request->ajax() && !$request->has('placeno')) {
            $newStatus = !$invoice->placeno;

            $updateData = ['placeno' => $newStatus];

            if ($newStatus) {
                // Mark as paid - set payment date to today
                $updateData['datum_placanja'] = now()->format('Y-m-d');
            } else {
                // Mark as unpaid - clear payment date and amount
                $updateData['datum_placanja'] = null;
                $updateData['uplaceni_iznos_eur'] = null;
            }

            try {
                $invoice->update($updateData);

                if ($newStatus && $user->send_payment_email) {
                    try {
                        \Mail::to($user->email)->send(new \App\Mail\PaymentConfirmation($invoice));
                        \Log::info('Email sent successfully to: '.$user->email);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send email: '.$e->getMessage());
                    }
                }

                return response()->json([
                    'success' => true,
                    'placeno' => $newStatus,
                    'message' => 'Status plaćanja ažuriran.'
                ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        // Original form-based logic
        $validated = $request->validate([
            'placeno' => 'boolean',
            'datum_placanja' => 'nullable|date',
            'uplaceni_iznos_eur' => 'nullable|numeric',
        ]);

        $validated['placeno'] = $request->has('placeno') ? true : false;

        if (! $validated['placeno']) {
            $validated['datum_placanja'] = null;
            $validated['uplaceni_iznos_eur'] = null;
        } else {
            if (empty($validated['datum_placanja'])) {
                $validated['datum_placanja'] = now()->format('Y-m-d');
            }
        }

        try {
            $invoice->update($validated);
            if ($validated['placeno'] && $user->send_payment_email) {
                try {
                    \Mail::to($user->email)->send(new \App\Mail\PaymentConfirmation($invoice));
                    \Log::info('Email sent successfully to: '.$user->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send email: '.$e->getMessage());
                }
            }

            return redirect()->route('invoices.show', $invoice)->with('success', 'Status plaćanja ažuriran.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $invoice->delete();

            return redirect()->route('invoices.index')->with('success', 'Faktura obrisana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške prilikom brisanja fakture: '.$e->getMessage());
        }
    }

    public function viewPdf(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load('client');
        $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';

        return view($view, compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load('client');
        // Use separate PDF-optimized view for download
        $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam_pdf' : 'invoices.invoice_eur_pdf';
        $pdf = Pdf::loadView($view, compact('invoice'))
            ->setPaper('a4', 'portrait');
        $safeFileName = str_replace('/', '-', $invoice->broj_fakture);

        // Check if user wants to view in browser or download
        if (request()->has('view')) {
            return $pdf->stream($safeFileName.'.pdf');
        }

        return $pdf->download($safeFileName.'.pdf');
    }

    public function bulkDownload(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('year', now()->year);

        // Get filtered invoices (same logic as index)
        $query = Invoice::where('user_id', $user->id)->with('client');

        if ($selectedYear !== 'all') {
            $query->whereYear('datum_izdavanja', $selectedYear);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return redirect()->back()->with('error', 'Nema faktura za preuzimanje.');
        }

        // Create a temporary directory for PDFs
        $tempDir = storage_path('app/temp_invoices_' . time());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Generate PDFs for each invoice
        foreach ($invoices as $invoice) {
            $invoice->load('client');
            $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam_pdf' : 'invoices.invoice_eur_pdf';
            $pdf = Pdf::loadView($view, compact('invoice'))->setPaper('a4', 'portrait');
            $safeFileName = str_replace('/', '-', $invoice->broj_fakture);
            $pdf->save($tempDir . '/' . $safeFileName . '.pdf');
        }

        // Create ZIP file
        $zipFileName = 'fakture_' . ($selectedYear === 'all' ? 'sve' : $selectedYear) . '_' . date('Y-m-d') . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $files = glob($tempDir . '/*.pdf');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Clean up temporary directory
        array_map('unlink', glob($tempDir . '/*.pdf'));
        rmdir($tempDir);

        // Download the ZIP file
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function payments(Request $request)
    {
        $user = Auth::user();
        $selectedYear = $request->get('year', now()->year);

        // Get all available years from user's paid invoices
        $availableYears = Invoice::where('user_id', $user->id)
            ->where('placeno', true)
            ->whereNotNull('datum_placanja')
            ->selectRaw('DISTINCT YEAR(datum_placanja) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Filter invoices by selected year
        $query = Invoice::where('user_id', $user->id)
            ->where('placeno', true)
            ->whereNotNull('datum_placanja')
            ->with('client')
            ->orderBy('datum_placanja', 'asc');

        if ($selectedYear !== 'all') {
            $query->whereYear('datum_placanja', $selectedYear);
        }

        $invoices = $query->get();

        $monthlyPayments = $invoices->groupBy(function ($invoice) {
            return $invoice->datum_placanja->format('F Y');
        });

        // Calculate total for all months in selected year/period
        $totalAllMonths = $invoices->sum('paid_bam_amount');

        $notes = Note::where('user_id', $user->id)->get();

        return view('invoices.payments', compact('monthlyPayments', 'notes', 'availableYears', 'selectedYear', 'totalAllMonths'));
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        Note::create([
            'user_id' => $user->id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Napomena dodana.');
    }

    public function updateNote(Request $request, Note $note)
    {
        $user = Auth::user();
        if ($note->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $note->update(['content' => $request->content]);

        return redirect()->back()->with('success', 'Napomena ažurirana.');
    }

    public function destroyNote(Note $note)
    {
        $user = Auth::user();
        if ($note->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        return redirect()->back()->with('success', 'Napomena obrisana.');
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    public function sendEmail(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Load the client relationship to ensure it's available
            $invoice->load('client');

            // Validate that client has email
            if (! $invoice->client || ! $invoice->client->email) {
                return redirect()->back()->with('error', 'Klijent nema definisanu email adresu. Molimo dodajte email adresu klijentu prije slanja.');
            }

            $mailService = new \App\Services\MailService;
            $mailService->sendInvoiceEmail($invoice, \App\Mail\PaymentReminderMail::class, 'payment_reminder');

            return redirect()->back()->with('success', 'Email opomene je uspješno poslan klijentu na adresu: '.$invoice->client->email);
        } catch (\Exception $e) {
            \Log::error('Error in InvoiceController::sendEmail', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Greška pri slanju emaila: '.$e->getMessage());
        }
    }

    public function sendInvoice(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Load the client relationship to ensure it's available
            $invoice->load('client');

            // Validate that client has email
            if (! $invoice->client || ! $invoice->client->email) {
                return redirect()->back()->with('error', 'Klijent nema definisanu email adresu. Molimo dodajte email adresu klijentu prije slanja.');
            }

            $mailService = new \App\Services\MailService;
            $mailService->sendInvoiceEmail($invoice, \App\Mail\InvoiceMail::class, 'invoice');

            return redirect()->back()->with('success', 'Faktura je uspješno poslana emailom klijentu na adresu: '.$invoice->client->email);
        } catch (\Exception $e) {
            \Log::error('Error in InvoiceController::sendInvoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Greška pri slanju fakture: '.$e->getMessage());
        }
    }
}
