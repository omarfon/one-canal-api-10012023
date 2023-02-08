<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('account_id')->constrained('accounts');
            $table->decimal('amount', 11, 2);
            $table->decimal('transfer_amount', 11, 2);
            $table->decimal('fees_amount', 11, 2);
            $table->string('period_name', 50);
            $table->foreignId('reason_id')->constrained('reasons');
            $table->json('logs');
            $table->string('status')->default('REGISTERED');
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
        Schema::dropIfExists('salary_advances');
    }
}
