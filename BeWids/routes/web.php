<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Registrarse;
use App\Http\Controllers\InicioSesion;
use App\Http\Controllers\perfil;
use App\Http\Controllers\sesion;
use App\Http\Controllers\inicio;
use Illuminate\Support\Facades\Auth;

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

Route::get('/',[inicio::class,'index'])->name('base');
// Router::get('/portal',[inicio::class,'portal']);
//Route::get('/perfil',[perfil::class,'index'])->name('perfil');

//ruta que envian los botones de cerrar sesión
Route::get('/cuenta/cerrar',[sesion::class, 'cerrar'])->name('cerrarS');

Route::get('/home', [inicio::class,'home'])->name('casa');

//ruta que envian los botones de iniciar sesión y registrar, indicando en {dir} cual es el caso
Route::get('/cuenta/{dir}',[sesion::class,'comprobar'])->name('sesion');

//ruta que envia el pulsar el icono de perfil
Route::get('/cuenta',[sesion::class,'comprobar']);
// Ruta para obtener información
Route::get('/perfil',[perfil::class,'index'])->name('perfil');

// ruta POST que introduce los datos en tabla infousuarios;
Route::post('/guardar',[perfil::class,'guardarDatos'])->name('guardar');


//ruta que se envia al enviar un formulario
Route::post('/cuenta',[sesion::class,'formulario'])->name('sesionF');

// Route::get('/iniciar',[InicioSesion::class,'mostrar'])->name('inicioSesion.index');
// Route::get('/registrarse',[Registrarse::class,'mostrar'])->name('registro.index');
// Route::post('/iniciar',[InicioSesion::class,'iniciar']);
// Route::post('/registrarse',[Registrarse::class,'crear']);