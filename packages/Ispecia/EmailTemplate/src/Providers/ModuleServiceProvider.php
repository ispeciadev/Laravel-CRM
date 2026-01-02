<?php

namespace Ispecia\EmailTemplate\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\EmailTemplate\Models\EmailTemplate::class,
    ];
}
