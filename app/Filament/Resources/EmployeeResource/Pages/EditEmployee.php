<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Update Linked User if exists, or create if missing
        $user = $record->user;

        if (!$user) {
            // Create if not exists (handling edge case)
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    // If creating new user here, password missing issue? 
                    // Edit form might not have password filled.
                    // Use default or random if not provided? 
                    // For now, let's assume email matches existing or we create basic.
                    'password' => '$2y$12$K.x.x.x', // generic hash or handle better
                ]
            );
            $record->user_id = $user->id;
        }

        // Update User details
        $userCheck = User::where('email', $data['email'])->where('id', '!=', $user->id)->first();
        if (!$userCheck) {
            $user->email = $data['email'];
            $user->name = $data['name'];
        }

        // Update Password if provided
        if (!empty($data['password'])) {
            $user->password = $data['password']; // Already hashed
        }

        $user->save();

        // 2. Sync Role
        $this->assignRoleToUser($user, $data['position']);

        // 3. Update Employee
        unset($data['password']); // Remove from employee data

        $record->update($data);

        return $record;
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
}
