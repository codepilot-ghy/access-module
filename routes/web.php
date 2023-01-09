<?php

use App\Http\Controllers\CRMCtlr;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['module.access'])->group(function () {

    Route::any('/', [CRMCtlr::class, 'index']);

    Route::prefix('crm')->group(function () {

        Route::any('/', [CRMCtlr::class, 'index']);
        Route::any('project', [CRMCtlr::class, 'index']);
    
    });

});
