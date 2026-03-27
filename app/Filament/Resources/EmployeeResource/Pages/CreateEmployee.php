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
        // 1. Create or Find User
        // Use email to link if exists, otherwise create new
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => $data['password'], // Password already hashed in resource dehydrate
            ]
        );

        // 2. Assign Role based on Position
        $this->assignRoleToUser($user, $data['position']);

        // 3. Create Employee linked to User
        // Remove password from data as it's not in employees table
        unset($data['password']);

        $data['user_id'] = $user->id;

        return static::getModel()::create($data);
    }

    protected function assignRoleToUser(User $user, string $position): void
    {
        // Map position to role name
        // position keys: barber, cashier, manager
        // role names: customer (default?), cashier, admin (for manager?), super-admin

        // Let's assume:
        // manager -> admin
        // cashier -> cashier
        // barber -> employee (if role exists) or just no specific administrative role?
        // creating 'employee' role might be good later, for now let's map what we have.

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
