<?php

namespace Ispecia\DataGrid\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\DataGrid\Models\SavedFilter::class,
    ];
}
