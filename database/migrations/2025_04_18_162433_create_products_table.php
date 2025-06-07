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
            $table->string('nafaka'); //nafaka
            $table->float('kiwango_cha_unit'); //kiwango_cha_unit
            $table->float('kilo_kwa_unit');//kilo_kwa_unit
            $table->float('bei_ya_tanesco');//bei_ya_tanesco
            $table->float('bei_ya_mashineni');//bei_ya_mashineni
            $table->float('kiwango_cha_kapi');//kiwango_cha_kapi
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
