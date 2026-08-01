<?php

declare(strict_types=1);

namespace Modules\Second\Filament\Admin\Resources\Invoices;

use Filament\Resources\Resource;
use Modules\Second\Models\Invoice;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
}
