<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    #[Title('Profile')]

    public $profile_picture;
    public $name;
    public $date_of_birth;
    public $gender;
    public $age;
    public $phone_number;
    public $username;
    public $address;
    public $email;
    public $nickname;
    public $bio;

    public function profile()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->date_of_birth = $user->date_of_birth;
        $this->gender = $user->gender;
        $this->age = $user->age;
        $this->phone_number = $user->phone_number;
        $this->username = $user->username;
        $this->address = $user->address;
        $this->email = $user->email;
        $this->nickname = $user->nickname;
        $this->bio = $user->bio;

        return compact('user');
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $this->validate([
            'profile_picture'           =>              ['image', 'max:102400', 'nullable'],
            'name'                      =>              ['required', 'min:5', 'max:50'],
            'date_of_birth'             =>              ['required', 'date'],
            'gender'                    =>              ['required', 'in:Male,Female,Others'],
            'age'                       =>              ['required', 'numeric', 'min:1', 'max:99'],
            'phone_number'              =>              ['required', 'numeric', 'digits:11'],
            'username'                  =>              ['required', 'min:5', 'max:20'],
            'address'                   =>              ['required', 'min:5', 'max:100'],
            'email'                     =>              ['required', 'email', 'regex:/^\S+@\S+\.\S+$/', 'unique:users,email,' . $user->id],
            // 'nickname'                  =>              ['required', 'min:1', 'max:255'],
            // 'bio'                       =>              ['required', 'min:1', 'max:255']


        ]);

        $updateData = [
            'name'                          =>              $this->name,
            'date_of_birth'                 =>              $this->date_of_birth,
            'gender'                        =>              $this->gender,
            'age'                           =>              $this->age,
            'phone_number'                  =>              $this->phone_number,
            'username'                      =>              $this->username,
            'address'                       =>              $this->address,
            'email'                         =>              $this->email,
            'nickname'                      =>              $this->nickname,
            'bio'                           =>              $this->bio
        ];

        if ($this->profile_picture) {
            if ($this->profile_picture && $user->profile_picture !== null) {
                Storage::delete($user->profile_picture);
            }
            $originalName = $this->profile_picture?->getClientOriginalName();
            $path = $this->profile_picture?->storeAs(path: 'public/images/profile_images', name: $originalName);
            $updateData['profile_picture'] = $path;
        }


        $user->update($updateData);

        $user->save();

        $this->dispatch('toastr', [
            'type'          =>          'success',
            'message'       =>          'Profile updated successfully',
        ]);
    }

    public function render()
    {
        return view('livewire.pages.profile', $this->profile());
    }
}
