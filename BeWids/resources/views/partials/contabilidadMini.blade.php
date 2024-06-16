@php
    if($usuario){
        if($deudaMax != 0)
        $porcentaje = (abs($yo->deuda)/$deudaMax)*100;
        else
            $porcentaje = 0;
    }

@endphp

<div class="basis-1/4 flex justify-center pt-6">
    @if ($usuario)
        <div class="flex flex-col items-center">
            @if ($yo->deuda > 0)
                <div class="grow w-[20px] lg:w-[40px] relative" style="background-image: linear-gradient(to top, #4465B8,#0b191f {{$porcentaje.'%'}}, #0b191f 100%)">
                    <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-x-[-50%] left-[50%] flex flex-col justify-center items-center" style="color:#4465B8; bottom: {{$porcentaje - 5 . "%" }}" ><p>{{"+".$yo->deuda}}</p></figure>
                </div>
            @endif
            @if($yo->deuda < 0)
                <div class="grow w-[20px] lg:w-[40px] relative" style="background-image: linear-gradient(to top, #D63865,#0b191f {{$porcentaje."%"}}, #0b191f 100%)">
                    <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-x-[-50%] left-[50%] flex flex-col justify-center items-center" style="color:#D63865; bottom: {{$porcentaje - 5 . "%"}}"><p>{{$yo->deuda}}</p></figure>
                </div>
            @endif
            @if($yo->deuda == 0)
                <div class="grow w-[20px] lg:w-[40px] relative bg-[#0b191f]">
                    <figure class="absolute bg-colorCaberaTras2 w-[55px] h-[55px] lg:w-[75px] lg:h-[75px] rounded-full translate-x-[-50%] left-[50%] flex flex-col justify-center items-center bottom-[-5%] text-colorComplem"><p>+{{$yo->deuda}}</p></figure>
                </div>
            @endif
        </div>
    @endif
</div>
<div class="basis-3/4 flex flex-col text-sm lg:text-base">
    <div class="grow flex flex-col gap-3 items-stretch px-3 py-6 overflow-y-auto">
        @if ($usuario)
            @foreach ($reembolsos as $reembolso)
                <div class="bg-colorComplem text-colorLetra rounded-3xl px-4 text-center">
                    <p>Debes {{$reembolso->cantidad}}€ a:</p>
                    <p>{{$reembolso->receptor}}</p>
                </div>
            @endforeach
            @foreach ($deudas as $deuda)
                <div class="bg-colorDetallesTras text-colorLetra rounded-3xl px-4 text-center">
                    <p>{{$deuda->pagador}} te debe:</p>
                    <p>{{$deuda->cantidad}}€</p>
                </div>
            @endforeach
            
        @endif
    </div>
    <p class="basis-1/12 @if($notificaciones) text-colorDetalles @else text-colorComplem @endif">@if($notificaciones)Tienes solicitudes de rembolso, revisa tus notificaciones @else No tienes ninguna solicitud de reembolso @endif</p>
</div>