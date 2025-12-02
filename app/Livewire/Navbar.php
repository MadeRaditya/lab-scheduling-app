<?php

namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public $isMenuOpen = false;

    public function toggleMenu()
    {
        $this->isMenuOpen = !$this->isMenuOpen;
    }

    public function closeMenu()
    {
        $this->isMenuOpen = false;
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}