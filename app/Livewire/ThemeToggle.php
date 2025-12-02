<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public $theme;

    public function mount()
    {
        $this->theme = session('theme', 'light');
        $this->dispatch('themeChanged', theme: $this->theme);
    }

    public function toggleTheme()
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        session(['theme' => $this->theme]);

        $this->dispatch('themeChanged', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
