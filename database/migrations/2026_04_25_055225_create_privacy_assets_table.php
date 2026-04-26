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
        Schema::create('privacy_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('asset_name');  // Es: Server DB Produzione, Laptop Sviluppatori
            $table->enum('type', ['hardware', 'software', 'cloud_service', 'paper_archive']);
            $table->string('owner');  // Chi ne è responsabile
            $table->string('location');  // Es: Data Center Milano, Ufficio Roma
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('privacy_assets');
    }
};
