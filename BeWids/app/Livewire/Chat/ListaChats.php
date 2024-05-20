<?php

namespace App\Livewire\Chat;

use App\Models\Participantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ListaChats extends Component
{
    public $participantes;
    public $usuario;
    public $portal;
    
    public function render()
    {
        $this->portal=Session::get('portal');
        $this->usuario=Auth::user();
         $this->participantes=Participantes::all();
        
        return view('livewire.chat.lista-chats',[
            'participantes'=>$this->participantes,
        ]);
    }
}
