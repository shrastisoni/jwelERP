<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('parties', function (Blueprint $table) {
        // $table->decimal('opening_balance', 15, 2)->default(0);
        $table->enum('opening_type', ['debit', 'credit'])->default('debit');
    });
}

public function down()
{
    Schema::table('parties', function (Blueprint $table) {
        // $table->dropColumn(['opening_balance', 'opening_type']);
     $table->dropColumn(['opening_type']);
        });
}

};
