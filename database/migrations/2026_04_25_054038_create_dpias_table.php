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
        Schema::create('dpias', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignId('registro_trattamenti_item_id');  // Collegamento al Registro Trattamenti
            $table->text('description_of_processing');
            $table->text('necessity_assessment');  // Perché è necessaria? (es. monitoraggio sistematico)

            // Valutazione di necessità e proporzionalità
            $table->boolean('is_necessary')->default(true);
            $table->boolean('is_proportional')->default(true);

            $table->enum('status', ['draft', 'under_review', 'completed'])->default('draft');
            $table->text('dpo_opinion')->nullable();  // Parere obbligatorio del DPO
            $table->date('completion_date')->nullable();
            $table->date('next_review_date')->nullable();

            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpias');
    }
};
