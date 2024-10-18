<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payer');
            $table->unsignedBigInteger('payee');
            $table->unsignedBigInteger('wallet_payer');
            $table->unsignedBigInteger('wallet_payee');
            $table->decimal('value', 19, 2);
            $table->enum('situation', ['P', 'E', 'F'])->default('P')->comment('Pending Error Finish');
            $table->boolean('error')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('payer')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('payee')->references('id')->on('users')->onDelete('restrict');

            $table->foreign('wallet_payer')->references('id')->on('wallets')->onDelete('restrict');
            $table->foreign('wallet_payee')->references('id')->on('wallets')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
