<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fees_ranges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')->constrained('businesses');

            $table->decimal('min', 11, 2);
            $table->decimal('max', 11, 2);

            $table->decimal('fee', 11, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fees_ranges');
    }
}
