<x-admin::layouts>
    <x-slot:title>
        VoIP Trunks
    </x-slot:title>

    <div class="flex items-start justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                VoIP Trunks
            </p>
            <p class="text-sm text-gray-500 mt-1">Manage your VoIP trunk connections</p>
        </div>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.voip.trunks.create') }}"
                class="primary-button"
            >
                Create Trunk
            </a>
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.voip.trunks.index')" class="mt-6">
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </x-admin::datagrid>

</x-admin::layouts>
