<?php

namespace App\Livewire\Chat;

use Livewire\Component;

class CrearChat extends Component
{

    public function comprobarChat($usuario)
    {
        echo 'buenos dias';
    }

    public function render()
    {
        return view('livewire.chat.crear-chat');
    }
}
