<?php

use App\Livewire\CommitFeed;
use App\Livewire\RepositorySelector;
use App\Livewire\ShowCommit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('repository-selector');
});

Route::get('commits/{organization}/{repository}', CommitFeed::class);
Route::get('commits/{hash}', ShowCommit::class);
Route::get('repository-selector', RepositorySelector::class);
