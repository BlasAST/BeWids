@extends('partials.base')
@section('rutaEstilos','../css/estilosSesion.css')
@section('rutaJs','../js/sesion.js')

@section('contenido')
@extends('partials.header')

    @if(!Auth::check())
        <div class="botones @if(!$dir) mostrar @endif">
            <h1>NUEVO EN BEWIDS?!</h1>
            <p>Inicia sesión o crea una cuenta BeWids para poder disfrutar de nuestras funcionalidades y empezar a organizarte. A que esperas!</p>
            <button class="botonIniciar">INICIAR SESIÓN</button>
            <button class="botonCrear">CREAR CUENTA</button>
        </div>

        <div class="inicio @if($dir == 'iniciar') mostrar @endif">
                <h1>INICIAR SESIÓN</h1>
                <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
                <form action="{{route('sesionF')}}" method="POST">
                    @csrf
                    <input type="hidden" value="iniciar" name="tipo">
                    <div>
                        <label for="email">Email</label>
                        <div class="contInput">
                            <input type="email" name="email" placeholder="Indica tu correo electrónico">
                            <div class="borde"></div>
                        </div>
                    </div>
                    <div>
                        <label for="password">Contraseña</label>
                        <div class="contInput">
                            <input class="pass" type="password" name="password" placeholder="Indica tu contraseña">
                            <div class="borde">
                                <figure class="ojo"></figure>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <input type="checkbox" name="recordar">
                            <label for="recordar" name="recordar">Recordar sesión</label>
                        </div>
                        <p><a href="">He olvidado mi contraseña</a></p>
                        <p>¿No tienes cuenta? <a href="" class="botonCrear">Registrate</a></p>
                    </div>
                    <p class="error"></p>
                    @error('message')
                        <p class="error">{{$message}}</p>
                    @endError
                    <input type="submit" name="inicio" value="INICIAR SESIÓN">
                    <p><a href="{{url()->previous()}}">volver</a> a la pagina anterior</p>
                </form>
        </div>
      

        <div class="crear @if($dir == 'registrar') mostrar @endif">
            <h1>CREAR CUENTA</h1>
            <p>Crea una cuenta con nosotros para descubrir los servicios que ofrecemos</p>
            <form action="{{route('sesionF')}}" method="POST">
                @csrf
                <input type="hidden" value="crear" name="tipo">
                <div>
                    <label for="name">Usuario</label>
                    <div class="contInput">
                        <input type="text" name="name" placeholder="Indica tu usuario">
                        <div class="borde"></div>
                    </div>
                </div>
                <div>
                    <label for="email">Email</label>
                    <div class="contInput">
                        <input type="email" name="email" placeholder="Indica tu correo electrónico">
                        <div class="borde"></div>
                    </div>
                </div>
                <div>
                    <label for="email2">Repetir Email</label>
                    <div class="contInput">
                        <input type="email" name="email2" placeholder="Repite tu correo electrónico">
                        <div class="borde"></div>
                    </div>
                </div>
                <div>
                    <label for="password">Contraseña</label>
                    <div class="contInput">
                        <input class="pass" type="password" name="password" placeholder="Indica tu contraseña">
                        <div class="borde">
                            <figure class="ojo"></figure>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="pass2">Repetir Contraseña</label>
                    <div class="contInput">
                        <input class="pass" type="password" name="pass2" placeholder="Tepite tu contraseña">
                        <div class="borde">
                            <figure class="ojo"></figure>
                        </div>
                    </div>
                    <p><a href="" class="botonIniciar">¿Ya tienes cuenta?</a></p>
                </div>
                <p class="error"></p>
                <input type="submit" name="registro" value="CREAR CUENTA">
            </form>
        </div>
    @else
        <div class="cerrar">
            <h1>Ya estás loggeado</h1>
            <h3>Para iniciar sesión cierra la sesión actual antes</h2>
            <button class="cerrarSesion">CERRAR SESIÓN</button>
            <p><a href="{{url()->previous()}}">volver</a> a la pagina anterior</p>
        </div>
        
    @endif
        
    
    {{-- <div class="inicio">
        <h1>INICIAR SESIÓN</h1>
        <p>Si ya tienes una cuenta con nosotros, indica tus credenciales y accede a tus sesiones</p>
        <form action="" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="pass">Contraseña</label>
            <input type="password" name="pass" placeholder="Indica tu contraseña">
            <a href="">He olvidado mi contraseña</a>
            <p>¿No tienes cuenta? <a href="">Registrate</a></p>
            <input type="submit" name="inicio" value="INICIAR SESIÓN">
        </form>
    </div>
    <div class="registro">
        <h1>CREAR CUENTA</h1>
        <p>Crea una cuenta con nosotros para descubrir los servicios que ofrecemos</p>
        <form action="" method="POST">
            <label for="user">Usuario</label>
            <input type="text" name="user" placeholder="Indica tu usuario">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Indica tu correo electrónico">
            <label for="email2">Repetir Email</label>
            <input type="email" name="email2" placeholder="Repite tu correo electrónico">
            <label for="pass">Contraseña</label>
            <input type="password" name="pass" placeholder="Indica tu contraseña">
            <label for="pass2">Repetir Contraseña</label>
            <input type="password" name="pass2" placeholder="Tepite tu contraseña">
            <a href="">¿Ya tienes cuenta?</a>
            <input type="submit" name="registro" value="CREAR CUENTA">
        </form>
    </div> --}}
    @endsection