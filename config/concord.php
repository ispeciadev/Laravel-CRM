<?php

return [
    'modules' => [
        \Ispecia\Activity\Providers\ModuleServiceProvider::class,
        \Ispecia\Admin\Providers\ModuleServiceProvider::class,
        \Ispecia\Attribute\Providers\ModuleServiceProvider::class,
        \Ispecia\Automation\Providers\ModuleServiceProvider::class,
        \Ispecia\Contact\Providers\ModuleServiceProvider::class,
        \Ispecia\Core\Providers\ModuleServiceProvider::class,
        \Ispecia\DataGrid\Providers\ModuleServiceProvider::class,
        \Ispecia\Deal\Providers\ModuleServiceProvider::class,
        \Ispecia\EmailTemplate\Providers\ModuleServiceProvider::class,
        \Ispecia\Email\Providers\ModuleServiceProvider::class,
        \Ispecia\Lead\Providers\ModuleServiceProvider::class,
        \Ispecia\Product\Providers\ModuleServiceProvider::class,
        \Ispecia\Quote\Providers\ModuleServiceProvider::class,
        \Ispecia\Tag\Providers\ModuleServiceProvider::class,
        \Ispecia\User\Providers\ModuleServiceProvider::class,
        \Ispecia\Warehouse\Providers\ModuleServiceProvider::class,
        \Ispecia\WebForm\Providers\ModuleServiceProvider::class,
        \Ispecia\DataTransfer\Providers\ModuleServiceProvider::class,
    ],

    'register_route_models' => true,
];
