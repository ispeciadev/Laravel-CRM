<?php

return [
    [
        'key'        => 'voip',
        'name'       => 'VoIP',
        'route'      => 'admin.voip.providers.index',
        'sort'       => 6,
        'icon-class' => 'icon-phone',
    ],
    [
        'key'        => 'voip.providers',
        'name'       => 'Providers',
        'route'      => 'admin.voip.providers.index',
        'sort'       => 1,
        'icon-class' => '',
    ],
    [
        'key'        => 'voip.trunks',
        'name'       => 'Trunks',
        'route'      => 'admin.voip.trunks.index',
        'sort'       => 2,
        'icon-class' => '',
    ],
    [
        'key'        => 'voip.routes',
        'name'       => 'Inbound Routes',
        'route'      => 'admin.voip.routes.index',
        'sort'       => 3,
        'icon-class' => '',
    ],
    [
        'key'        => 'voip.recordings',
        'name'       => 'Call Recordings',
        'route'      => 'admin.voip.recordings.index',
        'sort'       => 4,
        'icon-class' => '',
    ],
];
