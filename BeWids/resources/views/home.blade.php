@extends('partials.base')

@section('titulo','BeWids')
@section('rutaEstilos','css/estilosHome.css')
@section('rutaJs','js/basic.js')
@section('contenido')
@include('partials.header')


<main>
    <div class="margen">
        <div class="contenedor">
            <div class="hero">
                <h2>Te ayudamos a organizarte mejor con otras personas</h2>
            </div>
            <div class="content" data-aos="fade-up">
                <h2>¿Utilidad?</h2>
                    <p>Desarrollada con la intención de que
                        tu y más personas de tu alrededor tengan una mayor organización
                    </p>
            </div>

            <div class="content" data-aos="fade-up">
                <div>
                    <h2>Planificación</h2>
                    <p>Funcionalidad base de la aplicación para organizarse mejor con
                    otras personas de la sesión</p>
                </div>
                 
            </div>
            <div class="content" data-aos="fade-up">
                <div>
                    <h2>Comunicación</h2>    
                    <p>No te quedes sin dar tu opinión, dejate escuchar y que los demás sepan
                        lo que piensas o aporta tus propias ideas</p>
                </div>
                  
            </div>
            <div class="content" data-aos="fade-up">
                <div>
                    <h2>Opiniones</h2>    
                    <p>Realizar votaciones sobre lo que se os ocurra y asi saber lo que
                    prefiere la mayoría</p>
                </div>
                  
            </div>
            <div class="content" data-aos="fade-up">
                <div>
                    <h2>Buscador</h2>
                    <p>Encuentra eventos o planes cercanos y comprueba si alguien se anima</p>
                </div>
            </div>

            <div id="eleccion" class="botonesSesion">
                <button>Registrarse</button>
                <button>Iniciar Sesión</button>
            </div>
            <div id="muestraBreve" class="content" data-aos="fade-up">
                <div class="botonesMuestra">
                    <button class="btn-buscador">Buscador de eventos</button>
                    <button class="btn-chat">Chatear con amigos</button>
                    <button class="btn-contabilidad">Llevar la contabilidad</button>
                </div>
                <div class="muestraSeleccionada">
                    <div class="buscador">
                        <h2>Mediante la recopilación de la información de varias paginas
                            ofrecemos un servicio en el que unificamos eventos o acontecimientos
                            que vayan a tener lugar cerca de ti, permitiendo buscar por distintas
                            categorias y poniendolos a tu disposición para poder decidir en grupo
                            que se desea hacer
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/buscador1.png')}}" alt="">
                        <h2>Esta aplicación, aparte de brindarte la opción de filtrar por categorías 
                            te permite saber donde tendra lugar y añadirlo a un calendario en el que se
                            muestran los distintos eventos
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/buscador2.png')}}" alt="">
                        <h2>Si no te interesa buscarlo, ¡Crea tu propio evento y compartelo con el grupo!</h2>
                        <img src="{{asset('imagenes/imagenesHome/buscador3.png')}}" alt="">
                    </div>
                    <div class="chat">
                        <h2>Ofrecemos un medio de comunicación para todos los participantes del grupo
                            
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/chat1.png')}}" alt="">
                    <h2>además, puedes crea tus propios grupos o conversaciónes privadas</h2>
                        <img src="{{asset('imagenes/imagenesHome/chat2.png')}}" alt="">
                        <h2>¡Todo a tiempo real!</h2>
                        <img src="{{asset('imagenes/imagenesHome/chat3.png')}}" alt="">
                    </div>
                    <div class="contabilidad">
                        <h2>
                            Permitimos que los usuarios de cada sesión lleven de una forma más organizada
                            y sencilla sus gastos y beneficios para reducir la cantidad de problemas y 
                            evitar confusiones
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/contabilidad1.png')}}" alt="">
                        <h2>Cuenta con la funcionalidad de ver graficamente los gastos y asi saber
                            cuanto debe cada participante
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/contabilidad2.png')}}" alt="">
                        <h2>Podrás llevar al día tus gastos y no perder la información de quien te 
                            ha pagado ya
                        </h2>
                        <img src="{{asset('imagenes/imagenesHome/contabilidad3.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="subir"><img src="{{asset('imagenes/imagenesHome/flecha-up.png')}}" alt=""></div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1000, // duración de la animación en milisegundos
        easing: 'ease-out', // tipo de transición
        once: true, // si la animación debe ocurrir solo una vez
    });
</script>

@include('partials.footer')
@endsection


