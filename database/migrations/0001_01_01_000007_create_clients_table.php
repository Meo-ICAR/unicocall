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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('tax_code', 16)->nullable();
            $table->string('vat_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('dpo_email')->nullable();
            $table->string('company_type')->nullable();
            $table->string('privacy_policy_url')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('is_person')->default(1);
            $table->boolean('is_company_consultant')->default(0);
            $table->boolean('is_lead')->default(0);
            $table->string('status')->default('raccolta_dati')->comment('Stato nel funnel BPM');
            $table->foreignId('leadsource_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->timestamp('acquired_at')->nullable();
            $table->boolean('is_structure')->default(0);
            $table->boolean('is_regulatory')->default(0)->comment('Istituzione es. OAM / FINANZA / ISVASS');
            $table->boolean('is_ghost')->default(0)->comment('Profilo fittizio/incompleto usato per simulazioni');
            $table->boolean('is_sales')->default(1);
            $table->boolean('is_pep')->default(0)->comment('Antiriciclaggio (AML): Politically Exposed Person');
            $table->boolean('is_sanctioned')->default(0)->comment('Antiriciclaggio (AML): Soggetto presente in liste sanzionatorie');
            $table->boolean('is_remote_interaction')->default(0)->comment('Adeguata verifica a distanza');
            $table->boolean('is_requiredApprovation')->default(0);
            $table->boolean('is_approved')->default(1)->index();
            $table->boolean('is_anonymous')->default(0)->index();
            $table->boolean('is_client')->default(1);
            $table->timestamp('general_consent_at')->nullable();
            $table->timestamp('privacy_policy_read_at')->nullable();
            $table->timestamp('consent_special_categories_at')->nullable();
            $table->timestamp('consent_sic_at')->nullable();
            $table->timestamp('consent_marketing_at')->nullable();
            $table->timestamp('consent_profiling_at')->nullable();
            $table->boolean('is_consultant_gdpr')->default(0)->comment('Responsabile trattamento GDPR');
            $table->string('privacy_contact_email')->nullable()->comment('Email contatto privacy');
            $table->string('privacy_role')->nullable();
            $table->text('purpose')->nullable();
            $table->text('data_subjects')->nullable();
            $table->text('data_categories')->nullable();
            $table->string('retention_period')->nullable();
            $table->string('extra_eu_transfer')->nullable();
            $table->text('security_measures')->nullable();
            $table->string('privacy_data')->nullable();
            $table->foreignId('client_type_id')->nullable()->constrained('client_types')->nullOnDelete();

            $table->boolean('privacy_consent')->default(0);
            $table->text('subfornitori')->nullable();

            $table->string('servizio')->nullable();
            $table->json('categorie_dati')->nullable();
            $table->string('nomina')->nullable();
            $table->timestamp('nomina_at')->nullable();
            $table->text('istruzioni')->nullable();
            $table->json('documents')->nullable()->comment('Documents uploaded for client');

            $table->timestamp('blacklist_at')->nullable()->index();
            $table->string('blacklisted_by')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('salary_quote', 10, 2)->nullable();
            $table->boolean('is_art108')->default(0)->comment('Riferimento normativo specifico');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
