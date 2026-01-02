<?php

namespace Ispecia\Warehouse\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Warehouse\Models\Location::class,
        \Ispecia\Warehouse\Models\Warehouse::class,
    ];
}
