<?php

namespace Modules\Second\Filament\Admin\Resources;

use Filament\Resources\Resource;
use Modules\Second\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;
}
