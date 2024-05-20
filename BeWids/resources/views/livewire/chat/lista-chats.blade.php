<div>
    <div class="lista_header">
        <p>Chat</p>
        <img src="https://picsum.photos/200" alt="">
    </div>
    
    <div class="lista_main">
        @php
            $portal=Session::get('portal');
        @endphp
        <br>
        <div><input type="text" placeholder="Buscar Participante"></div>
        <div>
            <h1>{{$portal->id}}</h1>
            <h3>Mensajes:</h3>
            <br>
            <div class="chatItem">
                <img src="https://picsum.photos/200" alt="">
                <div class="infoUser">
                    <div class="itemsSup">
                        <div class="nombreChat">Ejemplo</div>
                        <div class="fecha">3h</div>
                    </div>
                    <div class="notificacion"></div>
                    <div class="resumen">Deja de llamarme...</div>
                    <div class="sinLeer"><p>52</p></div>
                </div>
            </div>
            @foreach($participantes as $participante)
                    <li>{{$participante->nombre_en_portal}}</li>
            @endforeach

            <br><br><br>
            <button>Crear nuevo chat</button>
            <ul>

            </ul>
        </div>
    </div>
</div>