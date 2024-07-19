<?php

use App\Http\Controllers\MedicineOutgoingController;

Route::prefix("{lang}")->group(function(){
    Route::prefix("medicine-outgoing")->group(function() {
        Route::get("/", [MedicineOutgoingController::class, 'index']);
        Route::get("{id}", [MedicineOutgoingController::class, 'show']);
        Route::post("create", [MedicineOutgoingController::class, 'create']);
    });
});
