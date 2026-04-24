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
        Schema::create('suppression_lists', function (Blueprint $table) {
            $table->comment('Registro globale di opt-out e blocchi comunicazioni. Implementa il principio di Privacy by Design.');
            $table->id();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->enum('identifier_type', ['email', 'phone'])->default('email');
            $table->date('request_date');
            $table->boolean('do_not_contact')->default(true);
            $table->string('hashed_identifier', 255)->comment("Hash SHA-256 (non decriptabile) dell'email o telefono per evitare di conservare il dato in chiaro in blacklist");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppression_lists');
    }
};
