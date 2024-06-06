<div class="contentedorMensajes grow flex flex-col">
    @if ($participanteSeleccionado)
    <header class="flex basis-1 items-center">
        <figure class=" w-8 mr-auto ml-4"><img src="https://picsum.photos/200" class="rounded-full" alt=""></figure>
        <h1 class="mr-auto">{{$participanteSeleccionado->nombre_en_portal}}</h1>
    </header>
    @endif
    @if($participantesSeleccionados)
    <header class="flex basis-1 items-center relative justify-between px-5">
        <h1>{{$conversacionSeleccionada->name_group}}</h1>
        <button class="mostrarListaParticipantes" wire:click="$dispatch('toggleParticipantesList')">Mostrar participantes</button>
        <ul class="hidden participantesList absolute right-0 top-0 p-4 bg-colorMain">
            @foreach ($participantesSeleccionados as $participantee)
                <li class="flex justify-center">
                    <p class="w-[50%]">{{$participantee}}</p>
                    <button wire:click="buscarInfoParticipantes('{{$participantee}}')">Abrir</button>
                </li>
            @endforeach
            @if ($inforParticipante)
                {{$inforParticipante}}
            @endif
        </ul>
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
        <h1>No hay ninguna conversación seleccionada</h1>
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
                    participantes.classList.toggle('hidden');
                    console.log('aqui tamo')
            });
        });
    </script>
    
    
</div>