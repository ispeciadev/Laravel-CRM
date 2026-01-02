<?php

namespace Ispecia\Attribute\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Attribute\Models\Attribute::class,
        \Ispecia\Attribute\Models\AttributeOption::class,
        \Ispecia\Attribute\Models\AttributeValue::class,
    ];
}
