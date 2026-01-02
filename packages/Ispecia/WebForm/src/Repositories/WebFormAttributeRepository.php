<?php

namespace Ispecia\WebForm\Repositories;

use Ispecia\Core\Eloquent\Repository;

class WebFormAttributeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\WebForm\Contracts\WebFormAttribute';
    }
}
