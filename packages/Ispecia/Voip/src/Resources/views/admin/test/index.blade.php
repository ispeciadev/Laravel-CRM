<x-admin::layouts>
    <x-slot:title>
        VoIP Test
    </x-slot>

    <div class="p-4">
        <h2 class="text-xl font-semibold mb-4">VoIP Test / Debug</h2>

        @if (session('voip_test'))
            @php $r = session('voip_test'); @endphp
            <div class="mb-4 p-3 rounded bg-white border">
                <pre style="white-space:pre-wrap;word-break:break-word;">{{ print_r($r, true) }}</pre>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.voip.test.run') }}">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Phone number to test (E.164 preferred)</label>
                <input type="text" name="to_number" class="mt-1 block w-full rounded border p-2" placeholder="+15551234567" />
            </div>

            <div class="mb-3">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="do_call" value="1" class="mr-2" />
                    <span>Also initiate an outbound test call (server-side)</span>
                </label>
            </div>

            <div>
                <button class="primary-button">Run Test</button>
            </div>
        </form>
    </div>
</x-admin::layouts>
