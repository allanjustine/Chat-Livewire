<div>
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-md bg-dark">
                    <div class="card-header text-center">
                        <h4 class="text-white">Update Profile</h4>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent='updateProfile'>
                            <div class="d-flex justify-content-center mb-3">
                                <img @if ($profile_picture) src="{{ $profile_picture->temporaryUrl() }}" @else
                                    @if(auth()->user()->profile_picture === null)
                                src="/images/profile.png"
                                @else
                                src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                @endif
                                @endif
                                width="150" height="150" class="rounded-circle float-start border border-light-subtle"
                                alt="...">
                            </div>

                            @error('profile_picture')
                            <span class="text-danger d-flex justify-content-center">{{ $message }}</span>
                            @enderror
                            <div class="mb-3 d-flex justify-content-center">
                                <label for="profile_picture"
                                    class="form-label m-1 btn btn-sm text-center text-white bg-primary"
                                    style="cursor: pointer;"><i class="far fa-camera"></i> Upload photo</label>
                                <input id="profile_picture" type="file"
                                    class="form-control d-none pr-4 @error('profile_picture') is-invalid @enderror"
                                    name="profile_picture" accept="image/*" autocomplete="profile_picture" autofocus
                                    wire:model='profile_picture'>
                            </div>

                            <div class="d-flex gap-1">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" wire:model='name'
                                            placeholder="Enter your name">
                                        <label for="name" class="form-label">Name</label>
                                        @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="address" wire:model='address'
                                            placeholder="Enter your address">
                                        <label for="address" class="form-label">Address</label>
                                        @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-1">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <select wire:model='gender' class="form-select">
                                            <option hidden selected>Select gender</option>
                                            <option disabled>Select gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Others">Others</option>
                                        </select>
                                        <label for="gender" class="form-label">Gender</label>
                                        @error('gender')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="date_of_birth"
                                            wire:model='date_of_birth'>
                                        <label for="date_of_birth" class="form-label">Birth Date</label>
                                        @error('date_of_birth')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-1">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="age" wire:model='age'
                                            placeholder="Enter your age">
                                        <label for="age" class="form-label">Age</label>
                                        @error('age')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="phone_number"
                                            wire:model='phone_number' placeholder="Enter your phone number">
                                        <label for="phone_number" class="form-label">Phone Number</label>
                                        @error('phone_number')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-1">
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" wire:model='username'
                                            placeholder="Enter your username">
                                        <label for="username" class="form-label">Username</label>
                                        @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" wire:model='email'
                                            placeholder="Enter your email">
                                        <label for="email" class="form-label">Email address</label>
                                        @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nickname"
                                    wire:model='nickname' placeholder="Enter your nickname">
                                <label for="nickname" class="form-label">Nickname</label>
                                @error('nickname')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="bio" wire:model='bio' rows="5"
                                    placeholder="Enter your bio"></textarea>
                                <label for="bio" class="form-label">Bio</label>
                                @error('bio')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <span wire:loading>Saving...</span>
                                    <span wire:loading.remove>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('livewire:navigated', ()=>{

            @this.on('toastr', (event) => {
                const data=event
                toastr[data[0].type](data[0].message, '', {
                closeButton: true,
                "progressBar": true,
                });
            })
        })
    </script>
</div>
