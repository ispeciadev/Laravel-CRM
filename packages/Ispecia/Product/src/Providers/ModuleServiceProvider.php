<?php

namespace Ispecia\Product\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Product\Models\Product::class,
        \Ispecia\Product\Models\ProductInventory::class,
    ];
}
