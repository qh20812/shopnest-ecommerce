<?php

use App\Http\Controllers\Api\AttributeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Attributes endpoints
Route::prefix('attributes')->group(function () {
    Route::get('/category/{categoryId}', [AttributeController::class, 'getCategoryAttributes'])
        ->name('api.attributes.category');
    
    Route::post('/generate-variants', [AttributeController::class, 'generateVariantCombinations'])
        ->name('api.attributes.generate-variants');
});
