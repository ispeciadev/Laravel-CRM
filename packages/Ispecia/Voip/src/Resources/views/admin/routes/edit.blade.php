<x-admin::layouts>
    <x-slot:title>
        Edit Inbound Route
    </x-slot:title>

    <x-admin::form :action="route('admin.voip.routes.update', $route->id)" method="PUT">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                Edit Inbound Route
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ route('admin.voip.routes.index') }}"
                    class="transparent-button"
                >
                    @lang('admin::app.back')
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.save')
                </button>
            </div>
        </div>

        <div class="mt-7 max-w-4xl">
            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                    Route Information
                </p>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        Route Name
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="name"
                        rules="required"
                        :value="old('name', $route->name)"
                        :label="'Route Name'"
                        :placeholder="'Enter route name'"
                    />

                    <x-admin::form.control-group.error control-name="name" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        DID Pattern
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="pattern"
                        rules="required"
                        :value="old('pattern', $route->pattern)"
                        :label="'DID Pattern'"
                        :placeholder="'e.g., +15551234567 or 555*'"
                    />

                    <x-admin::form.control-group.error control-name="pattern" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        Destination Type
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        name="destination_type"
                        rules="required"
                        :value="old('destination_type', $route->destination_type)"
                        :label="'Destination Type'"
                    >
                        <option value="">Select Destination</option>
                        <option value="user" {{ $route->destination_type == 'user' ? 'selected' : '' }}>User</option>
                        <option value="queue" {{ $route->destination_type == 'queue' ? 'selected' : '' }}>Queue</option>
                        <option value="voicemail" {{ $route->destination_type == 'voicemail' ? 'selected' : '' }}>Voicemail</option>
                        <option value="hangup" {{ $route->destination_type == 'hangup' ? 'selected' : '' }}>Hangup</option>
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="destination_type" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        Destination ID
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="destination_id"
                        :value="old('destination_id', $route->destination_id)"
                        :label="'Destination ID'"
                        :placeholder="'User ID, Queue ID, etc.'"
                    />

                    <x-admin::form.control-group.error control-name="destination_id" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        Trunk
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="select"
                        name="voip_trunk_id"
                        :value="old('voip_trunk_id', $route->voip_trunk_id)"
                        :label="'Trunk'"
                    >
                        <option value="">No Specific Trunk</option>
                        @foreach($trunks as $trunk)
                            <option value="{{ $trunk->id }}" {{ $route->voip_trunk_id == $trunk->id ? 'selected' : '' }}>
                                {{ $trunk->name }}
                                @if($trunk->provider)
                                    ({{ $trunk->provider->name }})
                                @endif
                            </option>
                        @endforeach
                    </x-admin::form.control-group.control>

                    <x-admin::form.control-group.error control-name="voip_trunk_id" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        Priority
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="number"
                        name="priority"
                        :value="old('priority', $route->priority)"
                        :label="'Priority'"
                        :placeholder="'1'"
                    />

                    <x-admin::form.control-group.error control-name="priority" />
                </x-admin::form.control-group>

                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        Active
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="switch"
                        name="is_active"
                        :value="1"
                        :checked="$route->is_active"
                        :label="'Active'"
                    />

                    <x-admin::form.control-group.error control-name="is_active" />
                </x-admin::form.control-group>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
