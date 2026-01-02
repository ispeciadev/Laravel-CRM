<?php

namespace Ispecia\Lead\Repositories;

use Ispecia\Core\Eloquent\Repository;

class TypeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\Lead\Contracts\Type';
    }
}
