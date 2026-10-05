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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id()->comment('ID intero autoincrementante');

            $table->string('name')->nullable()->comment('Descrizione');
            $table->unsignedBigInteger('address_type_id')->nullable()->comment('Relazione con tipologia indirizzo');
            $table->string('street')->nullable()->comment('Via');
            $table->string('numero')->nullable()->comment('Numero civico o identificativo indirizzo');
            $table->string('city')->nullable()->comment('Città o Comune');
            $table->string('province', 2)->nullable()->comment('Provincia');
            $table->string('zip_code', 20)->nullable()->comment('CAP (Codice di Avviamento Postale)');
            $table->string('country')->nullable()->comment('Paese');
            $table->string('addressable_type')->comment('Classe del Modello collegato (es. App\Models\Client)');
            $table->string('addressable_id')->comment('ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)');
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
            //   $table->userstamps();
            //   $table->softUserstamps();

            // Indexes
            $table->index(['addressable_type', 'addressable_id'], 'addresses_addressable_type_addressable_id_index');
            $table->foreign('address_type_id')->references('id')->on('address_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
