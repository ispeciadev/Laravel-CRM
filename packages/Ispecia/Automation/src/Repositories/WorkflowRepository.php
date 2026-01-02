<?php

namespace Ispecia\Automation\Repositories;

use Ispecia\Automation\Contracts\Workflow;
use Ispecia\Core\Eloquent\Repository;

class WorkflowRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Workflow::class;
    }
}
