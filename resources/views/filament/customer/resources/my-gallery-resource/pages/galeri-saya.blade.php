<x-filament-panels::page>
    <form method="post" wire:submit="save">
        {{ $this->form }}

        <div  class="flex justify-end mt-6 mb-12">
            <x-filament::button type="submit" class="mt-6 mb-12">
                Upload Image Galeri
            </x-filament::button>
        </div>

    </form>

</x-filament-panels::page>
