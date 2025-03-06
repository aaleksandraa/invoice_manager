<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
{
    $invoices = Invoice::with('client')->get();
    $totalPaid = $invoices->where('placeno', true)->sum('paid_bam_amount');
    return view('invoices.index', compact('invoices', 'totalPaid'));
}

    

    public function create()
    {
        $clients = Client::all();
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? (int)str_replace('#', '', explode('/', $lastInvoice->broj_fakture)[0]) + 1 : 1;
        $broj_fakture = "#{$nextNumber}/" . now()->year;
        return view('invoices.create', compact('clients', 'broj_fakture'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'klijent_id' => 'required|exists:clients,id',
            'broj_fakture' => 'required|unique:invoices',
            'datum_izdavanja' => 'required|date',
            'opis_posla' => 'required',
            'kolicina' => 'required|integer|min:1',
            'cijena' => 'required|numeric',
            'valuta' => 'required|in:BAM,EUR',
        ]);

        Invoice::create($request->all());
        return redirect()->route('invoices.index')->with('success', 'Faktura kreirana.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'placeno' => 'required|boolean',
            'datum_placanja' => 'nullable|date',
            'uplaceni_iznos_eur' => 'nullable|numeric|required_if:valuta,EUR',
        ]);

        $invoice->update($request->only('placeno', 'datum_placanja', 'uplaceni_iznos_eur'));
        return redirect()->route('invoices.index')->with('success', 'Status ažuriran.');
    }

    public function viewPdf(Invoice $invoice)
{
    // Učitavanje relacije client
    $invoice->load('client');

    $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';
    return view($view, compact('invoice'));
}

public function download(Invoice $invoice)
{
    // Učitavanje relacije client
    $invoice->load('client');

    $view = $invoice->valuta === 'BAM' ? 'invoices.invoice_bam' : 'invoices.invoice_eur';
    $pdf = Pdf::loadView($view, compact('invoice'));

    // Očistimo broj_fakture od nedozvoljenih karaktera
    $safeFileName = str_replace('/', '-', $invoice->broj_fakture);
    
    return $pdf->download($safeFileName . '.pdf');
}

    public function payments()
    {
        $invoices = Invoice::where('placeno', true)->with('client')->get();
        $monthlyPayments = $invoices->groupBy(function ($invoice) {
            return $invoice->datum_placanja->format('F Y');
        });
        return view('invoices.payments', compact('monthlyPayments'));
    }
}