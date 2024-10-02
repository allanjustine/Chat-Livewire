<div>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-5 col-xl-4 mb-2">
                <div class="card bg-dark-subtle">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Settings</h5>
                    </div>
                    <div class="list-group list-group-flush" id="list-tab" role="tablist">
                        <a class="bg-dark-subtle list-group-item list-group-item-action {{ $activeTab === 'account' ? 'active' : '' }}"
                            data-bs-toggle="list" href="#" wire:click.prevent="setActiveTab('account')" role="tab">
                            Account
                        </a>
                        <a class="bg-dark-subtle list-group-item list-group-item-action {{ $activeTab === 'password' ? 'active' : '' }}"
                            data-bs-toggle="list" href="#" wire:click.prevent="setActiveTab('password')" role="tab">
                            Password
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-7 col-xl-8">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade {{ $activeTab === 'account' ? 'show active' : '' }}" id="account"
                        role="tabpanel">
                        <div class="card bg-dark-subtle">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Public info</h5>
                                <div>
                                    <i class="fas fa-globe text-primary"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nickname" class="form-label">Nickname</label>
                                                <input type="text" class="form-control" wire:model='nickname'
                                                    id="nickname" placeholder="Nickname">
                                                @error('nickname')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="inputBio" class="form-label">Biography</label>
                                                <textarea rows="2" class="form-control" wire:model='bio' id="inputBio"
                                                    placeholder="Tell something about yourself"></textarea>
                                                @error('bio')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <img alt="{{ $name }}" @if ($profile_picture)
                                                    src="{{ $profile_picture->temporaryUrl() }}" @else
                                                    @if(auth()->user()->profile_picture === null)
                                                src="/images/profile.png"
                                                @else
                                                src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                                @endif
                                                @endif
                                                class="rounded-circle img-responsive mt-2" width="120" height="120">
                                                <div class="mt-2">
                                                    <div class="mb-3 d-flex justify-content-center">
                                                        <label for="profile_picture"
                                                            class="form-label m-1 btn btn-sm text-center text-white bg-primary"
                                                            style="cursor: pointer;">
                                                            <span wire:loading.remove wire:target='profile_picture'><i
                                                                    class="far fa-camera"></i>
                                                                Upload photo</span>
                                                            <span wire:loading wire:loading.attr='disabled'
                                                                wire:target='profile_picture'>
                                                                <i
                                                                    class="fa-duotone fa-solid fa-spinner-third fa-spin"></i>
                                                                Uploading...
                                                            </span>
                                                        </label>
                                                        <input id="profile_picture" type="file"
                                                            class="form-control d-none pr-4 @error('profile_picture') is-invalid @enderror"
                                                            name="profile_picture" accept="image/*"
                                                            autocomplete="profile_picture" autofocus
                                                            wire:model='profile_picture'>
                                                    </div>
                                                </div>
                                                @error('profile_picture')
                                                <small class="text-danger">{{ $message }}</small>
                                                @else
                                                <small>For best results, use an image at least 128px by 128px in .jpg
                                                    format</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" wire:click='updateProfile' wire:loading.attr='disabled'
                                        class="btn btn-primary">
                                        <span wire:loading wire:target='updateProfile'>Saving...</span>
                                        <span wire:loading.remove wire:target='updateProfile'>Save changes</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card bg-dark-subtle mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Private info</h5>
                                <div>
                                    <i class="fas fa-shield-check text-primary"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" wire:model='name' id="name"
                                                placeholder="Name">
                                            @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" wire:model='address'
                                                placeholder="Address">
                                            @error('address')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select wire:model='gender' class="form-select">
                                                <option hidden selected>Select gender</option>
                                                <option disabled>Select gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Others">Others</option>
                                            </select>
                                            @error('gender')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="date_of_birth" class="form-label">Date of birth</label>
                                            <input type="date" class="form-control" id="date_of_birth"
                                                wire:model='date_of_birth'>
                                            @error('date_of_birth')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="number" class="form-control" wire:model='age' id="age"
                                                placeholder="Age">
                                            @error('age')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone_number" class="form-label">Phone number</label>
                                            <input type="number" class="form-control" id="phone_number"
                                                wire:model='phone_number' placeholder="Phone number">
                                            @error('phone_number')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" wire:model='username' id="username"
                                                placeholder="Username">
                                            @error('username')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" wire:model='email'
                                                placeholder="Email">
                                            @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <button type="button" wire:loading.attr='disabled' wire:click='updateProfile'
                                        class="btn btn-primary mt-3">
                                        <span wire:loading wire:target='updateProfile'>Saving...</span>
                                        <span wire:loading.remove wire:target='updateProfile'>Save changes</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="password"
                        role="tabpanel">
                        <div class="card bg-dark-subtle">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Password</h5>
                                <i class="fas fa-lock-keyhole text-primary"></i>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current password</label>
                                    <input type="password" class="form-control" id="current_password"
                                        wire:model='current_password'>
                                    {{-- <small><a href="#">Forgot your password?</a></small> --}}
                                    @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New password</label>
                                    <input type="password" class="form-control" id="new_password"
                                        wire:model='new_password'>
                                    @error('new_password')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Verify password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        wire:model='new_password_confirmation'>
                                    @error('new_password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="button" wire:click.prevent='passwordChange' wire:loading.attr='disabled'
                                    class="btn btn-primary">
                                    <span wire:loading wire:target='passwordChange'>Saving...</span>
                                    <span wire:loading.remove wire:target='passwordChange'>Save Changes</span>
                                </button>
                            </div>
                        </div>

                        {{-- <div class="card bg-dark-subtle mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">Sessions</h5>
                                <i class="fas fa-calendar-users text-primary"></i>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Device</th>
                                            <th>Location</th>
                                            <th>IP</th>
                                            <th>Browser</th>
                                            <th>Last accessed</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($user->sessions as $session)
                                        <tr>
                                            <td>{{ $session->user_agent }}</td>
                                            <td>{{ $session }}</td>
                                            <td>192.168.1.10</td>
                                            <td>Chrome</td>
                                            <td>Today</td>
                                            <td><a href="#" class="btn btn-sm btn-danger">End session</a></td>
                                        </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Style the active list group item */
        .list-group-item.active {
            background-color: #434b57 !important;
            color: #fff;
            border-color: white !important;
        }
    </style>

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
