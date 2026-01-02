<?php

namespace Ispecia\DataGrid\Repositories;

use Ispecia\Core\Eloquent\Repository;
use Ispecia\DataGrid\Contracts\SavedFilter;

class SavedFilterRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return SavedFilter::class;
    }
}
