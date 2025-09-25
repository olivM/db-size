<div class="w-1/2 text-white">
    @foreach ($tables as $table)
        <div class="flex w-full">
            <span class="">{{ $table->Table }}</span>
            <span class="flex-1 text-right">
                {{ $table->Size }} MB
                {{-- <span class="text-red">{{ $table->Percentage }}%</span> --}}
                <span class="text-green">{{ str_repeat('#', floor($table->Percentage)) }}</span></span>
            </span>
        </div>
    @endforeach
</div>
