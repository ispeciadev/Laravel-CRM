<?php

namespace Ispecia\DataTransfer\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\DataTransfer\Contracts\ImportBatch;

class ImportBatchRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ImportBatch::class;
    }
}
