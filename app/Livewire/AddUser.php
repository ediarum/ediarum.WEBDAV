<?php

namespace App\Livewire;

use Livewire\Component;

class AddUser extends Component
{
    public $users;

    public function render()
    {
        return view('livewire.add-user');
    }
}
