<?php

namespace App\Filament\Admin\Resources;

use App\Models\Test;
use Filament\Resources\Resource;

class TestResource extends Resource
{
    protected static ?string $model = Test::class;
}
