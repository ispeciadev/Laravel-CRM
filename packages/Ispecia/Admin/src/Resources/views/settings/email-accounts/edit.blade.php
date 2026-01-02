<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.email-accounts.edit.title')
    </x-slot>

    <x-admin::form
        :action="route('admin.settings.email_accounts.update', $emailAccount->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="settings.email_accounts.edit" />
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.email-accounts.edit.title')
                    </div>
                </div>

                <button type="submit" class="primary-button">
                    @lang('admin::app.settings.email-accounts.edit.save-btn')
                </button>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left Panel -->
                <div class="flex flex-1 flex-col gap-2">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.email-accounts.edit.smtp-settings')
                        </p>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.edit.email')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="email"
                                name="email"
                                rules="required|email"
                                :value="$emailAccount->email"
                                :label="trans('admin::app.settings.email-accounts.edit.email')"
                            />
                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.edit.host')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="host"
                                rules="required"
                                :value="$emailAccount->host"
                                :label="trans('admin::app.settings.email-accounts.edit.host')"
                            />
                            <x-admin::form.control-group.error control-name="host" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.edit.port')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="number"
                                name="port"
                                rules="required"
                                :value="$emailAccount->port"
                                :label="trans('admin::app.settings.email-accounts.edit.port')"
                            />
                            <x-admin::form.control-group.error control-name="port" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.edit.username')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="username"
                                rules="required"
                                :value="$emailAccount->username"
                                :label="trans('admin::app.settings.email-accounts.edit.username')"
                            />
                            <x-admin::form.control-group.error control-name="username" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.settings.email-accounts.edit.password')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="password"
                                name="password"
                                :label="trans('admin::app.settings.email-accounts.edit.password')"
                                :placeholder="trans('admin::app.settings.email-accounts.edit.password-placeholder')"
                            />
                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.edit.encryption')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="select"
                                name="encryption"
                                rules="required"
                                :value="$emailAccount->encryption"
                                :label="trans('admin::app.settings.email-accounts.edit.encryption')"
                            >
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </x-admin::form.control-group.control>
                            <x-admin::form.control-group.error control-name="encryption" />
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                    <x-admin::accordion>
                        <x-slot:header>
                            <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.settings.email-accounts.edit.general')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.email-accounts.edit.name')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    rules="required"
                                    :value="$emailAccount->name"
                                    :label="trans('admin::app.settings.email-accounts.edit.name')"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.edit.from-name')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="from_name"
                                    :value="$emailAccount->from_name"
                                    :label="trans('admin::app.settings.email-accounts.edit.from-name')"
                                />
                            </x-admin::form.control-group>

                            <!-- Default CC -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.edit.default-cc')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="default_cc"
                                    :value="$emailAccount->default_cc"
                                    :label="trans('admin::app.settings.email-accounts.edit.default-cc')"
                                    :placeholder="trans('admin::app.settings.email-accounts.edit.default-cc-placeholder')"
                                />
                            </x-admin::form.control-group>

                            <!-- Default BCC -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.edit.default-bcc')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="default_bcc"
                                    :value="$emailAccount->default_bcc"
                                    :label="trans('admin::app.settings.email-accounts.edit.default-bcc')"
                                    :placeholder="trans('admin::app.settings.email-accounts.edit.default-bcc-placeholder')"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    name="is_default"
                                    value="1"
                                    :checked="$emailAccount->is_default"
                                    :label="trans('admin::app.settings.email-accounts.edit.is-default')"
                                />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    :checked="$emailAccount->is_active"
                                    :label="trans('admin::app.settings.email-accounts.edit.is-active')"
                                />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
