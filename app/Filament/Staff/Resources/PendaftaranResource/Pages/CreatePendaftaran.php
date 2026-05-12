<?php

namespace App\Filament\Staff\Resources\PendaftaranResource\Pages;

use Filament\Actions;
use App\Models\Customer;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Staff\Resources\PendaftaranResource;

class CreatePendaftaran extends CreateRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['password'] = bcrypt($data['password']);

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }


    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->record;

        // Pastikan role string valid
        $role = match ($user->role) {
            'administrator' => 'administrator',
            'staff'         => 'staff',
            default         => 'customer',
        };

        // Sync role Spatie
        // $user->syncRoles([$role]);

        // Auto create customer jika customer
        if ($role === 'customer') {
            Customer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_ktp' => $user->name,
                    'no_hp'    => $user->phone,
                ]
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
