<?php

use App\Livewire\CodeFeed;
use App\Livewire\Commits;
use App\Livewire\GitHub\GithubRepositories;
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

require __DIR__.'/auth.php';

Route::get('/github/oauth', \App\Http\Controllers\GitHub\OAuth::class);
Route::get('/github/auth-success', GithubRepositories::class);
Route::view('/admin', 'profile')
    ->middleware('auth:admin');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::view('/repository-selector', 'repositories')
        ->name('repository-selector');

    Route::view('feed', 'feed')
        ->name('feed');

    Route::view('/{organization}/{repository}/commits', 'commits')
        ->name('commits');
    Route::view('/{organization}/{repository}/commits/{hash}', 'show-commit')
        ->name('show-commit');

    Route::view('/{organization}/{repository}/post-feed', 'post-feed')
        ->name('post-feed');
});