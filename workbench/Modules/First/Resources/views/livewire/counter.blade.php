<?php

use Livewire\Component;

new class extends Component {
    public int $count = 2;

    public function increment(): void
    {
        $this->count++;
    }
};
?>

<div>
    <span>Counter: {{ $count }}</span>
</div>
