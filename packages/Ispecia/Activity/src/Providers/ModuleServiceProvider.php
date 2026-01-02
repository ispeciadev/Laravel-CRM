<?php

namespace Ispecia\Activity\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Activity\Models\Activity::class,
        \Ispecia\Activity\Models\File::class,
        \Ispecia\Activity\Models\Participant::class,
    ];
}
