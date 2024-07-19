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
        Schema::create('medicine_outgoings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicine_id')->index();
            $table->unsignedBigInteger('unit_id')->index()->nullable();
            $table->string('batch_no', 60);
            $table->date('exp_date');
            $table->integer('quantity');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_outgoings');
    }
};
