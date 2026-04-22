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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('branch_type')->nullable();
            $table->string('branch_id')->nullable();
            $table->boolean('is_main_office')->default(0)->comment('Sede Legale/Operativa principale');
            $table->string('manager_first_name', 100)->nullable();
            $table->string('manager_last_name', 100)->nullable();
            $table->string('manager_tax_code', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
