<div>
    <x-filament::badge color="success">
        <div class="flex items-center">

            <div>
                <img width="50" height="50" src="{{ asset('storage/' . $getRecord()->market->logo) }}"
                    alt="{{ $getRecord()->market->name }}" />
            </div>

            <div class="font-medium text-gray-900">
                {{ $getRecord()->market->name }}
                {{-- {{ $getState() }} --}}
            </div>


    </x-filament::badge>
</div>
