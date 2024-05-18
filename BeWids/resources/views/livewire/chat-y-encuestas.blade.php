
<div class="generico">
    <div class="chat mostrar">
        <div class="secciones">
            <div><input type="text" placeholder="Buscar Participante"></div>
            <div>
                <h3>Mensajes:</h3>
                <button>Crear nuevo chat</button>
                <ul>

                </ul>
            </div>

        </div>

        <div class="chatActual">

            <div class="enviarMensaje">
                <input type="text" wire:model="mensaje">
                <p>{{ $mensaje }}</p>
                <!-- <button>Enviar</button> -->
                
                
            </div>
        </div>

    </div>
    <div class="encuestas">
        <h1>Estas son las Encuestas</h1>
    </div>
    <div class="crearCHat"></div>
    <div class="crearEncuesta"></div>

</div>