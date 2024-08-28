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
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('CASCADE');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_product', function (Blueprint $table) {
            $table->dropForeign('document_product_document_id_foreign');
            $table->dropForeign('document_product_product_id_foreign');
        });
    }
};
