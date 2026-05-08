<?php

use App\Livewire\Beranda;
use App\Livewire\Gallery;
use App\Livewire\PaketList;
use App\Livewire\Testimoni;
use Illuminate\Support\Facades\Route;
use Filament\Http\Controllers\Auth\LogoutController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/login', function () {
    return redirect('customer/login');
})->name('login');

Route::get('/', Beranda::class)->name('beranda');
Route::get('/testimoni', Testimoni::class)->name('testimoni');
Route::get('/gallery', Gallery::class)->name('gallery');
Route::get('/paket-list', PaketList::class)->name('paketumroh');

Route::get('/logout', LogoutController::class)->name('logout');
