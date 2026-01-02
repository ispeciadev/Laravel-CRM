<?php

namespace Ispecia\Marketing\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\Marketing\Contracts\Campaign;

class CampaignRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Campaign::class;
    }
}
