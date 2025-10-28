<div class="w-full px-2 py-1" title="{{ $getRecord()->product->name }}" x-tooltip.raw="{{ $getRecord()->product->name }}">
    <div class="flex flex-row items-center space-x-2">



        @if ($getRecord()->product->image)
            <img class ="w-8 h-8 object-contain" src="{{ asset('storage/' . $getRecord()->product->image) }}"
                alt="{{ $getRecord()->product->name }}" />
        @endif

        <div class="flex flex-col w-56">
            {{-- <div class="text-sm text-gray-500">{{ $getRecord()->product->sku }}</div> --}}
            @if ($getRecord()->product->brand)
                <div class="text-sm text-gray-500">
                    {{ $getRecord()->product->brand }}
                </div>
            @endif
            <div class="font-bold text-xs font-mono">

                {{ Illuminate\Support\Str::limit($getRecord()->product->name, 20) }}
            </div>
            <div>
                @if ($getRecord()->product->format)
                    <div class="text-xs text-amber-500 ">
                        {{ __('common.format') . ': ' . $getRecord()->product->format }}
                    </div>
                @endif
            </div>


        </div>
        <div class="font-bold text-xs text-red-800 text-right pr-10">
            {{ $getRecord()->product->price . ' €' }}
        </div>
    </div>
</div>
