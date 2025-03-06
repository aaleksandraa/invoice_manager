<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InvoicesExport;
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
        $broj_fakture = $lastInvoice ? '#' . ((int)str_replace('#', '', explode('/', $lastInvoice->broj_fakture)[0]) + 1) . '/' . now()->year : '#1/' . now()->year;

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
                    if (!$client || $client->user_id !== $user->id) {
                        $fail('Odabrani klijent ne pripada trenutnom korisniku.');
                    }
                },
            ],
            'broj_fakture' => [
                'required',
                // Provjeravamo jedinstvenost broj_fakture unutar korisnika
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
            $validated['user_id'] = $user->id; // Dodajemo user_id prilikom kreiranja
            Invoice::create($validated);
            return redirect()->route('invoices.index')->with('success', 'Faktura kreirana.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Došlo je do greške prilikom kreiranja fakture: ' . $e->getMessage())->withInput();
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

    public function update(Request $request, Invoice $invoice)
    {
        $user = Auth::user();
        if ($invoice->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'placeno' => 'sometimes|boolean',
            'datum_placanja' => 'sometimes|nullable|date',
            'uplaceni_iznos_eur' => 'sometimes|nullable|numeric',
        ]);

        $invoice->update([
            'placeno' => $request->has('placeno'),
            'datum_placanja' => $request->datum_placanja,
            'uplaceni_iznos_eur' => $request->uplaceni_iznos_eur,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura ažurirana.');
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
        return $pdf->download($safeFileName . '.pdf');
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