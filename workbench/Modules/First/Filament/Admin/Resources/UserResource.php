<?php

namespace Modules\First\Filament\Admin\Resources;

use App\Filament\Dashboard\Clusters\Test;
use Filament\Resources\Resource;
use Modules\First\Models\User;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $cluster = Test::class;
}
