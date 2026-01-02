<?php

namespace Ispecia\Voip\DataGrids;

use Illuminate\Support\Facades\DB;
use Ispecia\DataGrid\DataGrid;
use Ispecia\Voip\Models\VoipProvider;

class ProviderDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('voip_providers')
            ->select('id', 'name', 'driver', 'is_active', 'priority', 'created_at', 'updated_at');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('voip::app.admin.providers.index.datagrid.id'),
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('voip::app.admin.providers.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'driver',
            'label'      => trans('voip::app.admin.providers.index.datagrid.driver'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $provider = VoipProvider::find($row->id);
                return $provider ? $provider->getDriverDisplayName() : ucfirst($row->driver);
            },
        ]);

        $this->addColumn([
            'index'      => 'is_active',
            'label'      => trans('voip::app.admin.providers.index.datagrid.status'),
            'type'       => 'boolean',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->is_active) {
                    return '<span class="badge badge-success">' . trans('voip::app.admin.providers.index.datagrid.active') . '</span>';
                }
                return '<span class="badge badge-secondary">' . trans('voip::app.admin.providers.index.datagrid.inactive') . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'priority',
            'label'      => trans('voip::app.admin.providers.index.datagrid.priority'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('voip::app.admin.providers.index.datagrid.created-at'),
            'type'       => 'datetime',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('voip::app.admin.providers.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.voip.providers.edit', $row->id);
            },
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('voip::app.admin.providers.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.voip.providers.destroy', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('voip::app.admin.providers.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.voip.providers.mass-destroy'),
        ]);
    }
}
