<div>
    @if ($getRecord()->product->image)
        
        <div>
            <img width="80" height="80" src="{{ asset('storage/' . $getRecord()->product->image) }}"
                alt="{{ $getRecord()->product->name }}" />
        </div>
    @endif

    <div>
         @if ($getRecord()->product->brand)
            <div class="text-sm text-gray-500">
                {{ $getRecord()->product->brand}}
            </div>
        @endif
        <div  class="font-bold text-xs mt-2">
             {{ Illuminate\Support\Str::limit($getRecord()->product->name, 20) }}
        </div>
       
    </div>
    <div class="font-bold text-lg mt-2">
        {{ $getRecord()->product->price . ' €' }}
    </div>

</div>
