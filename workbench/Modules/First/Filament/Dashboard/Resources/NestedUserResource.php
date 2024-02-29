<?php

namespace Modules\First\Filament\Dashboard\Resources;

use Filament\Resources\Resource;
use Modules\First\Filament\Dashboard\Clusters\Users;
use Modules\First\Models\Nested\NestedUser;

class NestedUserResource extends Resource
{
    protected static ?string $model = NestedUser::class;

    protected static ?string $cluster = Users::class;
}
