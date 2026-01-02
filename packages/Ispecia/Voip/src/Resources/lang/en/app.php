<?php

return [
    'admin' => [
        'trunks' => [
            'title' => 'VoIP Trunks',
            'create-title' => 'Create VoIP Trunk',
            'edit-title' => 'Edit VoIP Trunk',
            'save-btn' => 'Save',
            'update-btn' => 'Update',
        ],
        'routes' => [
            'title' => 'Inbound Routes',
        ],
        'recordings' => [
            'title' => 'Call Recordings',
        ],
        'providers' => [
            'title' => 'VoIP Providers',
            'create-title' => 'Create VoIP Provider',
            'edit-title' => 'Edit VoIP Provider',
            'save-btn' => 'Save Provider',
            'update-btn' => 'Update Provider',
            'create-success' => 'Provider created successfully',
            'update-success' => 'Provider updated successfully',
            'delete-success' => 'Provider deleted successfully',
            'delete-active-error' => 'Cannot delete active provider',
            'activate-success' => 'Provider activated successfully',
            'activate-validation-error' => 'Cannot activate provider with invalid configuration',
            'test-connection' => 'Test Connection',
            
            'index' => [
                'datagrid' => [
                    'id' => 'ID',
                    'name' => 'Name',
                    'driver' => 'Provider Type',
                    'status' => 'Status',
                    'priority' => 'Priority',
                    'created-at' => 'Created At',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'edit' => 'Edit',
                    'delete' => 'Delete',
                ],
            ],
        ],
    ]
];
