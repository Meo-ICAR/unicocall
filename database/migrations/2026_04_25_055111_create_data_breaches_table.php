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
        Schema::create('data_breaches', function (Blueprint $table) {
            $table->id();
            $table->dateTime('discovered_at');
            $table->dateTime('occurred_at');
            $table->text('description');  // Cosa è successo? (es. Smarrimento laptop)
            $table->text('nature_of_breach');  // Es. Perdita di riservatezza
            $table->integer('approximate_records_count');

            // Valutazione rapida
            $table->boolean('is_notifiable_to_authority')->default(false);  // Entro 72h?
            $table->boolean('is_notifiable_to_subjects')->default(false);  // Dobbiamo dirlo agli utenti?

            $table->text('mitigation_actions');  // Cosa abbiamo fatto per rimediare?
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
        Schema::dropIfExists('data_breaches');
    }
};
