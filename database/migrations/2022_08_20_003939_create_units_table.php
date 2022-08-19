<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unit_id');
            $table->string('brand');
            $table->string('model');
            $table->string('serial');
            $table->integer('count')->nullable();
            $table->foreignIdFor(Category::class)->nullable()->constrained();
            $table->foreignIdFor(Company::class)->nullable()->constrained();
            $table->foreignIdFor(Status::class)->nullable()->constrained();
            $table->foreignIdFor(User::class)->nullable()->constrained();
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
        Schema::dropIfExists('units');
    }
};
