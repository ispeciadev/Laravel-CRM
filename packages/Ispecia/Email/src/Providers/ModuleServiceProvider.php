<?php

namespace Ispecia\Email\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Ispecia\Email\Models\Email::class,
        \Ispecia\Email\Models\Attachment::class,
    ];
}
