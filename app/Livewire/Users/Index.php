<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

use function Laravel\Prompts\alert;

class Index extends Component
{

    #[Title('Users')]

    #[On('echo:newUserAccount,NewUserRegister')]
    public function showAllUsers()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return compact('users');
    }

    public function directVerified($id)
    {
        $user = User::find($id);

        if (!$user) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'This user is already verified',
            ]);

            return;
        } else {
            $user->update([
                'email_verified_at'             =>              now()
            ]);

            $this->dispatch('toastr', [
                'type'          =>          'success',
                'message'       =>          'You successfully verfied the user',
            ]);

            return;
        }
    }

    public function removeUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            $this->dispatch('toastr', [
                'type'          =>          'error',
                'message'       =>          'This user is already removed',
            ]);

            return;
        } else {
            $user->delete();
            $this->dispatch('toastr', [
                'type'          =>          'success',
                'message'       =>          'You successfully verfied the user',
            ]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.users.index', $this->showAllUsers());
    }
}
