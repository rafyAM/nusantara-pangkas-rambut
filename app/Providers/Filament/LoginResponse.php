<?php

namespace App\Providers\Filament;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function __construct()
    {
    }

    public function toResponse($request): RedirectResponse|Redirector
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('cashier')) {
            return redirect()->route('kasir.pos');
        }

        return redirect()->to(route('filament.admin.pages.dashboard'));
    }
}
