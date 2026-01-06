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
          Schema::table('stock_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_ledgers', 'rate')) {
                $table->decimal('rate', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('stock_ledgers', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
