<?php

namespace Ispecia\Automation\Providers;

use Ispecia\Automation\Models\Webhook;
use Ispecia\Automation\Models\Workflow;
use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define the modals to map with this module.
     *
     * @var array
     */
    protected $models = [
        Workflow::class,
        Webhook::class,
    ];
}
