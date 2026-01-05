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
       Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->decimal('purity', 5, 2);
            $table->decimal('making_charge', 10, 2)->default(0);
            $table->string('metal')->nullable()->after('category_id');
            // $table->string('purity')->nullable()->after('metal');
            $table->decimal('weight', 10, 3)->default(0)->after('purity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
