<x-filament::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($stats as $stat)
            <x-filament::card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <p class="text-3xl font-bold text-{{ $stat['color'] }}-600">
                            {{ $stat['value'] }}
                        </p>

                        @isset($stat['description'])
                            <p class="mt-1 text-sm text-gray-400">
                                {{ $stat['description'] }}
                            </p>
                        @endisset
                    </div>

                    @isset($stat['icon'])
                        <x-dynamic-component
                            :component="$stat['icon']"
                            class="h-10 w-10 text-{{ $stat['color'] }}-500"
                        />
                    @endisset
                </div>
            </x-filament::card>
        @endforeach
    </div>
</x-filament::page>
