<?php

namespace Ispecia\Deal\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Deal\Models\Deal::class,
    ];
}
