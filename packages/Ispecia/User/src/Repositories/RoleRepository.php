<?php

namespace Ispecia\User\Repositories;

use Ispecia\Core\Eloquent\Repository;

class RoleRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\User\Contracts\Role';
    }
}
