<?php

namespace Ispecia\EmailTemplate\Repositories;

use Ispecia\Core\Eloquent\Repository;

class EmailTemplateRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\EmailTemplate\Contracts\EmailTemplate';
    }
}
