<?php

namespace App\Livewire;
use Livewire\Component;

class ChatYEncuestas extends Component
{
    public $mensaje;

    public function mount()
    {
        $this->mensaje= "";
    }

    public function render()
    {
        return view('livewire.chat-y-encuestas');
    }
}
