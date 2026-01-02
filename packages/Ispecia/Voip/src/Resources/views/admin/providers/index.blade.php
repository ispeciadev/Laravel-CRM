<x-admin::layouts>
    <x-slot:title>
        {{ trans('voip::app.admin.providers.title') }}
    </x-slot:title>

    <div class="flex items-start justify-between gap-4 max-sm:flex-wrap">
        <div>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                {{ trans('voip::app.admin.providers.title') }}
            </p>
            <p class="text-sm text-gray-500 mt-1">Manage VoIP provider configurations</p>
        </div>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.voip.providers.create') }}"
                class="primary-button"
            >
                Add Provider
            </a>
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.voip.providers.index')" class="mt-6">
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />
    </x-admin::datagrid>

</x-admin::layouts>

@push('scripts')
    <script>
        function activateProvider(id) {
            if (!confirm('Are you sure you want to activate this provider? The currently active provider will be deactivated.')) {
                return;
            }

            fetch(`{{ url(config('app.admin_path')) }}/voip/providers/${id}/activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to activate provider');
            });
        }

        function testProvider(id) {
            const button = event.target;
            button.disabled = true;
            button.textContent = 'Testing...';

            fetch(`{{ url(config('app.admin_path')) }}/voip/providers/${id}/test`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                button.disabled = false;
                button.textContent = 'Test Connection';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Test failed');
                button.disabled = false;
                button.textContent = 'Test Connection';
            });
        }
    </script>
@endpush
