<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id()->comment('ID univoco del sito');
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name')->comment('Nome del sito');
            $table->string('type')->nullable()->comment('Tipologia sito');
            $table->unsignedInteger('clienti_id')->nullable()->comment('Mandante di riferimento');
            $table->boolean('is_active')->default(1)->comment('Stato del sito')->index();
            $table->string('domain')->comment('Dominio o sottodominio')->index();
            $table->boolean('is_typical')->default(1)->comment('Sito utilizzato per attività tipica');
            $table->date('privacy_date')->nullable()->comment('Data aggiornamento privacy');
            $table->date('transparency_date')->nullable()->comment('Data aggiornamento trasparenza');
            $table->date('privacy_prior_date')->nullable()->comment('Precedente aggiornamento privacy');
            $table->date('transparency_prior_date')->nullable()->comment('Precedente aggiornamento trasparenza');
            $table->string('url_privacy')->nullable()->comment('URL pagina privacy policy');
            $table->string('url_cookies')->nullable()->comment('URL pagina cookie policy');
            $table->boolean('is_footercompilant')->default(0)->comment('True se il footer è conforme GDPR');
            $table->string('url_transparency')->nullable()->comment('link trasparenza');
            $table->boolean('is_iso27001_certified')->default(0)->comment('Certificazione ISO 27001');
            $table->string('websiteable_type')->nullable();
            $table->uuid('websiteable_id')->nullable();
            $table->index(['websiteable_type', 'websiteable_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
