<?php

namespace Ispecia\Contact\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Contact\Models\Person::class,
        \Ispecia\Contact\Models\Organization::class,
    ];
}
