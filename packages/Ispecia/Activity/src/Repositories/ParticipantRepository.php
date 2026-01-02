<?php

namespace Ispecia\Activity\Repositories;

use Ispecia\Core\Eloquent\Repository;

class ParticipantRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Ispecia\Activity\Contracts\Participant';
    }
}
