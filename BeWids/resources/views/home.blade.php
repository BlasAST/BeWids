@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosHome.css')
@section('contenido')
@extends('partials.header')

@section('contenidoCabecera')
              <a href="{{route('inicioSesion.index')}}">Iniciar Sesión</a>
              <a href="{{route('registro.index')}}">Registrarse</a>
              <a href="{{route('cerrarSesion')}}">Cerrar Sesión</a>
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
            <div id="eleccion">
                <button>Registrarse</button>
                <p>o</p>
                <button>Iniciar Sesion</button>
            </div>
            <div id="muestraBreve">
                <div>
                    <button>Buscador</button>
                    <button>Agenda</button>
                    <button>Contabilidad</button>
                </div>
                <div class="contenido1">
                    <h2>Mediante la recopilación de la información de varias paginas
                        ofrecemos un servicio en el que unificamos eventos o actecimientos
                        que vayan a tener lugar cerca de ti, permitiendo buscar por distintas
                        categorias y poniendolos a tu disposición para poder decidir en grupo
                        que se desea hacer
                    </h2>
                </div>
                <div class="contenido2">
                <h2>Agenda, toda la información o Planificaciones que se hayan
                    realizado estarán a vuestra disposición en un calendario
                    en el que podrás ver la información de toda las personas de la
                    sesion o de ti exclusivamente</h2>
                </div>
                <div class="contenido3">
                    <h2>
                        Permitimos que los usuarios de cada sesión lleven de una forma más organizada
                        y sencilla sus gastos y beneficios para reducir la cantidad de problemas y 
                        evitar confusiones
                    </h2>
                </div>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
@endsection


