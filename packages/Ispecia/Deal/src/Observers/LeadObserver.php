<?php

namespace Ispecia\Deal\Observers;

use Ispecia\Deal\Repositories\DealRepository;
use Ispecia\Lead\Models\Lead;

class LeadObserver
{
    /**
     * Create a new observer instance.
     */
    public function __construct(
        protected DealRepository $dealRepository
    ) {}

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        $shouldCreateDeal = false;

        // Check if status_crm changed to 'qualified' or 'won'
        if ($lead->isDirty('status_crm') && in_array($lead->status_crm, ['qualified', 'won'])) {
            $shouldCreateDeal = true;
        }

        // Check if lead pipeline stage changed to 'Won'
        if ($lead->isDirty('lead_pipeline_stage_id') && $lead->stage) {
            $stageName = strtolower($lead->stage->name ?? '');
            if (str_contains($stageName, 'won')) {
                $shouldCreateDeal = true;
            }
        }

        // Create deal if conditions met and deal doesn't exist
        if ($shouldCreateDeal && !$this->dealRepository->findWhere(['lead_id' => $lead->id])->first()) {
            $this->dealRepository->createFromLead($lead);
        }
    }
}
