<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Registrarse;
use App\Http\Controllers\InicioSesion;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',function(){
    return view('home');
});

Route::get('/iniciar',[InicioSesion::class,'crear'])->name('inicioSesion.index');
Route::get('/registrarse',[Registrarse::class,'crear'])->name('registro.index');