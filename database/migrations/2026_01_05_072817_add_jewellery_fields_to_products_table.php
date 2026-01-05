<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('products', function (Blueprint $table) {
        //     $table->string('metal')->nullable()->after('category_id');
        //     $table->string('purity')->nullable()->after('metal');
        //     $table->decimal('weight', 10, 3)->default(0)->after('purity');
        // });
    }

    public function down(): void
    {
        // Schema::table('products', function (Blueprint $table) {
        //     // $table->dropColumn(['metal', 'purity', 'weight']);
        //     $table->dropColumn(['weight']);
        // });
    }
};
