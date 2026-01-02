<?php

return [
    // Main VoIP Module Permission
    [
        'key'   => 'voip',
        'name'  => 'admin::app.acl.voip',
        'route' => 'admin.voip.providers.index',
        'sort'  => 10,
    ],

    // VoIP Providers Permissions
    [
        'key'   => 'voip.providers',
        'name'  => 'admin::app.acl.providers',
        'route' => 'admin.voip.providers.index',
        'sort'  => 1,
    ], [
        'key'   => 'voip.providers.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.voip.providers.create', 'admin.voip.providers.store'],
        'sort'  => 1,
    ], [
        'key'   => 'voip.providers.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.voip.providers.edit', 'admin.voip.providers.update'],
        'sort'  => 2,
    ], [
        'key'   => 'voip.providers.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.voip.providers.delete',
        'sort'  => 3,
    ],

    // VoIP Trunks Permissions
    [
        'key'   => 'voip.trunks',
        'name'  => 'admin::app.acl.trunks',
        'route' => 'admin.voip.trunks.index',
        'sort'  => 2,
    ], [
        'key'   => 'voip.trunks.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.voip.trunks.create', 'admin.voip.trunks.store'],
        'sort'  => 1,
    ], [
        'key'   => 'voip.trunks.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.voip.trunks.edit', 'admin.voip.trunks.update'],
        'sort'  => 2,
    ], [
        'key'   => 'voip.trunks.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.voip.trunks.delete',
        'sort'  => 3,
    ],

    // Inbound Routes Permissions
    [
        'key'   => 'voip.routes',
        'name'  => 'admin::app.acl.routes',
        'route' => 'admin.voip.routes.index',
        'sort'  => 3,
    ], [
        'key'   => 'voip.routes.create',
        'name'  => 'admin::app.acl.create',
        'route' => ['admin.voip.routes.create', 'admin.voip.routes.store'],
        'sort'  => 1,
    ], [
        'key'   => 'voip.routes.edit',
        'name'  => 'admin::app.acl.edit',
        'route' => ['admin.voip.routes.edit', 'admin.voip.routes.update'],
        'sort'  => 2,
    ], [
        'key'   => 'voip.routes.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.voip.routes.delete',
        'sort'  => 3,
    ],

    // Call Recordings Permissions
    [
        'key'   => 'voip.recordings',
        'name'  => 'admin::app.acl.recordings',
        'route' => 'admin.voip.recordings.index',
        'sort'  => 4,
    ], [
        'key'   => 'voip.recordings.play',
        'name'  => 'admin::app.acl.play',
        'route' => 'admin.voip.recordings.play',
        'sort'  => 1,
    ], [
        'key'   => 'voip.recordings.download',
        'name'  => 'admin::app.acl.download',
        'route' => 'admin.voip.recordings.download',
        'sort'  => 2,
    ], [
        'key'   => 'voip.recordings.delete',
        'name'  => 'admin::app.acl.delete',
        'route' => 'admin.voip.recordings.delete',
        'sort'  => 3,
    ],

    // Call Management Permissions
    [
        'key'   => 'voip.calls',
        'name'  => 'admin::app.acl.calls',
        'route' => 'admin.voip.calls.history',
        'sort'  => 5,
    ], [
        'key'   => 'voip.calls.initiate',
        'name'  => 'admin::app.acl.initiate',
        'route' => 'admin.voip.call',
        'sort'  => 1,
    ], [
        'key'   => 'voip.calls.all_calls',
        'name'  => 'admin::app.acl.all-calls',
        'route' => 'admin.voip.calls.history',
        'sort'  => 2,
    ],
];
