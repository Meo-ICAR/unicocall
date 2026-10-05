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
        Schema::table('companies', function (Blueprint $table) {
            $table
                ->enum('company_type', ['mediatore', 'call center', 'hotel', 'sw house', 'list provider'])
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table
                ->enum('company_type', ['mediatore', 'call center', 'hotel', 'sw house'])
                ->nullable()
                ->change();
        });
    }
};
