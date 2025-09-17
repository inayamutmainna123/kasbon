<?php

use App\Models\Kasbon;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KasbonController;


// Redirect ke Google
Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

// Callback dari Google
Route::get('/kasbon/{kasbon}/struk', function (Kasbon $kasbon) {
    $pdf = Pdf::loadView('kasbon.struk', compact('kasbon'))
        ->setPaper([0, 0, 226.77, 600]); // ukuran struk 80mm

    return $pdf->stream('struk-kasbon.pdf');
})->name('kasbon.struk');
// routes/web.php
use App\Http\Controllers\KasbonStrukController;


Route::get('/', function () {
    return view('welcome');
});
