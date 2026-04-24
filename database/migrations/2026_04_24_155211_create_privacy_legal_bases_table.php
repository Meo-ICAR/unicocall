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
        Schema::create('privacy_legal_bases', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome breve: Consenso, Contratto, ecc.');
            $table->string('reference_article')->default('Art. 6 par. 1 lett. ...')->comment('Riferimento articolo GDPR');
            $table->text('description')->comment('Spiegazione estesa della base giuridica');
            $table->timestamps();

            $table->index('name', 'privacy_legal_bases_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_legal_bases');
    }
};
