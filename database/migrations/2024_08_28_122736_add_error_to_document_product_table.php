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
            $table->integer('inv_error')->nullable();
            $table->float('inv_error_cash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_product', function (Blueprint $table) {
            $table->dropColumn('inv_error');
            $table->dropColumn('inv_error_cash');
        });
    }
};
