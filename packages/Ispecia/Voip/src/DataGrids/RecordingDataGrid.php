<?php

namespace Ispecia\Voip\DataGrids;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Ispecia\DataGrid\DataGrid;

class RecordingDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('voip_recordings')
            ->leftJoin('voip_calls', 'voip_recordings.call_id', '=', 'voip_calls.id')
            ->leftJoin('users', 'voip_calls.user_id', '=', 'users.id')
            ->addSelect(
                'voip_recordings.id',
                'voip_recordings.call_id',
                'voip_recordings.recording_sid',
                'voip_recordings.duration',
                'voip_recordings.file_url',
                'voip_recordings.created_at',
                'voip_calls.from_number',
                'voip_calls.to_number',
                'voip_calls.direction',
                'users.name as user_name',
            );

        $this->addFilter('id', 'voip_recordings.id');
        $this->addFilter('call_id', 'voip_recordings.call_id');

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
            'index'      => 'recording_sid',
            'label'      => 'Recording SID',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'    => 'from_number',
            'label'    => 'From',
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'to_number',
            'label'    => 'To',
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'direction',
            'label'    => 'Direction',
            'type'     => 'string',
            'sortable' => true,
            'closure'  => fn ($row) => ucfirst($row->direction ?? 'N/A'),
        ]);

        $this->addColumn([
            'index'    => 'user_name',
            'label'    => 'User',
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'duration',
            'label'    => 'Duration',
            'type'     => 'string',
            'sortable' => true,
            'closure'  => fn ($row) => gmdate('H:i:s', $row->duration ?? 0),
        ]);

        $this->addColumn([
            'index'    => 'created_at',
            'label'    => 'Date',
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
            'index'  => 'play',
            'icon'   => 'icon-play',
            'title'  => 'Play',
            'method' => 'GET',
            'url'    => fn ($row) => $row->file_url,
            'target' => '_blank',
        ]);

        $this->addAction([
            'index'  => 'download',
            'icon'   => 'icon-download',
            'title'  => 'Download',
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.voip.recordings.download', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.voip.recordings.destroy', $row->id),
        ]);
    }
}
