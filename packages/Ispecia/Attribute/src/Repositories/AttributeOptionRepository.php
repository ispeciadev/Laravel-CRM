<?php

namespace Ispecia\Attribute\Repositories;

use Ispecia\Core\Eloquent\Repository;

class AttributeOptionRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\Attribute\Contracts\AttributeOption';
    }
}
