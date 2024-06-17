<?php

use App\Http\Controllers\Calendario;
use App\Http\Controllers\Chat_Y_Encuestas;
use App\Http\Controllers\Contabilidad;
use App\Http\Controllers\Contrasenia;
use App\Http\Controllers\EnlaceInvitacion;
use App\Http\Controllers\EventosC;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Perfil;
use App\Http\Controllers\Sesion;
use App\Http\Controllers\Inicio;
use App\Http\Controllers\Participantes;
use App\Http\Controllers\Portal;
use App\Http\Controllers\Salir;
use Illuminate\Auth\Events\Verified;
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

//Rutas HOME
Route::get('/',[Inicio::class,'index']);
Route::get('/home', [Inicio::class,'home'])->name('casa');

// Router::get('/portal',[inicio::class,'portal']);
//Route::get('/perfil',[perfil::class,'index'])->name('perfil');

//ruta que envian los botones de cerrar sesión
Route::get('/cuenta/cerrar',[Sesion::class, 'cerrar'])->name('cerrarS');
//ruta que envian los botones de iniciar sesión y registrar, indicando en {dir} cual es el caso
Route::get('/cuenta/{dir}',[Sesion::class,'comprobar'])->name('sesion');
//ruta que envia el pulsar el icono de perfil
Route::get('/cuenta',[Sesion::class,'comprobar']);
//ruta que se envia al enviar un formulario
Route::post('/cuenta',[Sesion::class,'formulario'])->name('sesionF');



// Ruta para obtener información
Route::get('/perfil',[Perfil::class,'index'])->middleware(['autenticar','verified'])->name('perfil');
// ruta POST que introduce los datos en tabla infousuarios;
Route::post('/guardar',[Perfil::class,'guardarDatos'])->middleware(['autenticar','verified'])->name('guardar');
Route::post('/perfil',[Perfil::class, 'crearPortal'])->middleware(['autenticar','verified'])->name('crearP');
Route::get('/profile/photo/{nombreFoto}', [Perfil::class, 'pedirFoto'])->middleware(['autenticar','verified'])->name('profile.photo');


Route::get('/portal',[Portal::class, 'index'])->middleware(['autenticar','verified','portal'])->name('portal');
Route::post('/abrirPortal',[Portal::class, 'irPortal'])->middleware(['autenticar','verified'])->name('abrirPortal');
Route::get('/cambiarConf', [Portal::class, 'cambiarConf'])->middleware(['autenticar','verified','portal']);
Route::post('/personalizar',[Portal::class, 'cambiarFondo'])->middleware(['autenticar','verified','portal']);
Route::get('/portal/foto/{foto}', [portal::class, 'pedirFoto'])->middleware(['autenticar','verified','portal'])->name('foto.fondo');


Route::get('/contabilidad',[Contabilidad::class, 'index'])->middleware(['autenticar','verified','portal','contabilidad']);
Route::post('/contabilidad',[Contabilidad::class, 'aniadirGasto'])->middleware(['autenticar','verified','portal'])->name('aniadirGasto');
Route::post('/solicitarReembolso',[Contabilidad::class, 'solicitarReembolso'])->middleware(['autenticar','verified','portal'])->name('reembolso');
Route::post('/responderNotificacion',[Contabilidad::class, 'ResponderNotificacion'])->middleware(['autenticar','verified','portal'])->name('responderNot');

// Route::get('/iniciar',[InicioSesion::class,'mostrar'])->name('inicioSesion.index');
// Route::get('/registrarse',[Registrarse::class,'mostrar'])->name('registro.index');
// Route::post('/iniciar',[InicioSesion::class,'iniciar']);
// Route::post('/registrarse',[Registrarse::class,'crear']);

// Route::get('/chat',ChatYEncuestas::class,'index')->name('chat');

