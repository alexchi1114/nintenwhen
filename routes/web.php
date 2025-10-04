<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FranchiseController;
use App\Http\Controllers\HomeController;

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

Route::get('/', [HomeController::class, 'index']);

Route::get('/franchise', [FranchiseController::class, 'index']);
Route::get('/franchise/{id}', [FranchiseController::class, 'show']);
Route::post('/franchise/search', [FranchiseController::class, 'search']);
Route::get('/franchise-analysis-cached', [FranchiseController::class, 'getCachedFranchiseAnalysis'])->name('franchise.analysis.cached');
Route::get('/franchise-analysis-streamed', [FranchiseController::class, 'streamFranchiseAnalysis'])->name('franchise.analysis.streamed');
Route::get('/franchise-analysis', [FranchiseController::class, 'getFranchiseAnalysis'])->name('franchise.analysis');
Route::get('/franchise-analysis-byid/{id}', [FranchiseController::class, 'getFranchiseAnalysisById'])->name('franchise.analysis.byid');


Route::get('/games', function(){
	return view('game.games');
});

