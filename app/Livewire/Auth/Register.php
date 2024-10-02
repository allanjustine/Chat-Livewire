<?php

namespace App\Livewire\Auth;

use App\Events\NewUserRegister;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Register extends Component
{
    use WithFileUploads;

    #[Title('Register')]

    #[Validate(['image', 'max:5120', 'nullable'])]
    public $profile_picture;

    #[Validate(['required', 'min:5', 'max:30'])]
    public $name;

    #[Validate(['required', 'date', 'before_or_equal:2010-12-31'])]
    public $date_of_birth;

    #[Validate(['required', 'in:Male,Female,Others'])]
    public $gender;

    #[Validate(['required', 'numeric', 'min:1', 'max:99'])]
    public $age;

    #[Validate(['required', 'numeric', 'digits:11'])]
    public $phone_number;

    #[Validate(['required', 'min:5', 'max:20', 'unique:users,username', 'regex:/^[a-zA-Z0-9_]+$/'])]
    public $username;

    #[Validate(['required', 'min:5', 'max:30'])]
    public $address;

    #[Validate(['required', 'email', 'unique:users,email', 'regex:/^\S+@\S+\.\S+$/'])]
    public $email;

    #[Validate(['required', 'min:6', 'confirmed'])]
    public $password;

    public $password_confirmation;

    public function register()
    {
        $this->validate();

        $originalName = $this->profile_picture?->getClientOriginalName();
        $path = $this->profile_picture?->storeAs(path: 'public/images/profile_images', name: $originalName);

        $token = Str::random(50);
        $token2 = Str::random(50);

        $user = User::create([
            'name'                      =>              $this->name,
            'date_of_birth'             =>              $this->date_of_birth,
            'gender'                    =>              $this->gender,
            'age'                       =>              $this->age,
            'phone_number'              =>              $this->phone_number,
            'username'                  =>              $this->username,
            'address'                   =>              $this->address,
            'email'                     =>              $this->email,
            'password'                  =>              bcrypt($this->password),
            'remember_token'            =>              $token,
            'user_token'                =>              $token2,
            'profile_picture'           =>              $path
        ]);

        $user->assignRole('user');

        event(new NewUserRegister($user));

        $this->reset();

        $this->dispatch('swal', [
            'title'       =>          'Registered',
            'text'        =>          $user->name . 'registered successfully.',
            'icon'        =>          'success'
        ]);

    }

    public function messages()
    {
        return [
            'date_of_birth.before_or_equal'         =>              'The date of birth must be on or before 2010',
        ];
    }

    public function render()

    {
        if (auth()->check()) {
            $this->redirect('/home', navigate: true);
        }
        return view('livewire.auth.register');
    }
}
