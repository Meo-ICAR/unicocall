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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('servizio')->nullable()->after('salary_quote');
            $table->json('categorie_dati')->nullable()->after('servizio');
            $table->string('nomina')->nullable()->after('categorie_dati');
            $table->timestamp('nomina_at')->nullable()->after('nomina');
            $table->text('istruzioni')->nullable()->after('nomina_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['servizio', 'categorie_dati', 'nomina', 'nomina_at', 'istruzioni']);
        });
    }
};
