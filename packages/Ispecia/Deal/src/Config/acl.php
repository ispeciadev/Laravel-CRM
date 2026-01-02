<?php

return [
    [
        'key'   => 'deals',
        'name'  => 'Deals',
        'route' => 'admin.deals.index',
        'sort'  => 3,
    ], [
        'key'   => 'deals.create',
        'name'  => 'Create',
        'route' => ['admin.deals.create', 'admin.deals.store'],
        'sort'  => 1,
    ], [
        'key'   => 'deals.view',
        'name'  => 'View',
        'route' => 'admin.deals.view',
        'sort'  => 2,
    ], [
        'key'   => 'deals.edit',
        'name'  => 'Edit',
        'route' => ['admin.deals.edit', 'admin.deals.update', 'admin.deals.mass_update'],
        'sort'  => 3,
    ], [
        'key'   => 'deals.delete',
        'name'  => 'Delete',
        'route' => ['admin.deals.delete', 'admin.deals.mass_delete'],
        'sort'  => 4,
    ],
];
