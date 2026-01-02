<x-admin::layouts>
    <x-slot:title>
        Create Deal
    </x-slot>

    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-2">
                <x-admin::breadcrumbs name="deals.create" />

                <div class="text-xl font-bold dark:text-white">Create Deal</div>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new deal to your sales pipeline</p>
            </div>

            <div class="flex items-center gap-x-2.5">
                <a href="{{ route('admin.deals.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 p-6">
            <form method="POST" action="{{ route('admin.deals.store') }}" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        Deal Title <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        placeholder="Enter deal title"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                        required
                        value="{{ old('title') }}"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                        Description
                    </label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        placeholder="Add deal details..."
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Deal Value -->
                    <div>
                        <label for="deal_value" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Deal Value <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-500 dark:text-gray-400">$</span>
                            <input 
                                type="number" 
                                name="deal_value" 
                                id="deal_value"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full pl-8 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                                required
                                value="{{ old('deal_value') }}"
                            >
                        </div>
                        @error('deal_value')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Owner -->
                    <div>
                        <label for="user_id" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Deal Owner <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="user_id" 
                            id="user_id"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                            required
                        >
                            <option value="">Select owner...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expected Close Date -->
                    <div>
                        <label for="expected_close_date" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Expected Close Date
                        </label>
                        <input 
                            type="date" 
                            name="expected_close_date" 
                            id="expected_close_date"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                            value="{{ old('expected_close_date') }}"
                        >
                        @error('expected_close_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Status
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:focus:border-blue-400"
                        >
                            <option value="open" {{ old('status', 'open') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="won" {{ old('status') == 'won' ? 'selected' : '' }}>Won</option>
                            <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a 
                        href="{{ route('admin.deals.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-6 py-2.5 font-semibold text-gray-900 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 font-semibold text-white transition-all hover:bg-blue-700 active:scale-95"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Deal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts>
