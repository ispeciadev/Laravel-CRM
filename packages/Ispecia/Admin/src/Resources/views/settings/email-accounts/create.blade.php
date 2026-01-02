<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.settings.email-accounts.create.title')
    </x-slot>

    <x-admin::form
        :action="route('admin.settings.email_accounts.store')"
        method="POST"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="settings.email_accounts.create" />
                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.settings.email-accounts.create.title')
                    </div>
                </div>

                <button type="submit" class="primary-button">
                    @lang('admin::app.settings.email-accounts.create.save-btn')
                </button>
            </div>

            <div class="flex gap-2.5 max-xl:flex-wrap">
                <!-- Left Panel -->
                <div class="flex flex-1 flex-col gap-2">
                    <div class="box-shadow rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.email-accounts.create.smtp-settings')
                        </p>

                        <!-- Email -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.email')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="email"
                                name="email"
                                rules="required|email"
                                :label="trans('admin::app.settings.email-accounts.create.email')"
                            />
                            <x-admin::form.control-group.error control-name="email" />
                        </x-admin::form.control-group>

                        <!-- Host -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.host')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="host"
                                rules="required"
                                value="smtp.gmail.com"
                                :label="trans('admin::app.settings.email-accounts.create.host')"
                            />
                            <x-admin::form.control-group.error control-name="host" />
                        </x-admin::form.control-group>

                        <!-- Port -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.port')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="number"
                                name="port"
                                rules="required"
                                value="587"
                                :label="trans('admin::app.settings.email-accounts.create.port')"
                            />
                            <x-admin::form.control-group.error control-name="port" />
                        </x-admin::form.control-group>

                        <!-- Username -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.username')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="username"
                                rules="required"
                                :label="trans('admin::app.settings.email-accounts.create.username')"
                            />
                            <x-admin::form.control-group.error control-name="username" />
                        </x-admin::form.control-group>

                        <!-- Password -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.password')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="password"
                                name="password"
                                rules="required"
                                :label="trans('admin::app.settings.email-accounts.create.password')"
                            />
                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>

                        <!-- Encryption -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.email-accounts.create.encryption')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="select"
                                name="encryption"
                                rules="required"
                                value="tls"
                                :label="trans('admin::app.settings.email-accounts.create.encryption')"
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
                                @lang('admin::app.settings.email-accounts.create.general')
                            </p>
                        </x-slot>

                        <x-slot:content>
                            <!-- Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.email-accounts.create.name')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="name"
                                    rules="required"
                                    :label="trans('admin::app.settings.email-accounts.create.name')"
                                />
                                <x-admin::form.control-group.error control-name="name" />
                            </x-admin::form.control-group>

                            <!-- From Name -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.create.from-name')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="from_name"
                                    :label="trans('admin::app.settings.email-accounts.create.from-name')"
                                />
                            </x-admin::form.control-group>

                            <!-- Default CC -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.create.default-cc')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="default_cc"
                                    :label="trans('admin::app.settings.email-accounts.create.default-cc')"
                                    :placeholder="trans('admin::app.settings.email-accounts.create.default-cc-placeholder')"
                                />
                            </x-admin::form.control-group>

                            <!-- Default BCC -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.email-accounts.create.default-bcc')
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="default_bcc"
                                    :label="trans('admin::app.settings.email-accounts.create.default-bcc')"
                                    :placeholder="trans('admin::app.settings.email-accounts.create.default-bcc-placeholder')"
                                />
                            </x-admin::form.control-group>

                            <!-- Is Default -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    name="is_default"
                                    value="1"
                                    :label="trans('admin::app.settings.email-accounts.create.is-default')"
                                />
                            </x-admin::form.control-group>

                            <!-- Is Active -->
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.control
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    checked
                                    :label="trans('admin::app.settings.email-accounts.create.is-active')"
                                />
                            </x-admin::form.control-group>
                        </x-slot>
                    </x-admin::accordion>
                </div>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
