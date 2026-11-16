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
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->string('asaas_cliente_id')->nullable();
            $table->string('asaas_cobranca_id')->nullable()->unique();
            $table->string('link_pagamento')->nullable();
            $table->timestamp('pago_em')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscricoes', function (Blueprint $table) {
            $table->dropColumn([
                'asaas_cliente_id', 'asaas_cobranca_id', 'link_pagamento', 'pago_em',
            ]);
        });
    }
};
