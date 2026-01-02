<v-sidebar-drawer>
    <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
</v-sidebar-drawer>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-sidebar-drawer-template"
    >
        <x-admin::drawer
            position="left"
            width="280px"
            class="lg:hidden [&>:nth-child(3)]:!m-0 [&>:nth-child(3)]:!rounded-l-none [&>:nth-child(3)]:max-sm:!w-[80%]"
        >
            <x-slot:toggle>
                <i class="icon-menu lg:hidden cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-100 dark:hover:bg-gray-950 max-lg:block"></i>
            </x-slot>

            <x-slot:header>
                <div class="flex items-center gap-2">
                    <span class="sr-only">{{ config('app.name') }}</span>
                    <span class="text-lg font-semibold lowercase text-gray-800 dark:text-white">ispecia</span>
                </div>
            </x-slot>

            <x-slot:content class="p-4">
                <div class="journal-scroll h-[calc(100vh-100px)] overflow-auto">
                    <nav class="grid w-full gap-2">
                        @foreach (menu()->getItems('admin') as $menuItem)
                            @php
                                $hasActiveChild = $menuItem->haveChildren() && collect($menuItem->getChildren())->contains(fn($child) => $child->isActive());

                                $isMenuActive = $menuItem->isActive() == 'active' || $hasActiveChild;

                                $menuKey = $menuItem->getKey();
                            @endphp

                            <div
                                class="menu-item relative"
                                data-menu-key="{{ $menuKey }}"
                            >
                                <a
                                    href="{{ ! in_array($menuItem->getKey(), ['settings', 'configuration']) && $menuItem->haveChildren() ? 'javascript:void(0)' : $menuItem->getUrl() }}"
                                    class="menu-link flex items-center justify-between rounded-lg p-2 transition-colors duration-200"
                                    @if ($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration']))
                                        @click.prevent="toggleMenu('{{ $menuKey }}')"
                                    @endif
                                    :class="{ 'bg-brandColor text-white': activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-950': !(activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}) }"
                                >
                                    <div class="flex items-center gap-3">
                                        @php $mobileIcon = $menuItem->getIcon(); @endphp
                                        @if ($mobileIcon === 'icon-phone')
                                            <svg class="text-2xl" :class="{ 'text-white': activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}, 'text-gray-700 dark:text-gray-300': !(activeMenu === '{{ $menuKey }}' || {{ $isMenuActive ? 'true' : 'false' }}) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.05.37 2.07.72 3.03a2 2 0 0 1-.45 2.11L9.91 10.09a16 16 0 0 0 6 6l1.23-1.23a2 2 0 0 1 2.11-.45c.96.35 1.98.6 3.03.72A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                        @else
                                            <span class="{{ $mobileIcon }} text-2xl"></span>
                                        @endif

                                        <p class="whitespace-nowrap font-semibold">{{ $menuItem->getName() }}</p>
                                    </div>

                                    @if ($menuItem->haveChildren())
                                        <span
                                            class="transform text-lg transition-transform duration-300"
                                            :class="{ 'icon-arrow-up': activeMenu === '{{ $menuKey }}', 'icon-arrow-down': activeMenu !== '{{ $menuKey }}' }"
                                        ></span>
                                    @endif
                                </a>

                                @if ($menuItem->haveChildren() && !in_array($menuKey, ['settings', 'configuration']))
                                    <div
                                        class="submenu ml-1 mt-1 overflow-hidden rounded-b-lg border-l-2 transition-all duration-300 dark:border-gray-700"
                                        :class="{ 'max-h-[500px] py-2 border-l-brandColor bg-gray-50 dark:bg-gray-900': activeMenu === '{{ $menuKey }}' || {{ $hasActiveChild ? 'true' : 'false' }}, 'max-h-0 py-0 border-transparent bg-transparent': activeMenu !== '{{ $menuKey }}' && !{{ $hasActiveChild ? 'true' : 'false' }} }"
                                    >
                                        @foreach ($menuItem->getChildren() as $subMenuItem)
                                            <a
                                                href="{{ $subMenuItem->getUrl() }}"
                                                class="submenu-link block whitespace-nowrap p-2 pl-10 text-sm transition-colors duration-200"
                                                :class="{ 'text-brandColor font-medium bg-gray-100 dark:bg-gray-800': '{{ $subMenuItem->isActive() }}' === '1', 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800': '{{ $subMenuItem->isActive() }}' !== '1' }">
                                                {{ $subMenuItem->getName() }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>
                </div>
            </x-slot>
        </x-admin::drawer>
    </script>

    <script type="module">
        app.component('v-sidebar-drawer', {
            template: '#v-sidebar-drawer-template',

            data() {
                return { activeMenu: null };
            },

            mounted() {
                const activeElement = document.querySelector('.menu-item .menu-link.bg-brandColor');

                if (activeElement) {
                    this.activeMenu = activeElement.closest('.menu-item').getAttribute('data-menu-key');
                }
            },

            methods: {
                toggleMenu(menuKey) {
                    this.activeMenu = this.activeMenu === menuKey ? null : menuKey;
                }
            },
        });
    </script>
@endPushOnce
