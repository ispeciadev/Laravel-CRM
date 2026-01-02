<x-admin::layouts>
    <x-slot:title>
        {{ trans('voip::app.admin.providers.edit-title') }}
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            {{ trans('voip::app.admin.providers.edit-title') }}
        </p>

        <div class="flex items-center gap-x-2.5">
            @if(!$provider->is_active)
                <button
                    type="button"
                    onclick="activateProvider({{ $provider->id }})"
                    class="secondary-button"
                >
                    Set as Active
                </button>
            @endif

            <button
                type="button"
                onclick="testProvider({{ $provider->id }})"
                class="secondary-button"
            >
                {{ trans('voip::app.admin.providers.test-connection') }}
            </button>

            <a
                href="{{ route('admin.voip.providers.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                Back
            </a>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('admin.voip.providers.update', $provider->id) }}"
        class="mt-8"
        x-data="providerForm()"
    >
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-900 rounded box-shadow">
            <div class="p-4 border-b dark:border-gray-800">
                <p class="text-base text-gray-800 dark:text-white font-semibold">
                    Provider Configuration
                    @if($provider->is_active)
                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Active</span>
                    @endif
                </p>
            </div>

            <div class="p-4">
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        Provider Name
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $provider->name) }}"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                        placeholder="e.g., Twilio Production"
                    />
                    @error('name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Driver (Read-only) -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        Provider Type
                    </label>
                    <input
                        type="text"
                        value="{{ $provider->getDriverDisplayName() }}"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-400 dark:text-gray-500 dark:bg-gray-900 dark:border-gray-800"
                        readonly
                    />
                    <input type="hidden" name="driver" value="{{ $provider->driver }}">
                    <p class="text-xs text-gray-500 mt-1">Provider type cannot be changed after creation</p>
                </div>

                <!-- Twilio Fields -->
                @if($provider->driver === 'twilio')
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Account SID
                        </label>
                        <input
                            type="text"
                            name="config[account_sid]"
                            value="{{ old('config.account_sid', $provider->config['account_sid'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Auth Token
                        </label>
                        <input
                            type="password"
                            name="config[auth_token]"
                            value="{{ old('config.auth_token', $provider->config['auth_token'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="••••••••••••••••••••••••••••••••"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            API Key SID (Optional)
                        </label>
                        <input
                            type="text"
                            name="config[api_key_sid]"
                            value="{{ old('config.api_key_sid', $provider->config['api_key_sid'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="SKxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            API Key Secret (Optional)
                        </label>
                        <input
                            type="password"
                            name="config[api_key_secret]"
                            value="{{ old('config.api_key_secret', $provider->config['api_key_secret'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="••••••••••••••••••••••••••••••••"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            TwiML App SID (Optional)
                        </label>
                        <input
                            type="text"
                            name="config[app_sid]"
                            value="{{ old('config.app_sid', $provider->config['app_sid'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="APxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5 required">
                            From Number
                        </label>
                        <input
                            type="text"
                            name="config[from_number]"
                            value="{{ old('config.from_number', $provider->config['from_number'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="+1234567890"
                        />
                    </div>
                @endif

                <!-- Telnyx Fields -->
                @if($provider->driver === 'telnyx')
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            API Key
                        </label>
                        <input
                            type="password"
                            name="config[api_key]"
                            value="{{ old('config.api_key', $provider->config['api_key'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="KEYxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Connection ID
                        </label>
                        <input
                            type="text"
                            name="config[connection_id]"
                            value="{{ old('config.connection_id', $provider->config['connection_id'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="conn_xxxxxxxxxxxxxxxxxxxx"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5 required">
                            From Number
                        </label>
                        <input
                            type="text"
                            name="config[from_number]"
                            value="{{ old('config.from_number', $provider->config['from_number'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="+1234567890"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Webhook API Secret (Optional)
                        </label>
                        <input
                            type="password"
                            name="config[webhook_api_secret]"
                            value="{{ old('config.webhook_api_secret', $provider->config['webhook_api_secret'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="••••••••••••••••••••••••••••••••"
                        />
                    </div>
                @endif

                <!-- SIP Fields -->
                @if($provider->driver === 'sip')
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            SIP Server
                        </label>
                        <input
                            type="text"
                            name="config[sip_server]"
                            value="{{ old('config.sip_server', $provider->config['sip_server'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="sip.example.com"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            SIP Port
                        </label>
                        <input
                            type="number"
                            name="config[sip_port]"
                            value="{{ old('config.sip_port', $provider->config['sip_port'] ?? '5060') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="5060"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Username
                        </label>
                        <input
                            type="text"
                            name="config[username]"
                            value="{{ old('config.username', $provider->config['username'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="1001"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Password
                        </label>
                        <input
                            type="password"
                            name="config[password]"
                            value="{{ old('config.password', $provider->config['password'] ?? '') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="••••••••••••••••"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Transport
                        </label>
                        <select
                            name="config[transport]"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                        >
                            <option value="udp" {{ ($provider->config['transport'] ?? 'udp') === 'udp' ? 'selected' : '' }}>UDP</option>
                            <option value="tcp" {{ ($provider->config['transport'] ?? '') === 'tcp' ? 'selected' : '' }}>TCP</option>
                            <option value="tls" {{ ($provider->config['transport'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                        </select>
                    </div>
                @endif

                <div class="flex gap-2">
                    <button type="submit" class="primary-button">
                        {{ trans('voip::app.admin.providers.update-btn') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-admin::layouts>

@push('scripts')
    <script>
        function providerForm() {
            return {
                driver: '{{ $provider->driver }}'
            }
        }

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
                alert(data.message);
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to activate provider');
            });
        }

        function testProvider(id) {
            const button = event.target;
            const originalText = button.textContent;
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
                if (data.success) {
                    alert('✓ ' + data.message);
                } else {
                    alert('✗ ' + data.message);
                }
                button.disabled = false;
                button.textContent = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('✗ Test failed: Unable to connect to server');
                button.disabled = false;
                button.textContent = originalText;
            });
        }
    </script>
@endpush
