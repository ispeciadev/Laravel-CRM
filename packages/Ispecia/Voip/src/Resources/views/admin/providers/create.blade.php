<x-admin::layouts>
    <x-slot:title>
        {{ trans('voip::app.admin.providers.create-title') }}
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            {{ trans('voip::app.admin.providers.create-title') }}
        </p>

        <div class="flex items-center gap-x-2.5">
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
        action="{{ route('admin.voip.providers.store') }}"
        class="mt-8"
        x-data="providerForm()"
    >
        @csrf
        
        <div class="bg-white dark:bg-gray-900 rounded box-shadow">
            <div class="p-4 border-b dark:border-gray-800">
                <p class="text-base text-gray-800 dark:text-white font-semibold">
                    Provider Configuration
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
                        value="{{ old('name') }}"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                        placeholder="e.g., Twilio Production"
                    />
                    @error('name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Driver -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        Provider Type
                    </label>
                    <select
                        name="driver"
                        x-model="driver"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                    >
                        <option value="">Select Provider</option>
                        @foreach($drivers as $key => $driverInfo)
                            <option value="{{ $key }}">{{ $driverInfo['name'] }}</option>
                        @endforeach
                    </select>
                    @error('driver')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Twilio Fields -->
                <div x-show="driver === 'twilio'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Account SID
                        </label>
                        <input
                            type="text"
                            name="config[account_sid]"
                            value="{{ old('config.account_sid') }}"
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
                            value="{{ old('config.auth_token') }}"
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
                            value="{{ old('config.api_key_sid') }}"
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
                            value="{{ old('config.api_key_secret') }}"
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
                            value="{{ old('config.app_sid') }}"
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
                            value="{{ old('config.from_number') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="+1234567890"
                        />
                    </div>
                </div>

                <!-- Telnyx Fields -->
                <div x-show="driver === 'telnyx'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            API Key
                        </label>
                        <input
                            type="password"
                            name="config[api_key]"
                            value="{{ old('config.api_key') }}"
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
                            value="{{ old('config.connection_id') }}"
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
                            value="{{ old('config.from_number') }}"
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
                            value="{{ old('config.webhook_api_secret') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="••••••••••••••••••••••••••••••••"
                        />
                    </div>
                </div>

                <!-- SIP Fields -->
                <div x-show="driver === 'sip'" x-cloak>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            SIP Server
                        </label>
                        <input
                            type="text"
                            name="config[sip_server]"
                            value="{{ old('config.sip_server') }}"
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
                            value="{{ old('config.sip_port', '5060') }}"
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
                            value="{{ old('config.username') }}"
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
                            value="{{ old('config.password') }}"
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
                            <option value="udp">UDP</option>
                            <option value="tcp">TCP</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="primary-button">
                        {{ trans('voip::app.admin.providers.save-btn') }}
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
                driver: '{{ old('driver') }}'
            }
        }
    </script>
@endpush
