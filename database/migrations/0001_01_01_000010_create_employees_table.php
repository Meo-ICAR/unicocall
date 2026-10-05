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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('role', 100)->nullable();
            $table->string('cf')->nullable();
            $table->string('phone')->nullable();
            $table->string('department', 100)->nullable();
            $table->date('hiring_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->foreignId('company_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('coordinated_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employee_types')->default('dipendente')->comment('Tipologia contrattuale');
            $table->string('supervisor_type')->default('no');
            $table->string('privacy_role')->nullable();
            $table->text('purpose')->nullable();
            $table->text('data_subjects')->nullable();
            $table->text('data_categories')->nullable();
            $table->string('retention_period')->nullable();
            $table->string('extra_eu_transfer')->nullable();
            $table->text('security_measures')->nullable();
            $table->string('privacy_data')->nullable();
            $table->boolean('is_structure')->default(0);
            $table->boolean('is_ghost')->default(0)->comment('Utenza tecnica di sistema');
            $table->string('employee_type')->nullable()->comment('Classe del Modello collegato');
            $table->string('employee_id')->nullable()->comment('ID del Modello');
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
        Schema::dropIfExists('employees');
    }
};
