<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Hash;
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
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    public $activeTab = 'account';

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
            'profile_picture'           =>              ['image', 'max:5120', 'nullable', 'mimes:jpg,jpeg,png,gif,ico,webp'],
            'name'                      =>              ['required', 'min:5', 'max:30'],
            'date_of_birth'             =>              ['required', 'date', 'before_or_equal:2010-12-31'],
            'gender'                    =>              ['required', 'in:Male,Female,Others'],
            'age'                       =>              ['required', 'numeric', 'min:1', 'max:99'],
            'phone_number'              =>              ['required', 'numeric', 'digits:11'],
            'username'                  =>              ['required', 'min:5', 'max:20', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username,' . $user->id],
            'address'                   =>              ['required', 'min:5', 'max:30'],
            'email'                     =>              ['required', 'email', 'regex:/^\S+@\S+\.\S+$/', 'unique:users,email,' . $user->id],
            'nickname'                  =>              ['min:1', 'max:10'],
            'bio'                       =>              ['min:1', 'max:100']


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

    public function messages()
    {
        return [
            'date_of_birth.before_or_equal'         =>              'The date of birth must be on or before 2010',
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function passwordChange()
    {
        $user = auth()->user();

        $this->validate([
            'current_password'              =>                  ['required', 'required_with:new_password'],
            'new_password'                  =>                  ['required', 'required-with:current_passowrd', 'different:current_password', 'min:6', 'confirmed']
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Your current password is incorrect');

            return;
        } else {

            $user->update([
                'password'          =>              $this->new_password
            ]);

            $this->dispatch('toastr', [
                'type'              =>              'success',
                'message'           =>              'Password change successfully'
            ]);

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        }
    }

    public function render()
    {
        return view('livewire.pages.profile', $this->profile());
    }
}
