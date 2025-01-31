<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckTokenExpiration;
use App\Http\Controllers\VideoController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([CheckTokenExpiration::class])->group(function () {
    Route::post('/process-video', [VideoController::class, 'processVideo']);  
});
