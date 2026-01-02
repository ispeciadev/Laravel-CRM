<?php

namespace Ispecia\Automation\Repositories;

use Ispecia\Automation\Contracts\Webhook;
use Ispecia\Core\Eloquent\Repository;

class WebhookRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Webhook::class;
    }
}
