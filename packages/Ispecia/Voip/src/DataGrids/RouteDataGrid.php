<?php

namespace Ispecia\Voip\DataGrids;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ispecia\DataGrid\DataGrid;

class RouteDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('voip_routes')
            ->addSelect(
                'voip_routes.id',
                'voip_routes.name',
                'voip_routes.pattern',
                'voip_routes.destination_type',
                'voip_routes.destination_id',
                'voip_routes.priority',
                'voip_routes.is_active',
                'voip_routes.created_at',
            );

        $this->addFilter('id', 'voip_routes.id');
        $this->addFilter('name', 'voip_routes.name');
        $this->addFilter('is_active', 'voip_routes.is_active');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => 'ID',
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Route Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'pattern',
            'label'      => 'Pattern',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'    => 'destination_type',
            'label'    => 'Destination',
            'type'     => 'string',
            'sortable' => true,
            'closure'  => fn ($row) => ucfirst($row->destination_type),
        ]);

        $this->addColumn([
            'index'    => 'priority',
            'label'    => 'Priority',
            'type'     => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'is_active',
            'label'    => 'Status',
            'type'     => 'boolean',
            'sortable' => true,
            'closure'  => fn ($row) => $row->is_active 
                ? '<span class="label-active">Active</span>' 
                : '<span class="label-inactive">Inactive</span>',
        ]);

        $this->addColumn([
            'index'    => 'created_at',
            'label'    => 'Created',
            'type'     => 'datetime',
            'sortable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => 'Edit',
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.voip.routes.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.voip.routes.destroy', $row->id),
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'POST',
            'url'    => route('admin.voip.routes.mass_destroy'),
        ]);
    }
}
