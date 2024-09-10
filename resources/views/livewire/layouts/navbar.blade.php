<div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark z-3">
        <div class="container-fluid">
            @auth
            <a class="navbar-brand" href="/home" wire:navigate>
                <img src="/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                Chat
            </a>
            @else
            <a class="navbar-brand" href="/" wire:navigate>
                <img src="/images/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                Chat
            </a>
            @endauth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse gap-3" id="navbarNav">
                @auth
                <ul class="navbar-nav ms-auto gap-3 mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ 'home' === request()->path() ? 'active' : '' }}" aria-current="page"
                            href="/home" wire:navigate><i class="far fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ 'announcement' === request()->path() ? 'active' : '' }}"
                            aria-current="page" href="/announcement" wire:navigate><i class="far fa-megaphone"></i>
                            Announcements</a>
                    </li>
                    <li class="nav-iteme">
                        <a class="nav-link {{ 'chats' === request()->path() ? 'active' : '' }}" href="/chats"
                            wire:navigate>
                            <i class="far fa-message-dots position-relative">
                                @if ($totalChats > 9)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    9+
                                    <span class="visually-hidden">Unread messages</span>
                                </span>
                                @elseif ($totalChats > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $totalChats }}
                                    <span class="visually-hidden">Unread messages</span>
                                </span>
                                @endif
                            </i> Chats

                        </a>
                    </li>
                    @role('admin')
                    <li class="nav-iteme">
                        <a class="nav-link {{ 'admin/users' === request()->path() ? 'active' : '' }}" href="/admin/users"
                            wire:navigate>
                            <i class="far fa-users">

                            </i> Users

                        </a>
                    </li>
                    @endrole

                </ul>
                <div class="navbar-nav">
                    <div class="dropdown">
                        <a class="nav-link d-flex gap-2 mt-1" href="#" id="navbarDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="position-relative"><i class="{{ auth()->user()->unreadNotifications()->count() > 0 ? 'fas' : 'far' }} fa-bell fs-5"></i>
                                @if (auth()->user()->unreadNotifications()->count() > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge
                                    rounded-pill bg-danger" style="font-size: 8px;">
                                    {{ auth()->user()->unreadNotifications()->count() }}
                                </span>
                                @endif
                            </span>
                            <span class="d-md-none d-block sticky-top">Notifications</span>
                        </a>
                        <div class="dropdown-menu shadow dropdown-menu-end p-2" style="min-width: 350px; z-index: 1021 !important;"
                            aria-labelledby="navbarDropdownMenuLink">
                            <h6 class="mt-2 ms-2">Notifications</h6>
                            <hr>
                            <div class="mt-3" style="max-height: 400px; overflow: auto;">
                                @forelse (auth()->user()->notifications as $notification)
                                <a class="d-flex gap-2 text-dark mb-1 p-1 rounded text-decoration-none position-relative"
                                wire:click.prevent="markAsRead('{{ $notification->id }}')"
                                style="background-color: {{ $notification->read_at ? 'transparent' : 'rgba(40, 39, 39, 0.100)' }};" href="/updates/{{ $notification->data['post_title'] }}" wire:navigate style="text-decoration: none;">
                                @if (!$notification->read_at)
                                <span class="rounded-circle bg-secondary position-absolute me-2 mt-2" style="width: 7px; height: 7px; display: inline-block; right: 0;"></span>
                                @endif
                                    <span>
                                        <img @if($notification->data['poster_profile_picture'] === null)
                                        src="/images/profile.png"
                                        @else
                                        src="{{ Storage::url($notification->data['poster_profile_picture']) }}"
                                        @endif
                                        alt="Profile Image"
                                        style="width: 30px; height: 30px;" class=" image-fluid rounded-circle">
                                    </span>
                                    <div class="notification-content" style="font-size: 13px;">
                                        <span><strong>{{ $notification->data['poster_name'] }}</strong> {{ $notification->data['post_body'] }}</span>
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($notification->data['post_created_at'])->diffForHumans() < 1 ? 'Just now' : \Carbon\Carbon::parse($notification->data['post_created_at'])->diffForHumans() }}
                                        </small>
                                    </div>
                                </a>
                                @empty
                                <div class="py-4 text-center">
                                    <i class="fas fa-bell-slash fs-3"></i>
                                    <p>No notifications yet</p>
                                </div>
                                @endforelse
                                <hr class="dropdown-divider">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-link" style="font-size: 13px;" wire:click='markAllAsRead'>Mark all as read</button>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a class="nav-link d-flex gap-2" href="/profile-info/{{ auth()->user()->username }}" id="navbarDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img @if (auth()->user()->profile_picture === null)
                            src="/images/profile.png"
                            @else
                            src="{{ Storage::url(auth()->user()->profile_picture) }}"
                            @endif
                            alt="{{ auth()->user()->name }}" width="30" height="30"
                            class="rounded-circle">

                            <span class="d-md-none d-block mt-2">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu shadow dropdown-menu-end" style="min-width: 300px;"
                            aria-labelledby="navbarDropdownMenuLink">
                            <li><a href="/profile-info/{{ auth()->user()->username }}" wire:navigate class="dropdown-item btn btn-link text-decoration-none"
                                    href="#"><img @if (auth()->user()->profile_picture === null)
                                    src="/images/profile.png"
                                    @else
                                    src="{{ Storage::url(auth()->user()->profile_picture) }}"
                                    @endif
                                    alt="{{ auth()->user()->name }}" width="40" height="40"
                                    class="rounded-circle"> <strong>{{ auth()->user()->name
                                        }}</strong></a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item btn btn-link text-decoration-none" href="/profile" wire:navigate>
                                    <div class="d-flex">
                                        <span class="col-1"><i class="far fa-gear"></i> </span>
                                        <span class="col-11"><strong>Settings</strong></span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><button type="button" class="dropdown-item btn btn-link text-decoration-none" href="#"
                                    wire:click='confirmLogout'>
                                    <div class="d-flex">
                                        <span class="col-1"><i class="far fa-arrow-right-from-bracket"></i></span>
                                        <span class="col-11"><strong>Logout</strong></span>
                                    </div>
                                </button></li>
                        </ul>
                    </div>
                </div>
                @else
                <ul class="navbar-nav ms-auto gap-3 mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ 'login' === request()->path() ? 'active' : '' }}" aria-current="page"
                            href="/login" wire:navigate><i class="far fa-right-to-bracket"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ 'register' === request()->path() ? 'active' : '' }}" href="/register"
                            wire:navigate><i class="far fa-user-plus"></i> Register</a>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('livewire:navigated', ()=>{
            @this.on('swal',(event)=>{
                const data=event
            swal.fire({
                icon:data[0]['icon'],
                title:data[0]['title'],
                text:data[0]['text'],
                showCancelButton:true,
                confirmButtonColor:'red',
                confirmButtonText:'Yes, Logout!',
            }).then((result)=>{
                if(result.isConfirmed){
                    @this.dispatch('onLogout')
                }
            })
        })
         })

    </script>

    <script>
        document.addEventListener('livewire:navigated', function () {
            window.addEventListener('beforeunload', function (event) {
                @this.dispatch('userLogout');
            });
        });
    </script>

    <script>
        document.addEventListener('livewire:navigated', function () {

            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    setTimeout(() => {
                        @this.dispatch('userAway');
                    }, 300000);
                } else {
                    setTimeout(() => {
                        @this.dispatch('userOnline');
                    }, 10000);
                }
            });
        });
    </script>

</div>
