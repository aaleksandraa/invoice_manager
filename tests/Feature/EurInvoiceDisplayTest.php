<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EurInvoiceDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function createTestInvoice()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'naziv_firme' => 'Test Company',
            'adresa' => 'Test Address 123',
            'postanski_broj_mjesto_drzava' => '71000 Sarajevo, BiH',
            'pdv_broj' => '123456789',
            'email' => 'test@example.com',
        ]);

        return Invoice::factory()->create([
            'user_id' => $user->id,
            'klijent_id' => $client->id,
            'broj_fakture' => '##001/2025',
            'datum_izdavanja' => now(),
            'opis_posla' => 'Software Development Services',
            'kolicina' => 1,
            'cijena' => 1000.00,
        ]);
    }

    public function test_eur_web_invoice_contains_all_banking_information()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.show', ['invoice' => $invoice->id, 'currency' => 'eur']));

        $response->assertStatus(200);

        // Check for IBAN
        $response->assertSee('IBAN: BA395676510000114506', false);

        // Check for SWIFT
        $response->assertSee('SWIFT: SABRBA2B', false);

        // EUR invoices should NOT have "Račun AtosBank" in the issuer section
        // It should only be in the footer as IBAN
        $response->assertDontSee('Račun AtosBank: 5676512500038858', false);

        // Check for company name with proper encoding
        $response->assertSee('Računarsko programiranje "Wizionar"', false);
    }

    public function test_eur_web_invoice_contains_vat_status_in_english()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.show', ['invoice' => $invoice->id, 'currency' => 'eur']));

        $response->assertStatus(200);
        $response->assertSee('Wizionar is not a part of the VAT system', false);
    }

    public function test_eur_web_invoice_uses_english_labels()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.show', ['invoice' => $invoice->id, 'currency' => 'eur']));

        $response->assertStatus(200);

        // Check English labels
        $response->assertSee('Invoice Number:', false);
        $response->assertSee('Invoice Date:', false);
        $response->assertSee('Invoice to:', false);
        $response->assertSee('Description', false);
        $response->assertSee('Quantity', false);
        $response->assertSee('Amount (EUR)', false);
        $response->assertSee('Total in EUR:', false);
        $response->assertSee('Authorized by', false);
        $response->assertSee('Customer', false);
    }

    public function test_eur_web_invoice_displays_eur_currency()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.show', ['invoice' => $invoice->id, 'currency' => 'eur']));

        $response->assertStatus(200);
        $response->assertSee('1,000.00 EUR', false);
    }

    public function test_eur_web_invoice_contains_payment_instruction_note()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.show', ['invoice' => $invoice->id, 'currency' => 'eur']));

        $response->assertStatus(200);
        $response->assertSee("Please ensure that the payment instruction is set to 'OUR'", false);
    }

    public function test_eur_pdf_invoice_generates_successfully()
    {
        $invoice = $this->createTestInvoice();

        $response = $this->actingAs($invoice->user)
            ->get(route('invoices.download', ['invoice' => $invoice->id, 'view' => true]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
