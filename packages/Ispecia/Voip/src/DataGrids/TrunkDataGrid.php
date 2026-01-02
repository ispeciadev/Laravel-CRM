<?php

namespace Ispecia\Voip\DataGrids;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ispecia\DataGrid\DataGrid;

class TrunkDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('voip_trunks')
            ->addSelect(
                'voip_trunks.id',
                'voip_trunks.name',
                'voip_trunks.provider',
                'voip_trunks.host',
                'voip_trunks.username',
                'voip_trunks.is_active',
                'voip_trunks.created_at',
            );

        $this->addFilter('id', 'voip_trunks.id');
        $this->addFilter('name', 'voip_trunks.name');
        $this->addFilter('provider', 'voip_trunks.provider');
        $this->addFilter('is_active', 'voip_trunks.is_active');

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
            'label'      => 'Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'provider',
            'label'      => 'Provider',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'host',
            'label'      => 'Host',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'username',
            'label'      => 'Username',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
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
            'url'    => fn ($row) => route('admin.voip.trunks.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.voip.trunks.destroy', $row->id),
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
            'url'    => route('admin.voip.trunks.mass_destroy'),
        ]);
    }
}
