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
        Schema::create('medicine_incomings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medicine');
            $table->string('batch_no', 60);
            $table->date('date');
            $table->date('exp_date');
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_incomings');
    }
};
