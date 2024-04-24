@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosHome.css')
@section('contenido')
@extends('partials.header')

@section('contenidoCabecera')
              <a href="{{route('inicioSesion.index')}}">Iniciar Sesión</a>
              <a href="{{route('registro.index')}}">Registrarse</a>
              <br>
              <a href="{{route('sesion', 'botones')}}">Perfil</a>
              <a href="{{route('sesion', 'inicio')}}">Iniciar</a>
              <a href="{{route('sesion', 'crear')}}">Crear Cuentar</a>
@endsection

<main>
    <div class="margen">
        <div class="contenedor">
            <div class="hero">
                <h2>Te ayudamos a organizarte mejor con otras personas</h2>
            </div>
            <div>
                <h2>¿Utilidad?</h2>
                    <p>Desarrollada con la intención de que
                        tu y más personas de tu alrededor tengan una mayor organización
                    </p>
            </div>

            <div>
                <div>
                    <h2>Planificación</h2>
                    <p>Funcionalidad base de la aplicación para organizarse mejor con
                    otras personas de la sesión</p>
                </div>
                 
                <!-- <h2>Planificate mejor con tus personas cercanas</h2> -->
            </div>
            <div>
                <div>
                    <h2>Chat</h2>    
                    <p>No te quedes sin dar tu opinión, dejate escuchar y que los demás sepan
                        lo que piensas o aporta tus propias ideas</p>
                </div>
                  
            </div>
            <div>
                <div>
                    <h2>Encuestas</h2>    
                    <p>Realizar votaciones sobre lo que se os ocurra y asi saber lo que
                    prefiere la mayoría</p>
                </div>
                  
            </div>
            <div>
                <div>
                    <h2>Buscador</h2>
                    <p>Encuentra eventos o planes cercanos y comprueba si alguien se anima</p>
                </div>
            </div>
        </div>
    </div>
</main>


@include('partials.footer')
@endsection


