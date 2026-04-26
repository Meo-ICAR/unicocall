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
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('UUID v4 generato da Laravel');
            $table->string('name')->comment("Ragione Sociale dell'azienda");
            $table->string('owner')->comment('Titolare azienda');
            $table->string('vat_number', 13)->nullable();
            $table->string('sponsor')->nullable();
            $table->enum('company_type', ['mediatore', 'call center', 'hotel', 'sw house'])->nullable();
            $table->boolean('is_iso27001_certified')->default(0)->comment('Certificazione ISO 27001');
            $table->string('contact_email')->nullable()->comment('Email contatto');
            $table->string('dpo_email')->nullable()->comment('Email DPO');
            $table->text('page_header')->nullable()->comment('Header stampa su carta intestata');
            $table->text('page_footer')->nullable()->comment('Footer stampa su carta intestata');
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->boolean('smtp_enabled')->default(0);
            $table->boolean('smtp_verify_ssl')->default(1);
            $table->integer('payment_frequency')->nullable();
            $table->decimal('payment', 10, 2)->nullable();
            $table->timestamp('payment_last_date')->nullable();
            $table->decimal('payment_startup', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
