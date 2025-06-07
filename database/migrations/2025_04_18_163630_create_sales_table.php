<?php
use App\Models\Product;
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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nafaka'); //nafaka
            $table->float('kilo_za_mteja'); //kilo_za_mteja
            $table->float('unit_zilizotumika');//Unit
            $table->float('kapi_zilizo_patikana');//kapi
            $table->float('anataka_kulipa');//anataka_kulipa
            $table->float('kapi_alizoacha_mteja');//kapi_alizoacha_mteja
            $table->float('malipo_ya_mteja');//malipo
            $table->float('faida');//faida
            $table->foreignIdFor(Product::class);
        });
    }
/**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
