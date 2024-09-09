<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Title('Login')]

    #[Validate(['required', 'min:5'])]
    public $username_or_email;

    #[Validate(['required', 'min:6'])]
    public $password;

    public $remember;

    public function login()
    {
        $this->validate();

        if ($this->remember) {
            Cookie::queue('remembered_username_or_email', $this->username_or_email, 60 * 24 * 30); // 30 days
        } else {
            Cookie::queue(Cookie::forget('remembered_username_or_email'));
        }

        $user = User::where('email', $this->username_or_email)
            ->orWhere('username', $this->username_or_email)
            ->first();

        if (!$user || $user->email_verified_at === null) {

            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'The username or email is either not verified yet or does not exist',
            ]);

            return;
        }

        $login = auth()->attempt([
            'email'         =>          filter_var($this->username_or_email, FILTER_VALIDATE_EMAIL) ? $this->username_or_email : $user->email,
            'password'      =>          $this->password
        ]);

        if ($login) {

            $user->update(['status' => 'online']);

            $this->redirect('/home', navigate: true);

            Log::notice('Username/Email: ' . $this->username_or_email . ' Password: ' . $this->password);

        } else {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'Invalid Credentials',
            ]);

            return;
        }
    }

    public function mount()
    {
        $this->username_or_email = Cookie::get('remembered_username_or_email');
    }

    public function render()
    {
        if (auth()->check()) {
            $this->redirect('/home', navigate: true);
        }
        return view('livewire.auth.login');
    }
}
