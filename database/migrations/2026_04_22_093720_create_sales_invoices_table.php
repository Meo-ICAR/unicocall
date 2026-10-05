<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->comment('Numero fattura');
            $table->string('nome_file')->nullable()->comment('Nome del file XML');
            $table->string('id_sdi')->nullable()->comment('ID SdI - Sistema di Interscambio');
            $table->date('data_invio')->nullable()->comment('Data di invio');
            $table->date('data_documento')->nullable()->comment('Data del documento');
            $table->string('tipo_documento')->nullable()->comment('Tipo documento (es. TD01)');
            $table->string('tipo_cliente')->nullable()->comment('Tipo cliente');
            $table->string('cliente')->comment('Nome del cliente');
            $table->string('partita_iva', 13)->nullable()->comment('Partita IVA del cliente');
            $table->string('codice_fiscale', 16)->nullable()->comment('Codice Fiscale del cliente');
            $table->string('indirizzo_telematico')->nullable()->comment('Indirizzo telematico/PEC');
            $table->string('metodo_pagamento')->nullable()->comment('Metodo di pagamento');
            $table->decimal('totale_imponibile', 10, 2)->nullable()->comment('Totale imponibile');
            $table->decimal('totale_escluso_iva_n1', 10, 2)->default(0)->comment('Totale escluso IVA (N1)');
            $table->decimal('totale_non_soggetto_iva_n2', 10, 2)->default(0)->comment('Totale non soggetto IVA (N2)');
            $table->decimal('totale_non_imponibile_iva_n3', 10, 2)->default(0)->comment('Totale non imponibile IVA (N3)');
            $table->decimal('totale_esente_iva_n4', 10, 2)->default(0)->comment('Totale esente IVA (N4)');
            $table->decimal('totale_regime_margine_iva_n5', 10, 2)->default(0)->comment('Totale regime del margine/IVA non esposta (N5)');
            $table->decimal('totale_inversione_contabile_n6', 10, 2)->default(0)->comment('Totale inversione contabile (N6)');
            $table->decimal('totale_iva_assolta_altro_stato_ue_n7', 10, 2)->default(0)->comment('Totale importo assoggettato ad IVA assolta in altro stato UE (N7)');
            $table->decimal('totale_iva', 10, 2)->default(0)->comment('Totale IVA');
            $table->decimal('totale_documento', 10, 2)->comment('Totale documento');
            $table->decimal('netto_a_pagare', 10, 2)->comment('Netto a pagare');
            $table->string('incassi')->nullable()->comment('Stato incassi');
            $table->date('data_incasso')->nullable()->comment('Data incasso');
            $table->string('stato')->comment('Stato fattura');

            // Foreign keys
            $table->uuid('company_id')->nullable()->comment('Azienda proprietaria della fattura');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['numero', 'data_documento'], 'sales_invoices_numero_data_index');
            $table->index(['cliente'], 'sales_invoices_cliente_index');
            $table->index(['partita_iva'], 'sales_invoices_partita_iva_index');
            $table->index(['data_documento'], 'sales_invoices_data_documento_index');
            $table->index(['stato'], 'sales_invoices_stato_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
