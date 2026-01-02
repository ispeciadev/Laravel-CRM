<?php

namespace Ispecia\Deal\Repositories;

use Ispecia\Core\Eloquent\Repository;

class DealRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Ispecia\Deal\Contracts\Deal';
    }

    /**
     * Create deal from lead
     */
    public function createFromLead($lead)
    {
        return $this->create([
            'title'                    => $lead->title,
            'description'              => $lead->description,
            'deal_value'               => $lead->lead_value,
            'status'                   => 'open',
            'expected_close_date'      => $lead->expected_close_date,
            'user_id'                  => $lead->user_id,
            'person_id'                => $lead->person_id,
            'lead_id'                  => $lead->id,
            'lead_source_id'           => $lead->lead_source_id,
            'lead_type_id'             => $lead->lead_type_id,
            'lead_pipeline_id'         => $lead->lead_pipeline_id,
            'lead_pipeline_stage_id'   => $lead->lead_pipeline_stage_id,
        ]);
    }
}
