<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TestEvent;

class TestListener
{
    public function handle(TestEvent $event): void {}
}
