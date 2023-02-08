<?php

use App\Constants\Role;
use App\Constants\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->enum('document_type', User::$document_type)->nullable();
            $table->string('document_number')->nullable();

            $table->string('names');
            $table->string('surnames');
            $table->string('email');

            $table->enum('role', Role::$role)->nullable();

            $table->foreignId('business_id')->nullable()->constrained('businesses');

            $table->boolean('active')->default(true);
            $table->string('password')->nullable();

            $table->tinyInteger('attemps')->default(3);

            $table->decimal('salary', 11, 2)->nullable();

            $table->boolean('valid')->default(false);
            $table->timestamp('validated_at')->nullable();

            $table->boolean('salary_view')->default(true);

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
        Schema::dropIfExists('users');
    }
}
