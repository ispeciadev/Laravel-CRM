<?php

namespace Ispecia\DataTransfer\Providers;

use Ispecia\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Define models to map with repository interfaces.
     *
     * @var array
     */
    protected $models = [
        \Ispecia\DataTransfer\Models\Import::class,
        \Ispecia\DataTransfer\Models\ImportBatch::class,
    ];
}
