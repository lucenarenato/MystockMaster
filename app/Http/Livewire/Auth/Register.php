<?php

declare(strict_types=1);

namespace App\Http\Livewire\Auth;

use App\Actions\ProvisionTenant;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $company = '';

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function register()
    {
        $this->validate([
            'company'  => ['required', 'max:255'],
            'name'     => ['required'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'same:passwordConfirmation'],
        ]);

        $user = DB::transaction(function () {
            $user = User::create([
                'email'    => $this->email,
                'name'     => $this->name,
                'password' => Hash::make($this->password),
            ]);

            app(ProvisionTenant::class)->handle($user, $this->company);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user, true);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
