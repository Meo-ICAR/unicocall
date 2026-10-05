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
        Schema::create('subappaltis', function (Blueprint $table) {
            $table->id();
            $table->string('originator_type')->comment('Type of originator (company, client)');
            $table->string('originator_id')->comment('ID of the originator');
            $table->string('sub_type')->comment('Type of sub-contractor (client, employee, software)');

            $table->string('sub_id')->comment('ID of the sub-contractor');

            $table->string('name')->comment('Name/description of the subappalto');
            $table->string('role')->comment('Role in the subappalto');
            $table->string('servizio')->comment('Service provided');
            $table->text('categoria_dati')->comment('Data categories handled (JSON)');
            $table->text('istruzioni')->comment('Operational instructions');
            $table->string('nomina')->comment('Nomination type (DPO, etc.)');
            $table->string('nomina_at')->comment('Nomination date');
            $table->string('description')->comment('Detailed description');
            $table->char('company_id', 36)->comment('Company UUID foreign key');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subappaltis');
    }
};
