<x-admin::layouts>
    <x-slot:title>
        Inbound Routes
    </x-slot:title>

    <div class="flex items-start justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                Inbound Routes
            </p>
            <p class="text-sm text-gray-500 mt-1">Manage incoming call routing rules</p>
        </div>

        <div class="flex items-center gap-x-2.5">
             <a href="{{ route('admin.voip.routes.create') }}" class="primary-button">Create Inbound Route</a>
        </div>
    </div>
    
    <x-admin::datagrid :src="route('admin.voip.routes.index')" class="mt-6">
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </x-admin::datagrid>

</x-admin::layouts>
