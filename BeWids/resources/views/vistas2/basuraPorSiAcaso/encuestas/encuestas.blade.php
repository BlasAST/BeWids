<div class="encuestas w-full h-full">
    <div class="botonesEncuestas flex justify-center">
        <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4 creadorEncuestas">Crear encuesta</button>
        <button class="bg-colorBarra2 text-white p-4 rounded-xl mx-4">Encuestas finalizadas</button>
    </div>

    <div class="listadoEncuestas w-full h-full border-4 overflow-y-scroll relative">
        <table class=" w-full">
            <thead class=" border-4sticky top-0 h-[20%]">
                <tr>
                    <th> Nombre encuesta</th>
                    <th>Porcetaje positivo</th>
                    <th>Porcentaje negativo</th>
                    <th>Votadores</th>
                    <th>Descripción</th>
                    <th><button>Detalles</button></th>
                    <th><button>Votar</button></th>
                </tr>
            </thead>
            <tbody class="border-4 border-solid border-blue-700 ">
                @foreach ($encuestas as $encuesta)
                    
                @endforeach
            </tbody>
        </table>
        <div class="formEncuesta absolute top-0 hidden justify-center items-center w-full h-full ">
            <form wire:submit.prevent="crearEncuesta" class="bg-colorBarra2 flex flex-col w-[50%] h-[90%] items-center justify-around">
                @csrf
                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" wire:model="titulo" >
                <label for="descripcion">Descripción</label>
                <textarea type="text" id="descripcion" wire:model="descripcion" class="w-[80%] h-[10rem]"></textarea>
                <div>
                    <label for="all">Votan todos</label>
                    <input type="checkbox" id="all" wire:model="allParticipantes">
                </div>
                <div>
                    <label for="one2Many">Pueden votar:</label>
                    <input type="checkbox" id="one2Many">
                </div>
            <div class="seleccionados flex flex-wrap justify-center px-2 py-5" >
                @foreach ($participantes as $participante)
                <div class="basis-[40%]">
                        <label for="{{$participante->nombre_en_portal}}">{{$participante->nombre_en_portal}}</label>
                        <input type="checkbox" id="{{$participante->nombre_en_portal}}" wire:model="participantesSeleccionados">
                </div>
                @endforeach
            </div>
                <div class="opciones flex flex-col h-[20%] overflow-y-auto justify-around">
                        
                        <input type="text" wire:model="opciones_votos" placeholder="Opcion de encuesta" class="opciones_votos">
                        <input type="text" wire:model="opciones_votos" placeholder="Opcion de encuesta" class="opciones_votos">
                        
                </div>
                
                <div class="bg-colorComplem rounded-3xl p-1 mb-4" onclick="aniadirInput">Crear más</div>

                <button type="submit" class="p-2 mb-2 bg-colorComplem rounded-3xl hover:bg-colorDetalles hover:text-white">Guardar Encuesta</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:init', function() {
            
        });
        function aniadirInput(evt){
            evt.preventDefault();
            console.log('hola');
        }
        
      
    </script>

</div>