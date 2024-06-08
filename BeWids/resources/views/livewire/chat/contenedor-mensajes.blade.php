<div class="contentedorMensajes grow flex flex-col">
    @if ($participanteSeleccionado)
    <header class="flex basis-1 items-center relative">
        <figure class=" w-8 mr-auto ml-4"><img src="https://picsum.photos/200" class="rounded-full" alt=""></figure>
        <h1 class="mr-auto font-bold">{{$participanteSeleccionado->nombre_en_portal}}</h1>
        <button class="mostrarListaParticipantes flex items-center" wire:click="$dispatch('toggleParticipantesList')">
            <p class="mr-4 font-extralight">Menu</p>
            <figure class="w-5 mr-4 flecha">
                    <img src="{{asset('imagenes/imagenesBasic/flechaAperturaInfo.png')}}">
                </figure>
        </button>

        <div class="bg-colorMain  participantesList hidden absolute top-7 right-0 p-4 flex flex-col ">
            <button wire:click="buscarInfoParticipantes('{{$participanteSeleccionado->nombre_en_portal}}')" class="hover:bg-colorFondo"><p>Mostrar información</p></button>
            <button wire:click="cerrarConversacion" class="hover:bg-colorFondo"> Cerrar conversación</button>
           
        </div>
        @if ($inforParticipante)
        <div class="bg-colorMain absolute top-12 right-5 w-[50%] p-10 mostrarInfo" >
            <figure wire:click="cerrarInfo"><img src="{{asset('imagenes/imagenesBasic/cancel.svg')}}" alt=""></figure>
            <h4><span class="text-blue-700">Nombre de usuario:</span><span>{{$inforParticipante->nombre}}</span></h4>
            <h4><span class="text-blue-700">Nombre en el portal:</span><span>{{$participanteSeleccionado->nombre_en_portal}}</span></h4>
            <h4><span class="text-blue-700">Descripción:</span><span>{{$inforParticipante->descripcion}}</span></h4>
            <h4><span class="text-blue-700">Numero de contacto:</span><span>{{$inforParticipante->numero_contacto}}</span></h4>
            <h4><span class="text-blue-700">Provincia:</span><span>{{$inforParticipante->provincia}}</span></h4>
            <h4><span class="text-blue-700"></span><span></span>
            
        </div>
        @endif
        
        
        
    </header>
    @endif
    @if($participantesSeleccionados)
    <header class="flex basis-1 items-center relative justify-between px-5">
        <h1 class="font-bold">{{$conversacionSeleccionada->name_group}}</h1>
        <button class="mostrarListaParticipantes flex items-center" wire:click="$dispatch('toggleParticipantesList')">
            <p class="mr-4 font-extralight">Menu</p>
            <figure class="w-5 mr-4 flecha">
                    <img src="{{asset('imagenes/imagenesBasic/flechaAperturaInfo.png')}}">
            </figure>
        </button>
        
        <ul class="hidden participantesList absolute right-0 top-6 p-4 bg-colorMain flex flex-col justify-center" >
            @foreach ($participantesSeleccionados as $participantee)
                <li class="flex justify-center items-center hover:bg-colorFondo" wire:click="buscarInfoParticipantes('{{$participantee}}')">
                    <p class="w-[50%]">{{$participantee}}</p>
                    <figure class="w-5 mr-4 flecha" >
                        <img src="{{asset('imagenes/imagenesBasic/flechaAperturaInfo.png')}}">
                    </figure>
                </li>
            @endforeach
            <button wire:click="cerrarConversacion" class="hover:bg-colorFondo">Cerrar conversación</button>
        </ul>
        @if ($inforParticipante)
            <div class="bg-colorMain absolute top-12 right-5 w-[50%] p-10  mostrarInfo">
                <figure wire:click="cerrarInfo"><img src="{{asset('imagenes/imagenesBasic/cancel.svg')}}" alt=""></figure>
                <h4><span class="text-blue-700">Nombre de usuario:</span><span>{{$inforParticipante->nombre}}</span></h4>
                <h4><span class="text-blue-700">Descripción:</span><span>{{$inforParticipante->descripcion}}</span></h4>
                <h4><span class="text-blue-700">Numero de contacto:</span><span>{{$inforParticipante->numero_contacto}}</span></h4>
                <h4><span class="text-blue-700">Provincia:</span><span>{{$inforParticipante->provincia}}</span></h4>
                <h4><span class="text-blue-700"></span><span></span></h4>
                    
            </div>
        @endif
            

 
    </header>
    @endif
    <main class="containerMessages grow bg-slate-800 overflow-y-scroll flex flex-col">
        @if ($participanteSeleccionado || $participantesSeleccionados)
            
        
        <div class="other bg-white w-[60%] ml-5 rounded-lg my-3 p-3">
            <p>Nombre</p>
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Sunt, animi velit ullam incidunt rerum laudantium voluptatem, fugiat odio repellat saepe voluptatibus corrupti vero totam aut quos quibusdam esse! Exercitationem, molestias.
            Inventore, enim unde? Magnam libero, nemo modi excepturi dolorem veniam recusandae molestiae quod provident quae totam fuga praesentium facere sed numquam ex eveniet doloribus alias saepe voluptas laudantium minus nihil.
            Ipsa accusamus inventore maxime blanditiis. Excepturi id dolores nihil corporis quo amet quam distinctio repudiandae culpa. Laborum delectus accusamus alias consequatur amet minus maiores rem eos, sunt porro, modi iusto!
            <p>Hace 5 horas</p>
        </div>
        <h1>HOla buenas tardes</h1>
           
        <h2>Funcion hijo de puta</h2>
        
        <div class="you w-[60%] self-end bg-blue-600 text-white mr-5 rounded-lg my-3 p-3 ">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate voluptatem quae porro. Commodi id est animi temporibus vel? Tempore quaerat nostrum sit facere voluptatum numquam sapiente commodi esse repellendus labore?
            <p>Hace 11min</p>
        </div>
        @else

        <h1 class="mt-8 text-center text-2xl text-white" >No hay ninguna conversación seleccionada</h1>
        @if ($inforParticipante)
            {{$inforParticipante=NULL}}
        @endif
        @endif
    </main>
    <footer class="sendMessage bg-blue-800 h-[10%] rounded-br-2xl flex">
        <input type="text" placeholder="Escribir mensaje" class="grow placeholder:pl-4 focus:bg-colorFondo">
        <button class="basis-1/6">Enviar</button>
    </footer>
    
    <script>
        document.addEventListener('livewire:init', function () {


            Livewire.on('toggleParticipantesList', function () {
                let participantes = document.querySelector('.participantesList');
                let flecha= document.querySelector('.flecha');
                    participantes.classList.toggle('hidden');
                    flecha.classList.toggle('rotate-180');  
                let info=document.querySelector('.mostrarInfo');
                console.log(info);
                                
            });

        });

    </script>
    
    
</div>