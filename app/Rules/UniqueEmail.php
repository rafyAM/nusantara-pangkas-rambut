<?php

namespace App\Rules;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEmail implements ValidationRule
{
    /**
     * @param  array{user?: int|null, customer?: int|null, employee?: int|null}  $ignore
     *   ID yang dikecualikan per tabel (untuk skenario edit).
     */
    public function __construct(protected array $ignore = [])
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $email = strtolower(trim($value));

        $userQuery = User::query()->where('email', $email);
        if (! empty($this->ignore['user'])) {
            $userQuery->where('id', '!=', $this->ignore['user']);
        }

        $customerQuery = Customer::query()->where('email', $email);
        if (! empty($this->ignore['customer'])) {
            $customerQuery->where('id', '!=', $this->ignore['customer']);
        }

        $employeeQuery = Employee::withoutGlobalScopes()->where('email', $email);
        if (! empty($this->ignore['employee'])) {
            $employeeQuery->where('id', '!=', $this->ignore['employee']);
        }

        if ($userQuery->exists() || $customerQuery->exists() || $employeeQuery->exists()) {
            $fail('Email ini sudah digunakan oleh akun lain.');
        }
    }
}
