<x-admin::layouts>
    <x-slot:title>
        Call Recordings
    </x-slot:title>

    <div class="flex items-start justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                Call Recordings
            </p>
            <p class="text-sm text-gray-500 mt-1">Browse, play and download call recordings</p>
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.voip.recordings.index')" class="mt-6">
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </x-admin::datagrid>

</x-admin::layouts>
