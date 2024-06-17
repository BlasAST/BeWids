
<div class="min-h-20  w-full  flex items-stretch justify-center flex-wrap evento" >
                <figure class="basis-1/6 imagenEvento m-0"></figure>
                <div class="flex flex-wrap basis-5/6 justify-evenly space-y-2 font-bold indent-3">

                    {{-- TITULO --}}
                    @if (array_key_exists('titulo',$evento))
                        <h3 class="basis-full text-center text-xl py-5">{{$evento['titulo']}}</h3>
                    @endif




                    @if (array_key_exists('descripcion',$evento))
                        <p class="max-h-[3.15rem] text-xs overflow-hidden text-ellipsis basis-full font-normal pl-3">{{$evento['descripcion']}}</p>
                    @endif




                    <div class="basis-3/6">
                        @if (array_key_exists('inicio',$evento)&&array_key_exists('fin',$evento))
                            <p>Inicio: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['inicio'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['inicio'])[1]))}}</span></p>
                            <p>Fin: <span class="font-normal text-xs">{{ date('d M Y',strtotime(explode(" ",$evento['fin'])[0]))}} - {{date('H:i',strtotime(explode(" ",$evento['fin'])[1]))}}</span></p>
                        @endif
                        
                   </div>




                   <div class="basis-3/6">
                    @if (array_key_exists('horas',$evento))
                        <p>Horario: <span class="font-normal text-xs">{{$evento['horas']}} @if(array_key_exists('dias',$evento)) {{$evento['dias']}} @endif </span></p>
                    @endif






                    @if (array_key_exists('precio',$evento))
                        <p>Precio:<span class="font-normal text-xs bg-opacity-50"> {{$evento['precio']}}</span></p>
                    @endif

                    
                       
                   </div>
                   @if (array_key_exists('calle',$evento))
                        <p class="basis-full">Lugar: <span class="font-normal text-xs">{{$evento['calle'].", ".$evento['cp'].", ".$evento['localidad']}} @if (array_key_exists('lugar',$evento)) -> {{$evento['lugar']}} @endif </span></p>
                    @endif











                    @if (array_key_exists('conex',$evento))
                        <p class="font-normal basis-full text-colorDetalles"><a href="{{$evento['conex']}}">Link al evento</a> </p>
                    @endif
                   {{-- <div class="basis-full">
                        @if(array_key_exists('address',$evento)&& array_key_exists('area',$evento['address'])&& array_key_exists('street-address',$evento['address']['area']))<p>Lugar: <span class="font-normal text-xs">{{$evento['address']['area']['street-address'].", ".$evento['address']['area']['postal-code'].", ".$evento['address']['area']['locality']." -> ".$evento['event-location']}}</span></p>@endif                        
                        @if (array_key_exists('link',$evento))
                        <a href="{{$evento['link']}}" class="font-normal ">Link al evento</a>
                        @endif

                   </div> --}}
                </div>
                <div class="hidden basis-full min-h-96 items-stretch m-4">
                    @if (array_key_exists('latitud',$evento))
                        <div class="basis-1/2" id={{$evento['latitud'].'|'.$evento['longitud']}}></div>
                    @endif
                    <div class="grow flex flex-col justify-around">
                        <button class="mx-auto rounded-md bg-colorDetalles b-2 border-colorComplem px-4 py-6">Añadir a "Nuestra Lista"</button>
                        <button class="mx-auto rounded-md bg-colorDetalles b-2 border-colorComplem px-4 py-6">Añadir al "Calendario"</button>

                    </div>
                    
                </div>
                <form action="">
                    <input type="hidden" name="evento" value={{$evento['url']}}>
                </form>

            </div>





            {{-- <figure class=" btnCE row-[10] col-[1] w-[80%] h-[40%] self-end mb-5 justify-self-center">
        <img src="{{asset('imagenes/imagenesBasic/chat2.png')}}" alt="">
    </figure>
    <figure class=" btnCE2 row-[10] col-[2] w-[80%] h-[40%] self-end mb-5 justify-self-end">
        <img src="{{asset('imagenes/imagenesBasic/encuestas.png')}}" alt="">
    </figure> --}}