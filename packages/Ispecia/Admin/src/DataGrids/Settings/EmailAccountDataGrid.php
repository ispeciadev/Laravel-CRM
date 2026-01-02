<?php

namespace Ispecia\Admin\DataGrids\Settings;

use Illuminate\Support\Facades\DB;
use Ispecia\DataGrid\DataGrid;


class EmailAccountDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('email_accounts')
            ->select('id', 'name', 'email', 'host', 'port', 'is_default', 'is_active');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.email-accounts.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('admin::app.settings.email-accounts.index.datagrid.email'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'host',
            'label'      => trans('admin::app.settings.email-accounts.index.datagrid.host'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => $row->host . ':' . $row->port,
        ]);

        $this->addColumn([
            'index'      => 'is_default',
            'label'      => trans('admin::app.settings.email-accounts.index.datagrid.default'),
            'type'       => 'boolean',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return $row->is_default
                    ? '<span class="label-active">' . trans('admin::app.settings.email-accounts.index.datagrid.yes') . '</span>'
                    : '<span class="label-pending">' . trans('admin::app.settings.email-accounts.index.datagrid.no') . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'is_active',
            'label'      => trans('admin::app.settings.email-accounts.index.datagrid.status'),
            'type'       => 'boolean',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return $row->is_active
                    ? '<span class="label-active">' . trans('admin::app.settings.email-accounts.index.datagrid.active') . '</span>'
                    : '<span class="label-pending">' . trans('admin::app.settings.email-accounts.index.datagrid.inactive') . '</span>';
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.email_accounts.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.settings.email-accounts.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.settings.email_accounts.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.automation.email_accounts.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.email-accounts.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.settings.email_accounts.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('settings.automation.email_accounts.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.settings.email-accounts.index.datagrid.mass-delete'),
                'method' => 'POST',
                'url'    => route('admin.settings.email_accounts.mass_delete'),
            ]);
        }
    }
}