// Rutas Livewire
Route::get('/chat',[Chat_Y_Encuestas::class, 'index'])->middleware(['autenticar','verified','portal'])->name('chat');
Route::get('/encuestas',[Chat_Y_Encuestas::class, 'index'])->middleware(['autenticar','verified','portal'])->name('encuestas');
Route::post('/nuevaEncuesta',[Chat_Y_Encuestas::class, 'newEncuesta'])->middleware(['autenticar','verified','portal'])->name('newEncuesta');
Route::get('/pedirDatos',[Chat_Y_Encuestas::class,'pedirDatos'])->middleware(['autenticar','verified','portal']);
Route::get('/chat/photo/{id}', [Chat_Y_Encuestas::class, 'pedirFoto'])->middleware(['autenticar','verified','portal'])->name('foto.mensaje');
Route::get('/updateEncuesta',[Chat_Y_Encuestas::class,'updateEncuestas'])->middleware(['autenticar','verified','portal']);



Route::get('/eventos',[EventosC::class,'index'])->middleware(['autenticar','verified','portal']);
Route::get('/buscarEventos',[EventosC::class,'mostrarEventos'])->middleware(['autenticar','verified','portal']);
Route::get('/buscador',[EventosC::class,'buscador'])->middleware(['autenticar','verified','portal']);
Route::get('/aniadir',[EventosC::class, 'aniadirEvento'])->middleware(['autenticar','verified','portal']);
Route::get('/eliminarEvt',[EventosC::class, 'eliminar'])->middleware(['autenticar','verified','portal']);



// Ruta invitación
Route::get('/crearEnlace',[EnlaceInvitacion::class,'crearEnlace'])->middleware('autenticar')->middleware(['autenticar','verified','portal']);
Route::get('/invitacion/{dir}',[EnlaceInvitacion::class,'redirigir'])->middleware(['autenticar','verified']);
Route::get('/aniadirPar',[EnlaceInvitacion::class,'aniadirParticipante'])->middleware(['autenticar','verified']);


//Ruta Calendario
Route::get('/calendario',[Calendario::class,'mostrar'])->middleware(['autenticar','verified','portal']);
Route::get('/cambiarCal',[Calendario::class,'cambiarMes'])->middleware(['autenticar','verified','portal']);
Route::post('/aniadirCal',[Calendario::class,'aniadirEvento'])->middleware(['autenticar','verified','portal']);
Route::get('/cambiarFecha',[Calendario::class, 'cambiarFechaEvt'])->middleware(['autenticar','verified','portal']);
Route::get('/pedirEvt',[Calendario::class, 'pedirEvt'])->middleware(['autenticar','verified','portal']);
Route::get('/retirarCal',[Calendario::class, 'retirarCal'])->middleware(['autenticar','verified','portal']);


//Rutas Participantes
Route::get('/participantes',[Participantes::class, 'index'])->middleware(['autenticar','verified','portal']);
Route::post('/crearParticipante',[Participantes::class, 'crearParticipante'])->middleware(['autenticar','verified','portal']);
Route::get('/desvincularPart',[Participantes::class, 'desvincular'])->middleware(['autenticar','verified','portal']);
Route::get('/ascenderPart',[Participantes::class, 'ascender'])->middleware(['autenticar','verified','portal']);
Route::get('/eliminarPart',[Participantes::class, 'eliminar'])->middleware(['autenticar','verified','portal']);
Route::get('/comprobarCuentas',[Participantes::class, 'comprobar'])->middleware(['autenticar','verified','portal']);









Route::get('/email/verify', [Sesion::class,'enviarCorreo'])->middleware('autenticar')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [Sesion::class,'codigoRecibido'])->middleware('autenticar')->middleware('signed')->name('verification.verify');
Route::post('/email/resend',[Sesion::class,'reenviar'])->middleware(['autenticar', 'throttle:6,1'])->name('verification.resend');

Route::get('/password/forgot', [Contrasenia::class, 'requestForm']);
Route::post('/password/email', [Contrasenia::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [Contrasenia::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [Contrasenia::class, 'reset'])->name('password.update');






Route::post('/salir',[Salir::class,'guardarPantalla'])->middleware(['autenticar','verified','portal']);




