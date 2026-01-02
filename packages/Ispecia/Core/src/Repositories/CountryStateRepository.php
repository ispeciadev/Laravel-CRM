<?php

namespace Ispecia\Core\Repositories;

use Prettus\Repository\Traits\CacheableRepository;
use Ispecia\Core\Eloquent\Repository;

class CountryStateRepository extends Repository
{
    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\Core\Contracts\CountryState';
    }
}
