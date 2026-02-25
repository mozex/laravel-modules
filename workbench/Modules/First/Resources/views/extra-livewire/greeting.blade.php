<?php

use Livewire\Component;

new class extends Component
{
    public string $message = 'Hello from extra livewire directory';
};
?>

<div>
    <span>{{ $message }}</span>
</div>
