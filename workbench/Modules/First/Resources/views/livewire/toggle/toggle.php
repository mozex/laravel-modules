<?php

use Livewire\Component;

new class extends Component
{
    public bool $on = false;

    public function toggle(): void
    {
        $this->on = ! $this->on;
    }
};
