<div class="contentedorMensajes grow flex flex-col">
    @if ($participanteSeleccionado)
        <header class="flex basis-1 items-center relative bg-colorDetallesTras">
            <figure class=" w-8 mr-auto ml-4"><img src="{{ route('foto.mensaje', ['id' => $participanteSeleccionado->id_usuario]) }}" class="rounded-full" alt="">
            </figure>
            <h1 class="mr-auto font-bold">{{ $participanteSeleccionado->nombre_en_portal }}</h1>
            <button class="mostrarListaParticipantes flex items-center" wire:click="$dispatch('toggleParticipantesList')">
                <p class="mr-4 font-light">Menu</p>
                <figure class="w-5 mr-4 flecha">
                    <img src="{{ asset('imagenes/imagenesBasic/flechaAperturaInfo.png') }}">
                </figure>
            </button>

            <div class="bg-colorMain  participantesList hidden absolute top-7 right-0 p-4 flex flex-col ">
                <button wire:click="buscarInfoParticipantes('{{ $participanteSeleccionado->nombre_en_portal }}')"
                    class="hover:bg-colorFondo">
                    <p>Mostrar información</p>
                </button>
                <button wire:click="cerrarConversacion" class="cerraduras hover:bg-colorFondo"> Cerrar conversación</button>

            </div>
            @if ($inforParticipante)
                <div class="bg-colorMain absolute top-12 right-[30%] w-[50%] p-10 mostrarInfo">
                    <figure wire:click="cerrarInfo"><img src="{{ asset('imagenes/imagenesBasic/cancel.svg') }}"
                            alt=""></figure>
                    <h4><span class="text-blue-700">Nombre de
                            usuario:</span><span>{{ $inforParticipante->nombre }}</span></h4>
                    <h4><span class="text-blue-700">Nombre en el
                            portal:</span><span>{{ $participanteSeleccionado->nombre_en_portal }}</span></h4>
                    <h4><span
                            class="text-blue-700">Descripción:</span><span>{{ $inforParticipante->descripcion }}</span>
                    </h4>
                    <h4><span class="text-blue-700">Numero de
                            contacto:</span><span>{{ $inforParticipante->numero_contacto }}</span></h4>
                    <h4><span class="text-blue-700">Provincia:</span><span>{{ $inforParticipante->provincia }}</span>
                    </h4>
                    <h4><span class="text-blue-700"></span><span></span>

                </div>
            @endif



        </header>
    @endif
    @if ($participantesSeleccionados)
        <header class="flex basis-1 items-center relative justify-between px-5 bg-colorDetallesTras">
            <h1 class="font-bold">{{ $conversacionSeleccionada->name_group }}</h1>
            <button class="mostrarListaParticipantes flex items-center"
                wire:click="$dispatch('toggleParticipantesList')">
                <p class="mr-4 font-light">Menu</p>
                <figure class="w-5 mr-4 flecha">
                    <img src="{{ asset('imagenes/imagenesBasic/flechaAperturaInfo.png') }}">
                </figure>
            </button>

            <ul class="hidden participantesList absolute right-0 top-6 p-2 bg-colorMain flex flex-col justify-center">
                @foreach ($participantesSeleccionados as $participantee)
                    <li class="flex justify-center items-center hover:bg-colorFondo"
                        wire:click="buscarInfoParticipantes('{{ $participantee }}')">
                        <p class="w-[50%]">{{ $participantee }}</p>
                        <figure class="w-5 mr-4 flecha">
                            <img src="{{ asset('imagenes/imagenesBasic/flechaAperturaInfo.png') }}">
                        </figure>
                    </li>
                @endforeach
                <button wire:click="cerrarConversacion" class="cerraduras hover:bg-colorFondo">Cerrar conversación</button>
            </ul>
            @if ($inforParticipante)
                <div class="bg-colorMain absolute top-12 right-[42%] w-[50%] p-10  mostrarInfo">
                    <figure wire:click="cerrarInfo"><img src="{{ asset('imagenes/imagenesBasic/cancel.svg') }}"
                            alt=""></figure>
                    <h4><span class="text-blue-700">Nombre de
                            usuario:</span><span>{{ $inforParticipante->nombre }}</span></h4>
                    <h4><span
                            class="text-blue-700">Descripción:</span><span>{{ $inforParticipante->descripcion }}</span>
                    </h4>
                    <h4><span class="text-blue-700">Numero de
                            contacto:</span><span>{{ $inforParticipante->numero_contacto }}</span></h4>
                    <h4><span class="text-blue-700">Provincia:</span><span>{{ $inforParticipante->provincia }}</span>
                    </h4>
                    <h4><span class="text-blue-700"></span><span></span></h4>

                </div>
            @endif


        </header>
    @endif
    @if ($participanteSeleccionado || $participantesSeleccionados)
        <main class="containerMessages grow bg-slate-800 overflow-y-scroll flex flex-col origin-bottom">

            @if ($mensajes->body != null)
                @foreach (json_decode($mensajes->body) as $mensaje)
                    @if ($mensaje->emisor != $participanteActual->nombre_en_portal)
                        <div class="other bg-white w-[20rem] ml-5 rounded-lg my-3 p-3">
                            <p class="break-words whitespace-normal">{{ $mensaje->emisor }}</p>
                            <div class="break-words whitespace-normal">{{ $mensaje->mensaje }}</div>
                            <p>Enviado a las: {{$mensaje->timestamp}}</p>
                        </div>
                    @endif


                    @if ($mensaje->emisor == $participanteActual->nombre_en_portal)
                        <div class="you w-[20rem] self-end bg-blue-600 text-white mr-5 rounded-lg my-3 p-3 ">
                            <p class="text-right break-words whitespace-normal">Tu</p>
                            <div class="break-words whitespace-normal">{{ $mensaje->mensaje }}</div>
                            <p>Enviado a las: {{$mensaje->timestamp}}</p>
                        </div>
                    @endif
                @endforeach
            @else
                <h1 class="mt-8 text-center text-2xl text-white">Nueva conversación creada</h1>
            @endif
        </main>
        <footer class="sendMessage bg-blue-800 h-[10%] rounded-br-2xl">
            <form class="flex h-full" wire:submit.prevent="enviarMensaje">
                <input type="text" wire:model="mensajeEnviado" placeholder="Escribir mensaje"
                    class="grow placeholder:text-colorCabera focus:bg-colorLetraTras indent-6">
                <button type="submit" class="basis-1/6">Enviar</button>
            </form>
        </footer>
    @else
        <main>
            <h1 class="mt-8 text-center text-2xl text-white">No hay ninguna conversación seleccionada</h1>
        </main>
    @endif

    <script>
        // Inicia los eventos relacionados con js creados mediante livewire
        document.addEventListener('livewire:init', function() {
            Livewire.on('toggleParticipantesList', function() {
                let participantes = document.querySelector('.participantesList');
                let flecha = document.querySelector('.flecha');
                participantes.classList.toggle('hidden');
                flecha.classList.toggle('rotate-180');
                let info = document.querySelector('.mostrarInfo');
            });


        });

        function cerrarConversacion(){
            
        }

        // Reiniciador de scroll a la parte inferior
        window.addEventListener('scrollFixed',function(){
            setTimeout(() => {
                let contenedor=document.querySelector('.containerMessages');
                contenedor.scrollTop=contenedor.scrollHeight;
            }, 0);
        });

        
    </script>

    <script>

        // Conexión con pusher para el chat a tiempo real
        Pusher.logToConsole = true;
    
        var pusher = new Pusher('8388d0d243e690cebd7f', {
          cluster: 'eu'
        });
    
        var channel = pusher.subscribe('chat-channel');
        channel.bind('chat-event', function(data) {
        setTimeout(() => {
            Livewire.dispatch('actualizandoChat', { datos: data});
            },0);
        
        });
    </script>

</div>
