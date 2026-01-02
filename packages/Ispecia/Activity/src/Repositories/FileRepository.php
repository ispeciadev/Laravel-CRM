<?php

namespace Ispecia\Activity\Repositories;

use Ispecia\Core\Eloquent\Repository;

class FileRepository extends Repository
{
    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return \Ispecia\Activity\Contracts\File::class;
    }
}
