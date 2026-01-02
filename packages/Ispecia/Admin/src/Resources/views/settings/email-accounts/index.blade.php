<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.settings.email-accounts.index.title')
    </x-slot>

    <div class="flex flex-col gap-4"> 
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <!-- Breadcrumbs -->
                <x-admin::breadcrumbs name="settings.email_accounts" />

                <div class="text-xl font-bold dark:text-white">
                    @lang('admin::app.settings.email-accounts.index.title')
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                <!-- Create button -->
                <div class="flex items-center gap-x-2.5">
                    @if (bouncer()->hasPermission('settings.email_accounts.create'))
                        <a
                            href="{{ route('admin.settings.email_accounts.create') }}"
                            class="primary-button"
                        >
                            @lang('admin::app.settings.email-accounts.index.create-btn')
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- DataGrid -->
        <x-admin::datagrid :src="route('admin.settings.email_accounts.index')">
            <!-- DataGrid Shimmer -->
            <x-admin::shimmer.datagrid />
        </x-admin::datagrid>
    </div>
</x-admin::layouts>
