<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_subject_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();

            // Richiedente — può essere un Client esistente o un soggetto esterno
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('requester_name');
            $table->string('requester_email')->nullable();
            $table->string('requester_phone')->nullable();

            // Tipo di richiesta (Art. 15-22 GDPR)
            $table->enum('request_type', [
                'access',           // Art. 15 — Diritto di accesso
                'rectification',    // Art. 16 — Rettifica
                'erasure',          // Art. 17 — Cancellazione ("diritto all'oblio")
                'restriction',      // Art. 18 — Limitazione del trattamento
                'portability',      // Art. 20 — Portabilità
                'objection',        // Art. 21 — Opposizione
                'withdraw_consent', // Art. 7 par. 3 — Revoca consenso
                'other',
            ]);

            // Stato del procedimento
            $table->enum('status', [
                'received',     // Ricevuta
                'in_progress',  // In lavorazione
                'completed',    // Evasa
                'rejected',     // Rifiutata (con motivazione)
                'extended',     // Prorogata (Art. 12 par. 3 — +2 mesi)
            ])->default('received');

            // Date chiave
            $table->date('received_at');
            $table->date('deadline_at');        // 30 gg dalla ricezione (Art. 12 par. 3)
            $table->date('extended_until')->nullable(); // Proroga +2 mesi
            $table->date('completed_at')->nullable();

            // Contenuto
            $table->text('request_description');
            $table->text('response_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Verifica identità
            $table->boolean('identity_verified')->default(false);
            $table->string('identity_verification_method')->nullable(); // Es. CIE, SPID, email

            // Canale di ricezione
            $table->enum('channel', [
                'email', 'pec', 'letter', 'in_person', 'online_form', 'other',
            ])->default('email');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_subject_requests');
    }
};
