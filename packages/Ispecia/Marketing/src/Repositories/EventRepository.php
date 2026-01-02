<?php

namespace Ispecia\Marketing\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\Marketing\Contracts\Event;

class EventRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Event::class;
    }
}
