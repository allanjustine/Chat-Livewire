<div>
    <div class="d-md-flex bg-secondary text-white" style="height: 91.5vh;">
        <div wire:ignore.self class="offcanvas offcanvas-start bg-dark text-white overflow-y-auto mt-1"
            data-bs-backdrop="static" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
            <div class="offcanvas-header py-3 bg-dark sticky-top text-white">
                <div class="offcanvas-title" id="staticBackdropLabel">
                    <h3>Chats</h3>
                    @if ($people === true)
                    <input type="search" class="form-control" placeholder="Search conversation..."
                        wire:model.live.debounce.500ms='search'>
                    @endif
                    @if ($groupChat === true)
                    <input type="search" class="form-control" placeholder="Search group chat conversation..."
                        wire:model.live.debounce.500ms='searched_gc'>
                    @endif
                </div>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <ul class="list-group">
                <div class="bg-secondary">
                    <div class="d-flex justify-content-evenly">
                        <button
                            class="btn btn-link text-decoration-none btn-sm {{ $people === true ? 'bg-info' : 'bg-dark' }} text-white my-2"
                            type="button" wire:click='peopleClick'>
                            <i class="far fa-user"></i> People
                        </button>
                        <button
                            class="btn btn-link text-decoration-none btn-sm {{ $groupChat === true ? 'bg-info' : 'bg-dark' }} text-white my-2"
                            type="button" wire:click='groupChatClick'>
                            <i class="far fa-users"></i> Group Chat
                        </button>
                    </div>
                </div>
                @if ($people === true)
                @if ($search)
                @forelse ($searched as $search)
                <a href="/chats/{{ $search->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" data-bs-dismiss="offcanvas" aria-label="Close"
                    @if ($search->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $search->id }})'
                    @endif
                    >
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        @if ($search->status === 'online') <span class="online-dot"></span> @elseif($search->status ===
                        'away') <span class="away-dot"></span> @else <span class="offline-dot"></span> @endif
                        <img @if ($search->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($search->profile_picture) }}"
                        @endif width="25" height="25"
                        alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $search->name }}</span>
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">No @if($search) "{{ $search }}" @endif user found.</p>
                @endforelse
                @else
                @forelse ($users as $user)
                <a href="/chats/{{ $user->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($user->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $user->id }})'
                    @endif
                    >
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        @if ($user->status === 'online') <span class="online-dot"></span> @elseif($user->status ===
                        'away') <span class="away-dot"></span> @else <span class="offline-dot"></span> @endif
                        <img @if ($user->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($user->profile_picture) }}"
                        @endif width="25" height="25"
                        alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $user->name }}</span>
                        @if ($user->unseen_sender_chats_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">{{
                            $user->unseen_sender_chats_count > 9 ? '9+' : $user->unseen_sender_chats_count }}</span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">No conversation.</p>
                @endforelse
                @if($users->count() < $usersCount)
                <button class="btn btn-primary btn-sm m-1 float-end" wire:click='loadMorePage' wire:loading.attr='disabled'>
                    <span wire:loading.remove wire:target='loadMorePage'>Load more</span>
                    <span class="spinner-border" wire:loading wire:target='loadMorePage'></span>
                </button>
                @endif
                @endif
                @else
                @if ($searched_gc)
                @forelse ($searchedGc as $sgc)
                <a href="/gc/{{ $sgc->group_chat_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25" height="25"
                            alt="GC Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $sgc->group_chat_name }}</span>
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">
                    No @if($searched_gc) "{{ $searched_gc }}" @endif group chat found.
                </p>
                @endforelse
                @else
                <p class="text-center mt-3">
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                        data-bs-target="#createGroupChat">
                        Create a Group Chat
                    </button>
                </p>
                @forelse ($groupChats as $gc)
                <a href="/gc/{{ $gc->group_chat_token }}" wire:navigate @if ($gc->unseen_count > 0)
                    wire:click='gcSeen({{ $gc->id }})'
                    @endif
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        <span class="online-dot"></span>
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25" height="25"
                            alt="GC Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $gc->group_chat_name }}</span>
                        @if ($gc->unseen_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $gc->unseen_count > 9 ? '9+' : $gc->unseen_count }}</span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">
                    No group chat yet.
                </p>
                @endforelse
                @endif
                @endif

            </ul>
        </div>
        <div class="col-md-3 col-lg-3 bg-light overflow-auto d-none d-md-block bg-dark">
            <div class="py-3 px-2 sticky-top bg-dark text-white">
                <h3>Chats</h3>
                @if ($people === true)
                <input type="search" class="form-control" placeholder="Search conversation..."
                    wire:model.live.debounce.500ms='search'>
                @endif
                @if ($groupChat === true)
                <input type="search" class="form-control" placeholder="Search group chat conversation..."
                    wire:model.live.debounce.500ms='searched_gc'>
                @endif
            </div>
            <ul class="list-group">
                <div class="bg-secondary">
                    <div class="d-flex justify-content-evenly">
                        <button
                            class="btn btn-link text-decoration-none btn-sm {{ $people === true ? 'bg-info' : 'bg-dark' }} text-white my-2"
                            type="button" wire:click='peopleClick'>
                            <i class="far fa-user"></i> People
                        </button>
                        <button
                            class="btn btn-link text-decoration-none btn-sm {{ $groupChat === true ? 'bg-info' : 'bg-dark' }} text-white my-2"
                            type="button" wire:click='groupChatClick'>
                            <i class="far fa-users"></i> Group Chat
                        </button>
                    </div>
                </div>
                @if ($people === true)
                @if ($search)
                @forelse ($searched as $search)
                <a href="/chats/{{ $search->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($search->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $search->id }})'
                    @endif
                    >
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        @if ($search->status === 'online') <span class="online-dot"></span> @elseif($search->status ===
                        'away') <span class="away-dot"></span> @else <span class="offline-dot"></span> @endif
                        <img @if ($search->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($search->profile_picture) }}"
                        @endif width="25" height="25"
                        alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $search->name }}</span>
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">No @if($search) "{{ $search }}" @endif user found.</p>
                @endforelse
                @else
                @forelse ($users as $user)
                <a href="/chats/{{ $user->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($user->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $user->id }})'
                    @endif
                    >
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        @if ($user->status === 'online') <span class="online-dot"></span> @elseif($user->status ===
                        'away') <span class="away-dot"></span> @else <span class="offline-dot"></span> @endif
                        <img @if ($user->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($user->profile_picture) }}"
                        @endif width="25" height="25"
                        alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $user->name }}</span>
                        @if ($user->unseen_sender_chats_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">{{
                            $user->unseen_sender_chats_count > 9 ? '9+' : $user->unseen_sender_chats_count }}</span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">No conversation.</p>
                @endforelse
                @if($users->count() < $usersCount)
                <button class="btn btn-primary btn-sm m-1 float-end" wire:click='loadMorePage' wire:loading.attr='disabled'>
                    <span wire:loading.remove wire:target='loadMorePage'>Load more</span>
                    <span class="spinner-border" wire:loading wire:target='loadMorePage'></span>
                </button>
                @endif
                @endif
                @else
                @if($searched_gc)
                @forelse ($searchedGc as $sgc)
                <a href="/gc/{{ $sgc->group_chat_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25" height="25"
                            alt="GC Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $sgc->group_chat_name }}</span>
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">
                    No @if($searched_gc) "{{ $searched_gc }}" @endif group chat found
                </p>
                @endforelse
                @else
                <p class="text-center mt-3">
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                        data-bs-target="#createGroupChat">
                        Create a Group Chat
                    </button>
                </p>
                @forelse ($groupChats as $gc)
                <a href="/gc/{{ $gc->group_chat_token }}" wire:navigate @if ($gc->unseen_count > 0)
                    wire:click='gcSeen({{ $gc->id }})'
                    @endif
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center text-white bg-secondary">
                        <span class="online-dot"></span>
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25" height="25"
                            alt="GC Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $gc->group_chat_name }}</span>
                        @if ($gc->unseen_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">{{ $gc->unseen_count > 9 ? '9+' : $gc->unseen_count }}</span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">
                    No group chat yet.
                </p>
                @endforelse
                @endif
                @endif
            </ul>
        </div>
        <div class="col-md-9 col-lg-9 col-sm-12 d-flex flex-column">
            <nav class="navbar navbar-secondary bg-secondary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">

                        <button class="btn btn-link text-decoration-none d-md-none d-sm-block text-black" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop"
                            aria-controls="staticBackdrop"><i class="far fa-bars"></i></button>
                    </a>

                </div>
            </nav>
            <div class="d-flex flex-column justify-content-center align-items-center" style="height: 91.5vh;">
                <div>
                    <p class="text-center"><strong style="font-size: 50px;">Welcome To Chat</strong></p>
                </div>
                <div>
                    <p class="text-center">Your total unseen messages: <span class="badge text-bg-info text-white"
                            style="font-size: 15px;">{{
                            $totalChats }}</span></p>
                    <p class="text-center">Your total unseen messages in group chat: <span
                            class="badge text-bg-info text-white" style="font-size: 15px;">{{
                            $totalChatGc }}</span></p>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal" id="createGroupChat" tabindex="-1" aria-labelledby="createGroupChatLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-secondary">
                <form wire:submit.prevent='createGc'>
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-white" id="createGroupChatLabel">Create Group Chat</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="px-2">
                            <div class="form-floating mb-3">
                                <input type="text" id="group-name" wire:model='group_chat_name'
                                    placeholder="Input Group Chat Name" class="form-control">
                                <label class="form-label text-white" id="label-gcn" for="group_chat_name">Group Chat
                                    Name</label>
                                @error('group_chat_name')
                                <span class="text-danger bg-primary-subtle rounded">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <h6 class="text-white">Select Members</h6>
                            <input class="form-control" id="group-search" type="search" placeholder="Search..."
                                wire:model.live.debounce.500ms='search_member'>
                            <div class="form-group mb-1">
                                <hr>
                                <div id="checkboxes-container" class="overflow-auto" style="max-height: 300px;">
                                    @forelse ($allUsers as $user)
                                    <div class="form-check" id='formCheck'>
                                        <input class="form-check-input mt-3" type="checkbox" id="user-{{ $user->id }}"
                                            value="{{ $user->id }}" wire:model="member">
                                        <img @if($user->profile_picture === null)
                                        src="/images/profile.png"
                                        @else
                                        src='{{ Storage::url($user->profile_picture) }}'
                                        @endif
                                        alt="{{ $user->name }}"
                                        class="rounded-circle me-2" width="40" height="40">
                                        <label class="form-check-label text-white fs-6 fw-bolder"
                                            for="user-{{ $user->id }}">
                                            {{ $user->name }}
                                        </label>
                                    </div>
                                    <hr>
                                    @empty
                                    <p class="text-white">@if($search_member)No "{{ $search_member }}" user found. @else
                                        No @if($search) "{{ $search }}" @endif user found. @endif</p>
                                    @endforelse
                                </div>
                                @error('member')
                                <span class="text-danger bg-primary-subtle rounded">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target='createGc'>Save</span>
                            <span wire:loading wire:target='createGc'>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .online-dot {
            width: 10px;
            height: 10px;
            background-color: #068d26;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .offline-dot {
            width: 10px;
            height: 10px;
            background-color: #a2a2a2;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .away-dot {
            width: 10px;
            height: 10px;
            background-color: #f6c81f;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        #group-name {
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
            transition: opacity 0.3s ease;
        }

        #group-search {
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
            transition: opacity 0.3s ease;
        }

        #group-search::placeholder {
            color: white !important;
            transition: opacity 0.3s ease;
        }

        #group-name::placeholder {
            color: white !important;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #group-name:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            opacity: 1;
        }

        #label-gcn::after {
            background-color: rgb(108, 108, 108) !important;
        }

        #formCheck label {
            cursor: pointer;
        }

        #formCheck input {
            cursor: pointer;
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

    <script>
        document.addEventListener('livewire:navigated', function () {
            @this.on('closeModal', () => {
                $('#createGroupChat').modal('hide');

                document.getElementById('createGroupChat').classList.remove('show');
            });
        });
    </script>

</div>
