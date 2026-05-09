<x-filament-panels::page>

    <x-filament::card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div>
                <p class="text-sm text-gray-500">Paket Umroh</p>
                <p class="text-lg font-bold text-primary-600">
                    {{ $this->namaPaket }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Periode Keberangkatan</p>
                <p class="text-lg font-semibold">
                    {{ $this->periode }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Total Customer</p>
                <p class="text-2xl font-bold text-success-600">
                    {{ $this->totalCustomer }} Org
                </p>
            </div>

        </div>
    </x-filament::card>

    {{ $this->table }}

</x-filament-panels::page>
