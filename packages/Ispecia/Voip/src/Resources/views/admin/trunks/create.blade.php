<x-admin::layouts>
    <x-slot:title>
        Create VoIP Trunk
    </x-slot:title>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            Create VoIP Trunk
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.voip.trunks.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                Back
            </a>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('admin.voip.trunks.store') }}"
        class="mt-8"
        x-data="{
            authMethod: 'username_password',
            showPassword: false
        }"
    >
        @csrf
        
        <!-- General Information Section -->
        <div class="bg-white dark:bg-gray-900 rounded box-shadow mb-6">
            <div class="p-4 border-b dark:border-gray-800">
                <p class="text-base text-gray-800 dark:text-white font-semibold">
                    General Information
                </p>
            </div>

            <div class="p-4">
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        Trunk Name
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                        placeholder="My SIP Trunk"
                    />
                    @error('name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Provider -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">Provider</label>
                     <select
                        name="voip_provider_id"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                    >
                        <option value="">Custom (No Provider)</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('voip_provider_id') == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }} ({{ ucfirst($provider->driver) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select a provider or leave as Custom</p>
                </div>
            </div>
        </div>

        <!-- SIP / Trunk Settings Section -->
        <div class="bg-white dark:bg-gray-900 rounded box-shadow">
            <div class="p-4 border-b dark:border-gray-800">
                <p class="text-base text-gray-800 dark:text-white font-semibold">
                    SIP / Trunk Settings
                </p>
            </div>

            <div class="p-4">
                <!-- SIP Domain / Host -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        SIP Domain / Host
                    </label>
                    <input
                        type="text"
                        name="host"
                        value="{{ old('host') }}"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                        placeholder="e.g., sip.twilio.com, sip.telnyx.com, 192.168.1.100"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the SIP server domain or IP address</p>
                    @error('host')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- SIP Port -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">SIP Port</label>
                    <input
                        type="number"
                        name="port"
                        value="{{ old('port', 5060) }}"
                        min="1"
                        max="65535"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Standard SIP port is 5060</p>
                    @error('port')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transport Protocol -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">Transport Protocol</label>
                    <select
                        name="transport"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                    >
                        <option value="udp" {{ old('transport', 'udp') == 'udp' ? 'selected' : '' }}>UDP</option>
                        <option value="tcp" {{ old('transport') == 'tcp' ? 'selected' : '' }}>TCP</option>
                        <option value="tls" {{ old('transport') == 'tls' ? 'selected' : '' }}>TLS</option>
                    </select>
                    @error('transport')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auth Method -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                        Authentication Method
                    </label>
                    <select
                        name="auth_method"
                        x-model="authMethod"
                        class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                    >
                        <option value="username_password" {{ old('auth_method', 'username_password') == 'username_password' ? 'selected' : '' }}>Username/Password</option>
                        <option value="ip_auth" {{ old('auth_method') == 'ip_auth' ? 'selected' : '' }}>IP Authentication</option>
                    </select>
                    @error('auth_method')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username/Password Fields (conditional) -->
                <div x-show="authMethod === 'username_password'" x-transition>
                    <!-- SIP Username -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            SIP Username
                        </label>
                        <input
                            type="text"
                            name="sip_username"
                            value="{{ old('sip_username') }}"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                            placeholder="username"
                        />
                        @error('sip_username')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SIP Password -->
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            SIP Password
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="sip_password"
                                value="{{ old('sip_password') }}"
                                class="w-full py-2.5 px-3 pr-10 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 6 characters</p>
                        @error('sip_password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- IP Authentication Fields (conditional) -->
                <div x-show="authMethod === 'ip_auth'" x-transition>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-800 dark:text-white font-medium mb-1.5">
                            Allowed IPs / Subnets
                        </label>
                        <textarea
                            name="allowed_ips"
                            rows="4"
                            class="w-full py-2.5 px-3 border rounded-md text-sm text-gray-600 dark:text-gray-300 dark:bg-gray-900 dark:border-gray-800 font-mono"
                            placeholder="123.45.67.89&#10;10.0.0.0/24&#10;192.168.1.0/24"
                        >{{ old('allowed_ips') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter one IP address or subnet per line</p>
                        @error('allowed_ips')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <button type="submit" class="primary-button">Save Trunk</button>
            </div>
        </div>
    </form>
</x-admin::layouts>
