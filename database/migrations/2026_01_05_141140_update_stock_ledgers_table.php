<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {

            /* rename existing column */
            if (Schema::hasColumn('stock_ledgers', 'transaction_type')) {
                $table->renameColumn('transaction_type', 'type');
            }

            /* new reference */
            if (!Schema::hasColumn('stock_ledgers', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('type');
            }

            /* quantities */
            if (!Schema::hasColumn('stock_ledgers', 'qty_in')) {
                $table->integer('qty_in')->default(0)->after('reference_id');
            }

            if (!Schema::hasColumn('stock_ledgers', 'qty_out')) {
                $table->integer('qty_out')->default(0)->after('qty_in');
            }

            /* balances */
            if (!Schema::hasColumn('stock_ledgers', 'balance_qty')) {
                $table->integer('balance_qty')->default(0)->after('weight_out');
            }

            if (!Schema::hasColumn('stock_ledgers', 'balance_weight')) {
                $table->decimal('balance_weight', 10, 3)->default(0)->after('balance_qty');
            }

            /* timestamps */
            if (!Schema::hasColumn('stock_ledgers', 'created_at')) {
                $table->timestamps();
            }

            /* old balance column cleanup */
            if (Schema::hasColumn('stock_ledgers', 'balance')) {
                $table->dropColumn('balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_ledgers', function (Blueprint $table) {
            // rollback intentionally omitted (ledger data is critical)
        });
    }
};
