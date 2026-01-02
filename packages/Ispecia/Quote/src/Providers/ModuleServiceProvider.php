<?php

namespace Ispecia\Quote\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Quote\Models\Quote::class,
        \Ispecia\Quote\Models\QuoteItem::class,
    ];
}
