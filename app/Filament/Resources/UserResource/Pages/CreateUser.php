<?php

namespace App\Filament\Resources\UserResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Customer;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = Carbon::now();

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
