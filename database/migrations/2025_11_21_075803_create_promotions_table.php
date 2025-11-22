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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->default('Заголовок акции');
            $table->text('text')->nullable();
            $table->unsignedTinyInteger('sale_percent')->default(1);
            $table->dateTime('date_start');
            $table->dateTime('date_end');

            $table->index(['date_end', 'sale_percent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
