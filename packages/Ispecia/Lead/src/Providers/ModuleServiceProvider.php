<?php

namespace Ispecia\Lead\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Lead\Models\Lead::class,
        \Ispecia\Lead\Models\Pipeline::class,
        \Ispecia\Lead\Models\Product::class,
        \Ispecia\Lead\Models\Source::class,
        \Ispecia\Lead\Models\Stage::class,
        \Ispecia\Lead\Models\Type::class,
    ];
}
