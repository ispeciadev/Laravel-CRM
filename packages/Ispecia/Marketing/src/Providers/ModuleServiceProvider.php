<?php

namespace Ispecia\Marketing\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define the module's array.
     *
     * @var array
     */
    protected $models = [
        \Ispecia\Marketing\Models\Event::class,
        \Ispecia\Marketing\Models\Campaign::class,
    ];
}
