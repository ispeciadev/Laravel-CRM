<?php

namespace Ispecia\WebForm\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\WebForm\Models\WebForm::class,
        \Ispecia\WebForm\Models\WebFormAttribute::class,
    ];
}
