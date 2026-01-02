<?php

namespace Ispecia\Core\Providers;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Core\Models\CoreConfig::class,
        \Ispecia\Core\Models\Country::class,
        \Ispecia\Core\Models\CountryState::class,
    ];
}
