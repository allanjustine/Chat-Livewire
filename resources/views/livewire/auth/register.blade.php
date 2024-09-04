<div>
    <div class="d-flex justify-content-center align-items-center" style="background: url('/images/bg.gif') no-repeat center center fixed;
        background-size: cover;">
        <div class="card shadow mt-5 py-2 col-md-5" style="opacity: 0.97">
            <h4 class="text-center">Register Account</h4>
            <div class="card-body">
                <form wire:submit.prevent='register'>
                    <div class="d-flex justify-content-center mb-3">
                        <img @if ($profile_picture) src="{{ $profile_picture->temporaryUrl() }}" @else
                            src="/images/profile.png" @endif width="70" height="70" class="rounded float-start"
                            alt="...">
                    </div>

                    @error('profile_picture')
                    <span class="text-danger d-flex justify-content-center">{{ $message }}</span>
                    @enderror
                    <div class="mb-3 d-flex justify-content-center">
                        <label for="profile_picture" class="form-label m-1 btn btn-sm text-center text-white bg-primary"
                            style="cursor: pointer;"><i class="far fa-camera"></i> Upload photo</label>
                        <input id="profile_picture" type="file"
                            class="form-control d-none pr-4 @error('profile_picture') is-invalid @enderror"
                            name="profile_picture" accept="image/*" autocomplete="profile_picture" autofocus
                            wire:model='profile_picture'>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" wire:model='name'>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                            wire:model='date_of_birth'>
                        @error('date_of_birth')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" class="form-select" id="gender" wire:model='gender'>
                            <option hidden selected>Select gender</option>
                            <option disabled>Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Others">Others</option>
                        </select>
                        @error('gender')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" wire:model='age'>
                        @error('age')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number"
                            wire:model='phone_number'>
                        @error('phone_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" wire:model='username'>
                        @error('username')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" wire:model='address'>
                        @error('address')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" wire:model='email'>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" wire:model='password'>
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Password Confirmation</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" wire:model='password_confirmation'>
                        @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary form-control btn-sm">
                        <span wire:loading.remove>Register</span>
                        <div wire:loading>
                            <span class="spinner-grow spinner-grow-sm"></span>
                            <span class="spinner-grow spinner-grow-sm"></span>
                            <span class="spinner-grow spinner-grow-sm"></span>
                        </div>
                    </button>
                </form>
            </div>
            <div class="card-footer text-center">
                Already have an account? <a href="/login" class="btn btn-link" wire:navigate>Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', ()=>{

        @this.on('swal',(event)=>{
            const data=event
            swal.fire({
                icon:data[0]['icon'],
                title:data[0]['title'],
                text:data[0]['text'],
                html: "You will redirected to Login page <br>Thank you!",
            }).then(function () {
                Livewire.navigate('/login');
        });
        })
    })
    </script>
</div>
