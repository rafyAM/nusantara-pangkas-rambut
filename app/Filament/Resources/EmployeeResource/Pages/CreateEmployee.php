<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected static ?string $title = 'Tambah Karyawan';

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $data['password'],
        ]);

        $this->assignRoleToUser($user, $data['position']);

        if (isset($data['branch_id'])) {
            $user->branches()->syncWithoutDetaching([$data['branch_id']]);
        }

        unset($data['password']);

        $data['user_id'] = $user->id;

        return static::getModel()::create($data);
    }

    protected function assignRoleToUser(User $user, string $position): void
    {
        $roleMapping = [
            'manager' => 'admin',
            'cashier' => 'cashier',
        ];

        if (isset($roleMapping[$position])) {
            $user->syncRoles($roleMapping[$position]);
        } else {
            $user->syncRoles([]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
