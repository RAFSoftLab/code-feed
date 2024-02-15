<?php

use App\Livewire\CodeFeed;
use App\Livewire\CommitFeed;
use App\Livewire\PostFeed;
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

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/repository-selector', RepositorySelector::class);

    Route::get('/{organization}/{repository}/commits', CommitFeed::class);
    Route::get('/{organization}/{repository}/commits/{hash}', ShowCommit::class);

    Route::get('/post-Feed', PostFeed::class);
    Route::get('/{organization}/{repository}/post-Feed', PostFeed::class);

    Route::get('/feed', CodeFeed::class);
});