<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();

            // Partecipante — polimorfico: Employee o Client
            $table->morphs('trainable'); // trainable_type + trainable_id

            // Quadro normativo di riferimento
            $table->enum('regulatory_framework', [
                'gdpr',
                'oam',
                'ivass',
                'sicurezza_lavoro', // D.Lgs. 81/08
                'antiriciclaggio',  // D.Lgs. 231/07
                'mifid',
                'other',
            ]);

            // Corso
            $table->string('course_title');
            $table->text('course_description')->nullable();
            $table->string('provider')->nullable();         // Ente erogatore
            $table->string('trainer')->nullable();          // Docente / formatore

            // Modalità
            $table->enum('delivery_mode', [
                'in_person',    // In aula
                'online',       // E-learning
                'blended',      // Misto
                'on_the_job',   // Affiancamento
                'webinar',
            ])->default('in_person');

            // Date e durata
            $table->date('training_date');
            $table->date('expiry_date')->nullable();        // Scadenza validità (es. OAM ogni 30h/anno)
            $table->decimal('hours', 5, 1)->default(0);    // Ore di formazione

            // Esito
            $table->enum('outcome', [
                'passed',       // Superato
                'failed',       // Non superato
                'attended',     // Frequentato (senza esame)
                'partial',      // Parziale
            ])->default('attended');

            $table->decimal('score', 5, 2)->nullable();    // Voto / punteggio
            $table->boolean('certificate_issued')->default(false);
            $table->string('certificate_number')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
