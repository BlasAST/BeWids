<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Registrarse;
use App\Http\Controllers\InicioSesion;
use App\Http\Controllers\sesion;
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

Route::get('/perfil/{$dir}',[sesion::class,'comprobar'])->name('perfil');

Route::get('/iniciar',[InicioSesion::class,'mostrar'])->name('inicioSesion.index');
Route::get('/registrarse',[Registrarse::class,'mostrar'])->name('registro.index');
Route::post('/iniciar',[InicioSesion::class,'iniciar']);
Route::post('/registrarse',[Registrarse::class,'crear']);