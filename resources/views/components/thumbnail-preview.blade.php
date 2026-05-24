@props([
    'src' => null,
    // 'label' => 'Thumbnail',
    'height' => '22rem',
])

<x-filament::card class="w-full">
    <div class="space-y-3">
        {{-- Label --}}
        {{-- <div class="text-sm font-medium text-gray-600 dark:text-gray-300">
            {{ $label }}
        </div> --}}

        {{-- Content --}}
     @if ($src)
            <div
                class="relative w-full overflow-hidden rounded-lg border bg-gray-50 dark:bg-gray-900"
                style="height: {{ $height }}"
            >
                <img
                    src="{{ asset('storage/' . $src) }}"
                    alt="Thumbnail"
                    class="absolute inset-0 w-full h-full object-contain"
                    loading="lazy"
                >
            </div>
        @else
            <div
                class="flex items-center justify-center rounded-lg border text-sm text-gray-400 italic"
                style="height: {{ $height }}" >
                Tidak ada thumbnail
            </div>
        @endif
    </div>
</x-filament::card>
