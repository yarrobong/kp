<?php

use App\Http\Controllers\Api\ProposalApiController;

Route::prefix('v1')->middleware('api')->group(function() {
    Route::get('/proposals', [ProposalApiController::class, 'index']);
    Route::post('/proposals', [ProposalApiController::class, 'store']);
    Route::get('/proposals/{id}', [ProposalApiController::class, 'show']);
    Route::put('/proposals/{id}', [ProposalApiController::class, 'update']);
    Route::delete('/proposals/{id}', [ProposalApiController::class, 'destroy']);
});



