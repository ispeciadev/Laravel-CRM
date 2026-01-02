<?php

namespace Ispecia\Admin\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'contacts.person.create.after' => [
            'Ispecia\Admin\Listeners\Person@linkToEmail',
        ],

        'lead.create.after' => [
            'Ispecia\Admin\Listeners\Lead@linkToEmail',
        ],

        'activity.create.after' => [
            'Ispecia\Admin\Listeners\Activity@afterUpdateOrCreate',
        ],

        'activity.update.after' => [
            'Ispecia\Admin\Listeners\Activity@afterUpdateOrCreate',
        ],
    ];
}
