
@for($i=$fechaInicio, $j=0;$j<42;$i->modify('+1 day'),$j++)

    @switch(true)
        @case($i->format('m') == $fechaFinal->format('m'))
            <div class="dia bg-colorComplem border-2 row-span-1 flex flex-col border-black">
                <h1 class="text-gray-300 indent-1">{{$i->format('d')}}</h1>
                <div class="w-full grow flex flex-col gap-1 justify-around py-1  dropZone">
                    
                    @foreach ($eventos as $evt)
                        @if($evt['fecha_cal'] == $i->format('Y-m-d'))
                        <div draggable="true" class="w-11/12 text-[9px] bg-white rounded-2xl px-2 text-center flex gap-1 justify-evenly items-center mx-auto evt">
                            <p>{{$evt['hora_inicio']}}</p>
                            <h1 class="font-bold whitespace-nowrap overflow-hidden text-ellipsis">{{$evt['titulo']}}</h1>
                            <p>{{$evt['hora_fin']}}</p>
                            <input type="hidden" value="{{$evt['id']}}">
                        </div>
                        @endif
                    @endforeach

                </div>
            </div>
            @break
        @case($i->format('m') < $fechaFinal->format('m'))
            <div class="dia bg-gray-400 border-2 border-black h-full flex flex-col"> 
                <h1>{{$i->format('d')}}</h1>
                <div class="w-full grow flex gap-1 items-center dropZone mesMenor">
                    @foreach ($eventos as $evt)
                        @if($evt['fecha_cal'] == $i->format('Y-m-d'))
                            <figure class="h-4 w-4 rounded-full bg-colorDetalles"></figure>
                        @endif
                    @endforeach
                </div>
            </div>  
            @break
        @case($i->format('m') > $fechaFinal->format('m'))
            <div class="dia bg-gray-400 border-2 border-black h-full flex flex-col"> 
                <h1>{{$i->format('d')}}</h1>
                <div class="w-full grow flex gap-1 items-center  dropZone mesMayor">
                    @foreach ($eventos as $evt)
                        @if($evt['fecha_cal'] == $i->format('Y-m-d'))
                            <figure class="h-4 w-4 rounded-full bg-colorDetalles"></figure>
                        @endif
                    @endforeach
                </div>
            </div>
            @break
        @default
            
    @endswitch

@endfor