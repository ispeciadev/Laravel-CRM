<?php

namespace Ispecia\Product\Repositories;

use Ispecia\Core\Eloquent\Repository;

class ProductInventoryRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\Product\Contracts\ProductInventory';
    }
}
