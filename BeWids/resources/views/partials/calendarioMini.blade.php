@php
    $diasSemanaCorto = ['L', 'M', 'X', 'J', 'V', 'S','D'];
@endphp
<section class="flex flex-col w-full h-full">
    <div class="basis-1/12 grid grid-cols-7">
        @foreach ($diasSemanaCorto as $dia)
            <div class="font-bold bg-gray-200 flex items-center justify-center border-2 border-black ">{{ $dia }}</div>
        @endforeach
    </div>
    <div class="grid grid-cols-7 grid-rows-6 basis-11/12 contDias">



        @for($i=$fechaInicio, $j=0;$j<42;$i->modify('+1 day'),$j++)

            @if ($i->format('m') == $fechaFinal->format('m'))
                

                    <div class="dia bg-colorComplem border-2 row-span-1 flex flex-col items-center border-black pb-1">
                        <h1 class="text-gray-300 grow flex justify-center items-center text-center text-sm">{{$i->format('d')}}</h1>
                        <div class="flex basis-1/6 gap-1">
                            @foreach ($eventosCal as $evt)
                                @if($evt['fecha_cal'] == $i->format('Y-m-d'))
                                    <figure class="h-1 w-1 rounded-full bg-colorDetalles"></figure>
                                @endif
                            @endforeach
                        </div>
                        
                    </div>
            @else
                    <div class="dia bg-gray-400 border-2 row-span-1 flex flex-col items-center border-black pb-1"> 
                        <h1 class="grow flex justify-center items-center text-center text-smtext-sm">{{$i->format('d')}}</h1>
                        <div class="flex basis-1/6 gap-1">
                            @foreach ($eventosCal as $evt)
                                @if($evt['fecha_cal'] == $i->format('Y-m-d'))
                                    <figure class="h-1 w-1 rounded-full bg-colorComplem"></figure>
                                @endif
                            @endforeach
                        </div>
                    </div>  
            @endif

        @endfor
    </div>
</section>
