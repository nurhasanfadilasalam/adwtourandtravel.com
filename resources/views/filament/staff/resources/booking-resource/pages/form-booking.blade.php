<x-filament-panels::page>
    <form method="post" wire:submit="save">
        {{ $this->form }}


        <div  class="flex justify-end mt-6 mb-12">
            <x-filament::button type="submit" class="mt-6 mb-12">
                Pesan/Booking Paket Umroh
            </x-filament::button>
        </div>

    </form>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
</x-filament-panels::page>
