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
        $user = $record->user;

        if (! $user) {
            $user = User::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => $data['password'] ?? bcrypt(str()->random(32)),
            ]);
            $record->user_id = $user->id;
        }

        $user->email = $data['email'];
        $user->name = $data['name'];

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        $this->assignRoleToUser($user, $data['position']);

        if (isset($data['branch_id'])) {
            $user->branches()->syncWithoutDetaching([$data['branch_id']]);
        }

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
