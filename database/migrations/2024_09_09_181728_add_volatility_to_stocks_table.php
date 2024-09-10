<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('volatility', 8, 2)->default(0)->after('price');  // Add volatility field
        });
    }
    
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('volatility');
        });
    }
    
};
