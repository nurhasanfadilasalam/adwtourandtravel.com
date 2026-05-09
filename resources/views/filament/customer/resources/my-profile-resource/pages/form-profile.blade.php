<x-filament-panels::page style="margin-bottom: 500px">
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        {{-- Floating Save Button --}}
        <div class="flex justify-end mt-6 mb-12">
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="false"
            />
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
<script>
    document.addEventListener('livewire:init', function () {
        Livewire.on('customerProfileUpdated', function () {
            window.location.reload();
        });
    });
</script>


{{-- <x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="flex justify-end mt-6">
            <x-filament::button type="submit">
                Simpan Profile
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page> --}}

