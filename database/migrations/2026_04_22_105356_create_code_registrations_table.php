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
        Schema::create('code_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Code da usare in registrations');
            $table->string('name')->nullable()->comment('Descrizione del codice');
            $table->boolean('is_mandatory')->default(true)->comment('Indica se il codice è obbligatorio');
            $table->string('codeable_type')->default('App\Models\Client')->comment('Tipo di entità a cui si applica il codice (registration, client, etc.)');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_mandatory', 'code_registrations_mandatory_index');
            $table->index('name', 'code_registrations_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_registrations');
    }
};
