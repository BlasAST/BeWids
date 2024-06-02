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
            </tbody>
        </table>
        <div class="formEncuesta absolute top-0 hidden justify-center items-center w-full h-full ">
            <form action="/nuevaEncuesta" method="POST" class="bg-colorBarra2 flex flex-col w-[50%] h-[90%] items-center justify-around">
                @csrf
                <label for="nombreEncuesta">Nombre de la encuesta</label>
                <input type="text" id="nombreEncuesta" required>
                <label for="descripcion">Descripción</label>
                <textarea type="text" id="descripcion" class="w-[80%] h-[10rem]"></textarea>
                <div>
                    <label for="all">Votan todos</label>
                    <input type="checkbox" id="all">
                </div>
                <div>
                    <label for="one2Many">Pueden votar:</label>
                    <input type="checkbox" id="one2Many">
                </div>
                <!-- hacer un foreach con label e input de cada participante -->
                <button class="p-2 mb-2 bg-colorComplem rounded-3xl hover:bg-colorDetalles hover:text-white">Guardar Encuesta</button>
            </form>
        </div>
    </div>


</div>