<div>
    <div class="container-fluid">
        <div class="mt-5">
            <button class="float-end btn btn-primary" wire:click='setOfflineAll'>Set all offline</button>
            <h1 class="fw-bold text-white">
                Users
            </h1>
        </div>
        <div class="table-responsive rounded shadow">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID.</th>
                        <th>Profile Picture</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><img @if ($user->profile_picture === null)
                            src="/images/profile.png"
                            @else
                            src="{{ Storage::url($user->profile_picture) }}"
                            @endif width="80" height="80"
                            alt="Profile Image" class="rounded-circle"></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span
                                class="{{ $user->email_verified_at ? 'badge rounded-pill text-bg-primary' : 'badge rounded-pill text-bg-danger' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}</span></td>
                        <td>@forelse ($user->roles as $role )
                            <span class="badge badge-pill {{ $role->name === 'admin' ? 'bg-info' : 'bg-dark' }}">
                                {{ $role->name }}
                            </span>
                            @empty
                            <button class="btn btn-primary btn-sm" wire:click='assignRole({{ $user->id }})'>
                                Add role
                            </button>
                            <span class="badge badge-pill bg-danger">
                                No roles applied
                            </span>
                            @endforelse
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if ($user->email_verified_at === null)
                                <button type="button" class="btn btn-sm btn-primary"
                                    wire:click='directVerified({{ $user->id }})'>Verified user</button>
                                @else
                                <button type="button" class="btn btn-sm btn-danger"
                                    wire:click='banUser({{ $user->id }})'>Ban user</button>
                                @endif
                                <button type="button" class="btn btn-sm btn-warning"
                                    wire:click='removeUser({{ $user->id }})'>Remove user</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
