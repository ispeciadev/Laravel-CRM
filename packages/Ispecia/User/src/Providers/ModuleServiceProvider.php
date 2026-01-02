<?php

namespace Ispecia\User\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\User\Models\Group::class,
        \Ispecia\User\Models\Role::class,
        \Ispecia\User\Models\User::class,
    ];
}
