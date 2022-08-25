<?php

use App\Models\Specification;
use App\Models\Unit;
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
    public function up(): void
    {
        Schema::create('spec_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('details')->nullable();
            $table->foreignIdFor(Specification::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Unit::class)->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['specification_id', 'unit_id'], 'unique_spec_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_details');
    }
};
