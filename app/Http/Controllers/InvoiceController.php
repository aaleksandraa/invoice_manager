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
    public function index()
    {
        $user = Auth::user();
        $invoices = Invoice::where('user_id', $user->id)->with('client')->get();
        $totalPaid = $invoices->where('placeno', true)->sum('paid_bam_amount');

        return view('invoices.index', compact('invoices', 'totalPaid'));
    }

    public function create()
    {
        $user = Auth::user();
        $clients = Client::where('user_id', $user->id)->get();

        if ($clients->isEmpty()) {
            return redirect()->route('clients.create')->with('warning', 'Morate kreirati klijenta prije nego što možete kreirati fakturu.');
        }

        $lastInvoice = Invoice::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        $broj_fakture = $lastInvoice ? '#'.((int) str_replace('#', '', explode('/', $lastInvoice->broj_fakture)[0]) + 1).'/'.now()->year : '#1/'.now()->year;

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

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
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
            'datum_izdavanja' => 'required|date',
            'opis_posla' => 'required',
            'kolicina' => 'required|integer|min:1',
            'cijena' => 'required|numeric',
            'valuta' => 'required|in:BAM,EUR',
            'placeno' => 'sometimes|boolean',
            'datum_placanja' => 'sometimes|nullable|date',
            'uplaceni_iznos_eur' => 'sometimes|nullable|numeric',
        ]);

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
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'placeno' => 'boolean',
            'datum_placanja' => 'nullable|date',
            'uplaceni_iznos_eur' => 'nullable|numeric',
        ]);

        // Handle checkbox: if not present, it means unchecked
        $validated['placeno'] = $request->has('placeno') ? true : false;

        // If not paid, clear payment date and amount
        if (! $validated['placeno']) {
            $validated['datum_placanja'] = null;
            $validated['uplaceni_iznos_eur'] = null;
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
        $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';
        $pdf = Pdf::loadView($view, compact('invoice'))
            ->setPaper('a4', 'portrait');
        $safeFileName = str_replace('/', '-', $invoice->broj_fakture);

        return $pdf->download($safeFileName.'.pdf');
    }

    public function payments()
    {
        $user = Auth::user();
        $invoices = Invoice::where('user_id', $user->id)
            ->where('placeno', true)
            ->with('client')
            ->get();

        $monthlyPayments = $invoices->groupBy(function ($invoice) {
            return $invoice->datum_placanja->format('F Y');
        });

        $notes = Note::where('user_id', $user->id)->get();

        return view('invoices.payments', compact('monthlyPayments', 'notes'));
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
}
