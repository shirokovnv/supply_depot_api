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
        Schema::table('document_product', function (Blueprint $table) {
            $table->unsignedInteger('remains');
            $table->float('remains_cash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_product', function (Blueprint $table) {
            $table->dropColumn('remains');
            $table->dropColumn('remains_cash');
        });
    }
};
