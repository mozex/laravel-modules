<?php

namespace App\Filament\Dashboard\Resources;

use App\Models\Nested\NestedTest;
use Filament\Resources\Resource;

class NestedTestResource extends Resource
{
    protected static ?string $model = NestedTest::class;
}
