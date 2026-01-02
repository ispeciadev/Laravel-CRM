<?php

namespace Ispecia\DataTransfer\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\DataTransfer\Contracts\Import;

class ImportRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Import::class;
    }
}
