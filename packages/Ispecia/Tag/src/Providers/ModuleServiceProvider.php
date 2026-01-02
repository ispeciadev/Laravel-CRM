<?php

namespace Ispecia\Tag\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Tag\Models\Tag::class,
    ];
}
