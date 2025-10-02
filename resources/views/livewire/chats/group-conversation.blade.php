<div>
    <div>
        <div class="d-flex bg-secondary position-relative text-white" style="height: 91.5vh;" x-data="drop_file_component()"
            x-on:drop="dropingFile = false" x-on:drop.prevent="handleFileDrop($event)"
            x-on:dragover.prevent="dropingFile = true" x-on:dragleave.prevent="dropingFile = false" x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false" x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <div class="overlay" x-show='dropingFile'></div>
            <div wire:ignore.self class="offcanvas offcanvas-start bg-dark text-white overflow-y-auto mt-1"
                data-bs-backdrop="static" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
                <div class="offcanvas-header py-3 sticky-top bg-dark text-white">
                    <div class="offcanvas-title" id="staticBackdropLabel">
                        <h3>Chats</h3>
                        <input type="search" class="form-control" placeholder="Search conversation..."
                            wire:model.live.debounce.500ms='search'>
                    </div>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <ul class="list-group">
                    @forelse ($allChats as $item)
                        @if ($item instanceof App\Models\GroupChat)
                            <a @if ($item->unseen_count > 0) wire:click='gcSeen({{ $item->id }})' @endif
                                href="/gc/{{ $item->group_chat_token }}" wire:navigate
                                class="mt-1 text-decoration-none rounded shadow mx-1">
                                <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                                    <span class="online-dot"></span>
                                    <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25"
                                        height="25" alt="Group Chat Image" class="rounded-circle">
                                    <span class="ms-2 text-start"
                                        style="font-size: 12px !important;">{{ $item->group_chat_name }}</span>
                                    @if ($item->unseen_count > 0)
                                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                                            {{ $item->unseen_count > 9 ? '9+' : $item->unseen_count }}
                                        </span>
                                    @endif
                                </li>
                            </a>
                        @elseif ($item instanceof App\Models\User)
                            <a href="/chats/{{ $item->user_token }}" wire:navigate
                                class="mt-1 text-decoration-none rounded shadow mx-1"
                                @if ($item->unseen_sender_chats_count > 0) wire:click='seen({{ $item->id }})' @endif>
                                <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                                    @if ($item->status === 'online')
                                        <span class="online-dot"></span>
                                    @elseif ($item->status === 'away')
                                        <span class="away-dot"></span>
                                    @else
                                        <span class="offline-dot"></span>
                                    @endif
                                    <img src="{{ $item->profile_picture ? Storage::url($item->profile_picture) : '/images/profile.png' }}"
                                        width="25" height="25" alt="Profile Image" class="rounded-circle">
                                    <span class="ms-2 text-start"
                                        style="font-size: 12px !important;">{{ $item->name }}</span>
                                    @if ($item->unseen_sender_chats_count > 0)
                                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                                            {{ $item->unseen_sender_chats_count > 9 ? '9+' : $item->unseen_sender_chats_count }}
                                        </span>
                                    @endif
                                </li>
                            </a>
                        @endif
                    @empty
                        <p class="text-center mt-5">
                            @if ($search)
                                No "{{ $search }}" found.
                            @else
                                No conversation yet.
                            @endif
                        </p>
                    @endforelse
                </ul>
            </div>
            <div class="col-md-3 bg-light overflow-auto d-none d-md-block bg-dark">
                <div class="py-3 px-2 sticky-top bg-dark">
                    <h3>Chats</h3>
                    <input type="search" class="form-control" placeholder="Search conversation..."
                        wire:model.live.debounce.500ms='search'>
                </div>
                <ul class="list-group">
                    @forelse ($allChats as $item)
                        @if ($item instanceof App\Models\GroupChat)
                            <a @if ($item->unseen_count > 0) wire:click='gcSeen({{ $item->id }})' @endif
                                href="/gc/{{ $item->group_chat_token }}" wire:navigate
                                class="mt-1 text-decoration-none rounded shadow mx-1">
                                <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                                    <span class="online-dot"></span>
                                    <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="25"
                                        height="25" alt="Group Chat Image" class="rounded-circle">
                                    <span class="ms-2 text-start"
                                        style="font-size: 12px !important;">{{ $item->group_chat_name }}</span>
                                    @if ($item->unseen_count > 0)
                                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                                            {{ $item->unseen_count > 9 ? '9+' : $item->unseen_count }}
                                        </span>
                                    @endif
                                </li>
                            </a>
                        @elseif ($item instanceof App\Models\User)
                            <a href="/chats/{{ $item->user_token }}" wire:navigate
                                class="mt-1 text-decoration-none rounded shadow mx-1"
                                @if ($item->unseen_sender_chats_count > 0) wire:click='seen({{ $item->id }})' @endif>
                                <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                                    @if ($item->status === 'online')
                                        <span class="online-dot"></span>
                                    @elseif ($item->status === 'away')
                                        <span class="away-dot"></span>
                                    @else
                                        <span class="offline-dot"></span>
                                    @endif
                                    <img src="{{ $item->profile_picture ? Storage::url($item->profile_picture) : '/images/profile.png' }}"
                                        width="25" height="25" alt="Profile Image" class="rounded-circle">
                                    <span class="ms-2 text-start"
                                        style="font-size: 12px !important;">{{ $item->name }}</span>
                                    @if ($item->unseen_sender_chats_count > 0)
                                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                                            {{ $item->unseen_sender_chats_count > 9 ? '9+' : $item->unseen_sender_chats_count }}
                                        </span>
                                    @endif
                                </li>
                            </a>
                        @endif
                    @empty
                        <p class="text-center mt-5">
                            @if ($search)
                                No "{{ $search }}" found.
                            @else
                                No conversation yet.
                            @endif
                        </p>
                    @endforelse
                </ul>
            </div>
            <div class="col-md-9 col-sm-12 d-flex flex-column col-12">
                <nav class="navbar navbar-secondary bg-secondary border rounded shadow">
                    <div class="container-fluid d-flex align-items-center">
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <button class="btn btn-link text-decoration-none d-md-none d-sm-block text-black"
                                type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop"
                                aria-controls="staticBackdrop">
                                <i class="far fa-bars"></i>
                            </button>
                            <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" alt="Profile Image"
                                width="30" height="30" class="d-inline-block align-top rounded-circle me-2">
                            <span class="text-white fs-6"><strong>{{ $groupConvo->group_chat_name }}</strong></span>
                        </a>
                        <div class="dropdown ms-auto">
                            <a class="nav-link" href="#" id="navbarDropdownMenuLink" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-circle-info fs-4"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end pe-3" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <button class="dropdown-item btn btn-link text-decoration-none" href="#"
                                        data-bs-toggle="modal" data-bs-target="#allMembers">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-users"></i></span>
                                            <span class="col-10"><strong>All Members</strong></span>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item btn btn-link text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#addMembersGc">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-user-plus"></i></span>
                                            <span class="col-10"><strong>Add Members</strong></span>
                                        </div>
                                    </button>

                                </li>
                                <li>
                                    <button class="dropdown-item btn btn-link text-decoration-none">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-trash"></i></span>
                                            <span class="col-10"><strong>Delete Conversation</strong></span>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item btn btn-link text-decoration-none">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-info-circle"></i></span>
                                            <span class="col-10"><strong>Report Conversation</strong></span>
                                        </div>
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item btn btn-link text-decoration-none"
                                        wire:click='leaveGc({{ $groupConvo->id }})'>
                                        <div class="d-flex">
                                            <span class="col-2"><i
                                                    class="far fa-arrow-right-from-bracket"></i></span>
                                            <span class="col-10"><strong>Leave</strong></span>
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                {{-- All Members Modal --}}
                <div wire:ignore.self class="modal fade" id="allMembers" tabindex="-1"
                    aria-labelledby="allMembersLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content bg-secondary">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5 text-white" id="allMembersLabel">All Members</h1>
                                <button wire:click='closeNicknameEdit' type="button" class="btn-close"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group border-0">
                                    @php
                                        $isAuthenticatedUserAdmin = $groupConvo->groupChatMembers->contains(function (
                                            $member,
                                        ) {
                                            return auth()->user()->id === $member->id && $member->pivot->is_admin;
                                        });
                                    @endphp
                                    @foreach ($groupConvo->groupChatMembers as $member)
                                        <li class="list-group-item bg-secondary border-0 d-flex align-items-center"
                                            title="{{ $member->name }}">
                                            <img @if ($member->profile_picture === null) src="/images/profile.png"
                                    @else
                                    src='{{ Storage::url($member->profile_picture) }}' @endif
                                                alt="{{ $member->name }}" class="rounded-circle me-2" width="40"
                                                height="40">
                                            @if ($inputEditNickname === true && $editNickname->id === $member->pivot->id)
                                                <div class="input-group">
                                                    <input type="text" id="inputGcNickname" class="form-control"
                                                        wire:model='gc_nickname' style="max-width: 180px;">
                                                    <button type="button" wire:click='updateGcNickname'
                                                        class="input-group-text bg-primary text-white"
                                                        id="basic-addon">
                                                        <span wire:loading.remove wire:target='updateGcNickname'><i
                                                                class="far fa-floppy-disk"></i></span>
                                                        <span wire:target='updateGcNickname' wire:loading><span
                                                                class="spinner-border spinner-border-sm"></span></span>
                                                    </button>
                                                </div>
                                            @else
                                                <span
                                                    class="text-white"><strong>{{ $member->pivot->gc_nickname ?: $member->name }}</strong>
                                                    <span
                                                        class="{{ $member->pivot->is_admin ? 'badge text-bg-primary' : 'badge text-bg-dark' }}"
                                                        style="font-size: 8px !important;">{{ $member->pivot->is_admin ? 'Admin' : 'Member' }}</span></span>
                                            @endif
                                            <div class="d-flex ms-auto gap-1">
                                                @if ($inputEditNickname === true && $editNickname->id === $member->pivot->id)
                                                    <button class="btn btn-sm btn-dark d-flex gap-1" type="button"
                                                        wire:click='closeNicknameEdit'>
                                                        <span><i class="far fa-x"></i></span>
                                                        <span class="d-none d-md-block">Cancel</span></button>
                                                @else
                                                    <button class="btn btn-primary btn-sm d-flex gap-1"
                                                        wire:click='nicknameEdit({{ $member->pivot->id }})'
                                                        type="button">
                                                        <span><i class="far fa-pen"></i></span>
                                                        <span class="d-none d-md-block">Edit</span>
                                                    </button>
                                                @endif
                                                @if ($isAuthenticatedUserAdmin && auth()->user()->id !== $member->id)
                                                    <button class="btn btn-danger btn-sm d-flex gap-1">
                                                        <span><i class="far fa-xmark"></i></span>
                                                        <span class="d-none d-md-block">Kick</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark" data-bs-dismiss="modal"
                                    wire:click='closeNicknameEdit'>Close</button>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Add Members To Gc Modal --}}

                <div wire:ignore.self class="modal" id="addMembersGc" tabindex="-1"
                    aria-labelledby="addMembersGcLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content bg-secondary">
                            <form wire:submit.prevent='addMemberToGc'>
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5 text-white" id="addMembersGcLabel">Add Members to
                                        Group Chat
                                    </h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="px-2">
                                        <h6 class="text-white">Select Members</h6>
                                        <input class="form-control" id="group-search" type="search"
                                            placeholder="Search..." wire:model.live.debounce.500ms='search_member'>
                                        <div class="form-group mb-1">
                                            <hr>
                                            <div id="checkboxes-container" class="overflow-auto"
                                                style="max-height: 300px;">
                                                @forelse ($allUsers as $user)
                                                    <div class="form-check" id='formCheck'>
                                                        <input class="form-check-input mt-3" type="checkbox"
                                                            id="user-{{ $user->id }}"
                                                            value="{{ $user->id }}" wire:model="member">
                                                        <img @if ($user->profile_picture === null) src="/images/profile.png"
                                                @else
                                                src='{{ Storage::url($user->profile_picture) }}' @endif
                                                            alt="{{ $user->name }}" class="rounded-circle me-2"
                                                            width="40" height="40">
                                                        <label class="form-check-label text-white fs-6 fw-bolder"
                                                            for="user-{{ $user->id }}">
                                                            {{ $user->name }}
                                                        </label>
                                                    </div>
                                                    <hr>
                                                @empty
                                                    <p class="text-white">
                                                        @if ($search_member)
                                                            No "{{ $search_member }}" user
                                                            found.
                                                        @else
                                                            No @if ($search)
                                                                "{{ $search }}"
                                                            @endif user found.
                                                        @endif
                                                    </p>
                                                @endforelse
                                            </div>
                                            @error('member')
                                                <span class="text-danger">To proceed, Please select atleast 1
                                                    person.</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span wire:loading.remove wire:target='addMemberToGc'>Add</span>
                                        <span wire:loading wire:target='addMemberToGc'>Adding...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-danger" wire:offline>
                    <h6 class="text-center" style="font-size: 12px !important;">Whoops, your device has lost
                        connection. The web page
                        you are viewing is offline.</h6>
                </div>
                <div id="chatMessages"
                    class="flex-grow-1 overflow-auto p-3 d-flex flex-column-reverse position-relative">

                    <button id="backToBottom" onclick="scrollToBottom()" class="btn btn-dark rounded-circle"
                        style="position: fixed; display: none; left: 60%; transform: translateX(-50%); bottom: 100px; z-index: 9999;">
                        <i class="far fa-arrow-down"></i>
                    </button>

                    <span class="text-white text-end" style="font-size: 12px !important;" wire:loading
                        wire:target='sendMessage'>
                        Sending...
                    </span>

                    @forelse ($convos as $index => $convo)

                        @if ($convo->user_id === auth()->user()->id)
                            @if (!empty($convo->message) || !empty($convo->attachment))
                                <div class="p-2 mb-2" style="font-size: 13px;">
                                    <div class="d-flex justify-content-end">
                                        @if ($convo->status === 'unsent')
                                            <div class="dropdown mt-2 ms-2 drop-set">
                                                <button class="btn btn-sm btn-link text-decoration-none"
                                                    type="button" id="dropdownMenuButton{{ $convo->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-vertical text-white"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu"
                                                    aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                                    <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                            href="#"
                                                            wire:click='deleteForYou({{ $convo->id }})'>
                                                            <div class="d-flex">
                                                                <span class="col-2"><i
                                                                        class="far fa-trash"></i></span>
                                                                <span class="col-10"><strong>Remove</strong></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div style="background-color: rgba(29, 29, 29, 0.386);"
                                                class="border shadow p-2 me-2 rounded-start-4 rounded-bottom-4 justify-content-end"
                                                title="{{ $convo->created_at->format('l, g:i A') }}">
                                                <span class="fst-italic mt-2"
                                                    style='font-size: 12px; color:rgb(232, 178, 178);'>
                                                    {{ auth()->user()->id ? 'You unsent a message' : 'Message unsent' }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="dropdown mt-2 drop-set">
                                                <button class="btn btn-sm btn-link text-decoration-none"
                                                    type="button" id="dropdownMenuButton{{ $convo->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-vertical text-white"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu"
                                                    aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                                    <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                            href="#" data-bs-toggle="modal"
                                                            data-bs-target="#toEditMessage"
                                                            wire:click='editMessage({{ $convo->id }})'>
                                                            <div class="d-flex">
                                                                <span class="col-2"><i class="far fa-pen"></i></span>
                                                                <span class="col-10"><strong>Edit</strong></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                            href="#"
                                                            wire:click='replyToChat({{ $convo->id }})'>
                                                            <div class="d-flex">
                                                                <span class="col-2"><i
                                                                        class="far fa-reply"></i></span>
                                                                <span class="col-10"><strong>Reply</strong></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                            href="#"
                                                            wire:click='removeForEveryone({{ $convo->id }})'>
                                                            <div class="d-flex">
                                                                <span class="col-2"><i
                                                                        class="far fa-comment-xmark"></i></span>
                                                                <span class="col-10"><strong>Unsent</strong></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                    <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                            href="#"
                                                            wire:click='deleteForYou({{ $convo->id }})'>
                                                            <div class="d-flex">
                                                                <span class="col-2"><i
                                                                        class="far fa-trash"></i></span>
                                                                <span class="col-10"><strong>Remove</strong></span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="dropdown mt-2 drop-set">
                                                <button class="btn btn-sm btn-link text-decoration-none"
                                                    type="button" id="dropdownMenuButton{{ $convo->id }}"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="far fa-smile text-white"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu"
                                                    style="width: 270px; overflow: auto;"
                                                    aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                                    <li class="px-2">
                                                        <div>
                                                            @foreach ($emojiReaction as $emoji)
                                                                @php
                                                                    $userReaction = $convo->groupChatReactions->first(
                                                                        function ($reaction) use ($emoji) {
                                                                            return $reaction->emoji->id ===
                                                                                $emoji->id &&
                                                                                $reaction->user_id === auth()->id();
                                                                        },
                                                                    );
                                                                @endphp
                                                                <button type="button"
                                                                    class="btn m-0 p-0 fs-4 btn-link text-decoration-none"
                                                                    style="{{ $userReaction ? 'background-color: rgba(156, 151, 151, 0.500); color: white;' : '' }}"
                                                                    wire:click="handleEmojiClick({{ $emoji->id }}, {{ $convo->id }})">{{ $emoji->value }}</button>
                                                            @endforeach
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div wire:ignore class="modal" id="toEditMessage" tabindex="-1"
                                                aria-labelledby="toEditMessageLabel" aria-hidden="true">
                                                <div
                                                    class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content bg-secondary">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5 text-white"
                                                                id="toEditMessageLabel">
                                                                Updating a message...
                                                            </h1>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" id="update-textarea" rows='3' name="" placeholder="Type a message"
                                                                wire:model='messageEdit'></textarea>
                                                            @error('messageEdit')
                                                                <span class="text-danger mt-1">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button wire:loading.attr="disabled" wire:target="update"
                                                                type="button" wire:click='update'
                                                                class="btn btn-primary">
                                                                <span wire:loading.remove wire:target="update">Save
                                                                    changes</span><span wire:loading
                                                                    wire:target="update">Saving...</span>
                                                            </button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="position-relative me-2 justify-content-end"
                                                title="{{ $convo->created_at->format('l, g:i A') }}"
                                                style="max-width: 85%;">

                                                @foreach ($convo->groupChatReplies as $reply)
                                                    @if ($reply->fromGroupChatContent?->message)
                                                        <div>
                                                            <span class="rounded me-2">
                                                                <small class="p-1 rounded"
                                                                    style="background-color:rgba(92, 92, 92, 0.46);"><i
                                                                        class="far fa-reply"></i>
                                                                    @if ($reply->fromGroupChatContent?->status === 'unsent')
                                                                        <span class="fst-italic">
                                                                            Message unsent
                                                                        </span>
                                                                    @else
                                                                        You replied to
                                                                        <strong>{{ Str::limit($reply->fromGroupChatContent?->message, 12) ?: 'attachment' }}
                                                                    @endif
                                                                    </strong>
                                                                </small>
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach

                                                <div class="border p-2 rounded-start-4 rounded-bottom-4 shadow"
                                                    style="background-color: rgba(29, 29, 29, 0.386);">
                                                    {{-- Display the message if it exists --}}
                                                    @if (!empty($convo->message))
                                                        <p class="text-break">
                                                            @if ($convo->message === '(y)')
                                                                <i class="fa-solid fa-thumbs-up text-primary"
                                                                    style="font-size: 60px;"></i>
                                                            @else
                                                                {!! nl2br($convo->message) !!}
                                                            @endif
                                                        </p>
                                                    @endif

                                                    {{-- Display the attachments if they exist --}}
                                                    @if (!empty($convo->attachment))
                                                        <p>
                                                            @foreach ($convo->attachment as $index => $attach)
                                                                @php
                                                                    $extension = pathinfo($attach, PATHINFO_EXTENSION);
                                                                @endphp
                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp']))
                                                                    <a class="text-decoration-none" href="#"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#imageModal{{ $convo->id }}">
                                                                        <img wire:click.prevent="setActiveImage({{ $index }})"
                                                                            src="{{ Storage::url($attach) }}"
                                                                            style="max-width: 200px;"
                                                                            alt="Attachment Image" class="mb-2 me-2">
                                                                    </a>
                                                                @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                                                    <audio controls class="mb-2">
                                                                        <source src="{{ Storage::url($attach) }}"
                                                                            type="audio/{{ $extension }}">
                                                                        Your browser does not support the audio element.
                                                                    </audio>
                                                                @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                                                    <video controls width="250" class="mb-2">
                                                                        <source src="{{ Storage::url($attach) }}"
                                                                            type="video/{{ $extension }}">
                                                                        Your browser does not support the video element.
                                                                    </video>
                                                                @else
                                                                    <a href="{{ Storage::url($attach) }}" download
                                                                        class="text-white text-decoration-none">
                                                                        <i style="font-size: 100px;"
                                                                            @if (in_array($extension, ['zip'])) class="far fa-file-zip me-2" @elseif(in_array($extension, ['docs', 'docx', 'doc'])) class="far fa-file-word me-2" @elseif(in_array($extension, ['ppt', 'pptx'])) class="far fa-file-powerpoint me-2"
                                            @elseif(in_array($extension, ['xlsx', 'xlsm', 'xlsb', 'xltx']))
                                            class="far fa-file-excel me-2" @elseif (in_array($extension, ['pdf']))
                                            class="far fa-file-pdf me-2" @elseif(in_array($extension, ['sql', 'html', 'js', 'jsx', 'ts', 'tsx', 'php', 'py']))
                                            class="far fa-file-code me-2" @else class="fas fa-file-zipper me-2" @endif>
                                                                        </i>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                    <small>
                                                        @if ($convo->created_at->diffForHumans() < 1)
                                                            Just now
                                                        @else
                                                            {{ $convo->created_at->diffForHumans() }}
                                                        @endif
                                                    </small>
                                                    <span class="mx-0 mb-0 d-flex justify-content-end">
                                                        @if (!empty($convo->groupChatSeenBies) && $loop->first)
                                                            @foreach ($convo->groupChatSeenBies as $seenBy)
                                                                <img style="margin-left: -4px;"
                                                                    @if ($seenBy->profile_picture === null) src='/images/profile.png'
                                    @else
                                    src="{{ Storage::url($seenBy->profile_picture) }}" @endif
                                                                    width='15' height='15'
                                                                    class='rounded-circle animate__animated animate__wobble'
                                                                    alt="{{ $seenBy->name }}"
                                                                    title='{{ $seenBy->name }}'>
                                                            @endforeach
                                                        @endif
                                                    </span>
                                                    <div class="position-absolute"
                                                        style="bottom: -17px; right: 0; width: 300px;">
                                                        <div class="d-flex justify-content-end">
                                                            @php
                                                                $groupedReactions = $convo->groupChatReactions
                                                                    ->groupBy(function ($reaction) {
                                                                        return $reaction->emoji->value;
                                                                    })
                                                                    ->map(function ($group) {
                                                                        return $group;
                                                                    });
                                                            @endphp
                                                            @foreach ($groupedReactions as $emoji => $group)
                                                                <div data-bs-toggle="modal"
                                                                    data-bs-target="#reactionsModal{{ $convo->id }}"
                                                                    style="background-color: #3333338c; cursor: pointer;"
                                                                    class="fs-6 px-1 rounded-pill animate__animated animate__heartBeat">
                                                                    <span>{{ $emoji }}</span>
                                                                    <span class="">
                                                                        @if ($group->count() > 1)
                                                                            {{ $group->count() }}
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                                <div wire:ignore.self class="modal fade"
                                                                    id="reactionsModal{{ $convo->id }}"
                                                                    tabindex="-1"
                                                                    aria-labelledby="reactionsModal{{ $convo->id }}Label"
                                                                    aria-hidden="true">
                                                                    <div
                                                                        class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
                                                                        <div class="bg-secondary modal-content">
                                                                            <div class="modal-header">
                                                                                <h1 class="modal-title fs-5"
                                                                                    id="reactionsModal{{ $convo->id }}Label">
                                                                                    Reactions</h1>
                                                                                <button type="button"
                                                                                    class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body p-0">
                                                                                @foreach ($convo->groupChatReactions as $reaction)
                                                                                    <div id="reaction_content"
                                                                                        class="d-flex justify-content-between align-items-start rounded p-2 m-2"
                                                                                        @if ($reaction->user_id === auth()->user()->id) wire:click='handleEmojiClick({{ $reaction->emoji_id }}, {{ $convo->id }})' style="cursor: pointer;" @endif>
                                                                                        <div
                                                                                            class="d-flex align-items-center">
                                                                                            <img @if ($reaction->user->profile_picture === null) src="/images/profile.png"
                                                                @else
                                                                src="{{ Storage::url($reaction->user->profile_picture) }}" @endif
                                                                                                alt="{{ $reaction->user->name }}"
                                                                                                width="25"
                                                                                                height="25"
                                                                                                class="rounded-circle me-2">
                                                                                            <div>
                                                                                                <div>
                                                                                                    {{ $reaction->user->groupChats->firstWhere('id', $groupConvo->id)?->pivot->gc_nickname ?: $reaction->user->name }}
                                                                                                </div>
                                                                                                @if ($reaction->user_id === auth()->user()->id)
                                                                                                    <div class="text-light fst-italic"
                                                                                                        style="font-size: 0.85rem;">
                                                                                                        Tap to remove
                                                                                                        reaction
                                                                                                    </div>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div
                                                                                            @if ($reaction->user_id === auth()->user()->id) class="mt-3" @endif>
                                                                                            {{ $reaction->emoji->value }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <a title="{{ $convo->user->name }}"
                                            href="/profile-info/{{ $convo->user->username }}" wire:navigate>
                                            <img @if ($convo->user->profile_picture === null) src="/images/profile.png"
                            @else
                            src="{{ Storage::url($convo->user->profile_picture) }}" @endif
                                                style="width: 30px; height: 30px;" alt="Profile Image"
                                                class="rounded-circle">
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Display the message with attachments --}}
                            @if (!empty($convo->attachment) || !empty($convo->message))
                                <div class="p-2 mb-2" style="font-size: 13px;">
                                    <div class="d-flex">
                                        {{-- Profile Image --}}
                                        <a href="/profile-info/{{ $convo->user->username }}" wire:navigate
                                            title="{{ $convo->user->name }}">
                                            <img @if ($convo->user->profile_picture === null) src="/images/profile.png"
                            @else
                            src="{{ Storage::url($convo->user->profile_picture) }}" @endif
                                                style="width: 30px; height: 30px;" alt="Profile Image"
                                                class="rounded-circle"></a>

                                        {{-- Message and Attachments --}}
                                        <div class="position-relative ms-2"
                                            title="{{ $convo->created_at->format('l, g:i A') }}"
                                            style="max-width: 85%;">
                                            @foreach ($convo->groupChatReplies as $reply)
                                                @if ($reply->fromGroupchatContent?->message)
                                                    <div>
                                                        <span class="rounded me-2">
                                                            <small class="p-1 rounded"
                                                                style="background-color:rgba(92, 92, 92, 0.46);"><i
                                                                    class="far fa-reply"></i>
                                                                @if ($reply->fromGroupChatContent?->status === 'unsent')
                                                                    <span class="fst-italic">
                                                                        Message unsent
                                                                    </span>
                                                                @else
                                                                    {{ $convo->user->groupChats->firstWhere('id', $convo->group_chat_id)?->pivot->gc_nickname ?: $convo->user->name }}
                                                                    replied to
                                                                    <strong>{{ Str::limit($reply->fromGroupChatContent->message, 12) ?: 'attachment' }}
                                                                @endif
                                                                </strong>
                                                            </small>
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <div class="border p-2 rounded-end-4 rounded-bottom-4 shadow"
                                                style="background-color: rgba(29, 29, 29, 0.386);">
                                                <strong
                                                    title="{{ $convo->user->name }}">{{ $convo->user->groupChats->firstWhere('id', $convo->group_chat_id)?->pivot->gc_nickname ?: $convo->user->name }}</strong>
                                                {{-- If message is unsent --}}
                                                @if ($convo->status === 'unsent')
                                                    <p class="text-break">
                                                        <span class="fst-italic mt-2"
                                                            style='font-size: 12px; color:rgb(232, 178, 178);'>
                                                            Message unsent
                                                        </span>
                                                    </p>
                                                @else
                                                    {{-- Display message if available --}}
                                                    @if (!empty($convo->message))
                                                        <p class="text-break">
                                                            @if ($convo->message === '(y)')
                                                                <i class="fa-solid fa-thumbs-up text-primary"
                                                                    style="font-size: 60px;"></i>
                                                            @else
                                                                {!! nl2br($convo->message) !!}
                                                            @endif
                                                        </p>
                                                    @endif

                                                    {{-- Display attachments if available --}}
                                                    @if (!empty($convo->attachment))
                                                        <p class="mt-2">
                                                            @foreach ($convo->attachment as $index => $attach)
                                                                @php
                                                                    $extension = pathinfo($attach, PATHINFO_EXTENSION);
                                                                @endphp
                                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp']))
                                                                    <a class="text-decoration-none" href="#"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#imageModal{{ $convo->id }}">
                                                                        <img wire:click.prevent="setActiveImage({{ $index }})"
                                                                            src="{{ Storage::url($attach) }}"
                                                                            style="max-width: 200px;"
                                                                            alt="Attachment Image" class="mb-2 me-2">
                                                                    </a>
                                                                @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                                                    <audio controls class="mb-2 me-2">
                                                                        <source src="{{ Storage::url($attach) }}"
                                                                            type="audio/{{ $extension }}">
                                                                        Your browser does not support the audio element.
                                                                    </audio>
                                                                @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                                                    <video controls width="250" class="mb-2 me-2">
                                                                        <source src="{{ Storage::url($attach) }}"
                                                                            type="video/{{ $extension }}">
                                                                        Your browser does not support the video element.
                                                                    </video>
                                                                @else
                                                                    <a href="{{ Storage::url($attach) }}" download
                                                                        class="text-white text-decoration-none">
                                                                        <i style="font-size: 100px;"
                                                                            @if (in_array($extension, ['zip'])) class="far fa-file-zip me-2" @elseif(in_array($extension, ['docs', 'docx', 'doc'])) class="far fa-file-word me-2" @elseif(in_array($extension, ['ppt', 'pptx'])) class="far fa-file-powerpoint me-2"
                                            @elseif(in_array($extension, ['xlsx', 'xlsm', 'xlsb', 'xltx']))
                                            class="far fa-file-excel me-2" @elseif(in_array($extension, ['pdf']))
                                            class="far fa-file-pdf me-2" @elseif(in_array($extension, ['sql', 'html', 'js', 'jsx', 'ts', 'tsx', 'php', 'py']))
                                            class="far fa-file-code me-2" @else class="fas fa-file-zipper me-2" @endif></i>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                @endif
                                                <small>
                                                    @if ($convo->created_at->diffForHumans() < 1)
                                                        Just now
                                                    @else
                                                        {{ $convo->created_at->diffForHumans() }}
                                                    @endif
                                                </small>
                                                <span class="mx-0 mb-0 d-flex">
                                                    @if (!empty($convo->groupChatSeenBies) && $loop->first)
                                                        @foreach ($convo->groupChatSeenBies as $seenBy)
                                                            <img style="margin-left: -4px;"
                                                                @if ($seenBy->profile_picture === null) src='/images/profile.png'
                                    @else
                                    src="{{ Storage::url($seenBy->profile_picture) }}" @endif
                                                                width='15' height='15'
                                                                class='rounded-circle animate__animated animate__wobble'
                                                                alt="{{ $seenBy->name }}"
                                                                title='{{ $seenBy->name }}'>
                                                        @endforeach
                                                    @endif
                                                </span>
                                                <div class="position-absolute"
                                                    style="bottom: -17px; left: 0; width: 300px;">
                                                    <div class="d-flex">
                                                        @php
                                                            $groupedReactions = $convo->groupChatReactions
                                                                ->groupBy(function ($reaction) {
                                                                    return $reaction->emoji->value;
                                                                })
                                                                ->map(function ($group) {
                                                                    return $group;
                                                                });
                                                        @endphp
                                                        @foreach ($groupedReactions as $emoji => $group)
                                                            <div data-bs-toggle="modal"
                                                                data-bs-target="#reactionsModal{{ $convo->id }}"
                                                                style="background-color: #3333338c; cursor: pointer;"
                                                                class="fs-6 px-1 rounded-pill animate__animated animate__heartBeat">
                                                                <span>{{ $emoji }}</span>
                                                                <span class="">
                                                                    @if ($group->count() > 1)
                                                                        {{ $group->count() }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div wire:ignore.self class="modal fade"
                                                                id="reactionsModal{{ $convo->id }}"
                                                                tabindex="-1"
                                                                aria-labelledby="reactionsModal{{ $convo->id }}Label"
                                                                aria-hidden="true">
                                                                <div
                                                                    class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm">
                                                                    <div class="bg-secondary modal-content">
                                                                        <div class="modal-header">
                                                                            <h1 class="modal-title fs-5"
                                                                                id="reactionsModal{{ $convo->id }}Label">
                                                                                Reactions</h1>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body p-0">
                                                                            @foreach ($convo->groupChatReactions as $reaction)
                                                                                <div id="reaction_content"
                                                                                    class="d-flex justify-content-between align-items-start rounded p-2 m-2"
                                                                                    @if ($reaction->user_id === auth()->user()->id) wire:click='handleEmojiClick({{ $reaction->emoji_id }}, {{ $convo->id }})' style="cursor: pointer;" @endif>
                                                                                    <div
                                                                                        class="d-flex align-items-center">
                                                                                        <img @if ($reaction->user->profile_picture === null) src="/images/profile.png"
                                                                @else
                                                                src="{{ Storage::url($reaction->user->profile_picture) }}" @endif
                                                                                            alt="{{ $reaction->user->name }}"
                                                                                            width="25"
                                                                                            height="25"
                                                                                            class="rounded-circle me-2">
                                                                                        <div>
                                                                                            <div>
                                                                                                {{ $reaction->user->groupChats->firstWhere('id', $groupConvo->id)?->pivot->gc_nickname ?: $reaction->user->name }}
                                                                                            </div>
                                                                                            @if ($reaction->user_id === auth()->user()->id)
                                                                                                <div class="text-light fst-italic"
                                                                                                    style="font-size: 0.85rem;">
                                                                                                    Tap to remove
                                                                                                    reaction
                                                                                                </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        @if ($reaction->user_id === auth()->user()->id) class="mt-3" @endif>
                                                                                        {{ $reaction->emoji->value }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown mt-2 drop-set">
                                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="far fa-smile text-white"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow drop-set-menu"
                                                style="width: 270px; overflow: auto;"
                                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                                <li class="px-2">
                                                    <div>
                                                        @foreach ($emojiReaction as $emoji)
                                                            @php
                                                                $userReaction = $convo->groupChatReactions->first(
                                                                    function ($reaction) use ($emoji) {
                                                                        return $reaction->emoji->id === $emoji->id &&
                                                                            $reaction->user_id === auth()->id();
                                                                    },
                                                                );
                                                            @endphp
                                                            <button type="button"
                                                                class="btn m-0 p-0 fs-4 btn-link text-decoration-none"
                                                                style="{{ $userReaction ? 'background-color: rgba(156, 151, 151, 0.500); color: white;' : '' }}"
                                                                wire:click="handleEmojiClick({{ $emoji->id }}, {{ $convo->id }})">{{ $emoji->value }}</button>
                                                        @endforeach
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="dropdown mt-2 drop-set">
                                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-vertical text-white"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow drop-set-menu"
                                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                                <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                        href="#"
                                                        wire:click='replyToChat({{ $convo->id }})'>
                                                        <div class="d-flex">
                                                            <span class="col-2"><i class="far fa-reply"></i></span>
                                                            <span class="col-10"><strong>Reply</strong></span>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li><a class="dropdown-item btn-link text-decoration-none btn"
                                                        href="#"
                                                        wire:click='deleteForYou({{ $convo->id }})'>
                                                        <div class="d-flex">
                                                            <span class="col-2"><i class="far fa-trash"></i></span>
                                                            <span class="col-10"><strong>Remove</strong></span>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endif
                        <div wire:ignore.self class="modal fade" id="imageModal{{ $convo->id }}" tabindex="-1"
                            aria-labelledby="imageModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content bg-secondary">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imageModalLabel">
                                            <span class="text-center bg-dark text-white rounded-1 px-1"
                                                style="font-size: 12px !important;">
                                                Sent on: <span
                                                    id="date">{{ $convo->created_at->format('F d, Y \a\t h:i A') }}</span>
                                            </span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-0">
                                        <div id="carouselExample{{ $convo->id }}" class="carousel slide">
                                            <div class="carousel-inner">
                                                @foreach ($convo->attachment as $index => $image)
                                                    <div
                                                        class="carousel-item mt-3 position-relative {{ $index === $activeImageIndex ? 'active' : '' }}">
                                                        <a href="{{ Storage::url($image) }}" download
                                                            class="btn btn-link btn-sm bg-primary text-white text-decoration-none position-absolute"
                                                            style="margin-left: -47px; margin-top: -15px;">
                                                            <i class="far fa-download"></i> Download
                                                        </a>
                                                        <img src="{{ Storage::url($image) }}" class="d-block w-100"
                                                            alt="Image Preview">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if (count($convo->attachment) > 1)
                                                <button class="carousel-control-prev" type="button"
                                                    data-bs-target="#carouselExample{{ $convo->id }}"
                                                    data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon"
                                                        aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button class="carousel-control-next" type="button"
                                                    data-bs-target="#carouselExample{{ $convo->id }}"
                                                    data-bs-slide="next">
                                                    <span class="carousel-control-next-icon"
                                                        aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if ($lastUnseen?->id === $convo->id)
                            <div class="d-flex align-items-center justify-content-center"
                                wire:key='{{ $convo->id }}'>
                                <hr class="custom-hr flex-grow-1" style="margin: 0 0.5rem;">
                                <span class="mx-2 text-white" style="font-size: 11px;">
                                    Unread messages
                                </span>
                                <hr class="custom-hr flex-grow-1" style="margin: 0 0.5rem;">
                            </div>
                        @endif
                    @empty
                        <p class="text-center">Start a chat in <strong>{{ $groupConvo->group_chat_name }}</strong>
                            Group Chat
                        </p>
                    @endforelse
                    @if ($convos->count() < $messageCount)
                        <p class="text-center">
                            <button wire:loading.attr='disabled' wire:target="loadMoreMessage" type="button"
                                class="btn btn-link btn-sm text-white text-decoration-none"
                                wire:click='loadMoreMessage'>
                                <span wire:loading.remove style="font-size: 12px !important;"
                                    wire:target="loadMoreMessage">Load
                                    more</span>

                                <div wire:loading wire:target="loadMoreMessage" class="spinner-border"></div>
                            </button>
                        </p>
                    @endif
                </div>
                <div class="px-3 pb-3">
                    @if ($attachment && count($attachment) > 0)
                        <div class="d-flex flex-wrap justify-content-end me-5 position-relative"
                            style="max-height: 150px; overflow-y: auto; overflow-x: hidden;">
                            <button type="button" wire:click="clearAllAttachments"
                                class="btn btn-dark btn-sm position-absolute top-0 start-0"
                                style="z-index: 99;">Remove
                                all</button>
                            @foreach ($attachment as $index => $attach)
                                @if ($attach)
                                    <div class="me-2 position-relative" wire:key="attachment-{{ $index }}">
                                        <div class="position-absolute w-100 d-flex justify-content-end"
                                            id="image">
                                            <button type="button"
                                                wire:click='removeTempUrlImg({{ $index }})'
                                                class="btn btn-link text-decoration-none text-black"
                                                style="margin-right: -8px;"><i
                                                    class="fas fa-circle-xmark fs-5"></i></button>
                                        </div>
                                        <!-- Display the attach based on its type -->
                                        @if (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'gif',
                                                'ico',
                                                'webp',
                                            ]))
                                            <img src="{{ $attach->temporaryUrl() }}" width="100"
                                                class="img-thumbnail" alt="Attach Image">
                                        @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['mp4', 'avi', 'mov']))
                                            <video src="{{ $attach->temporaryUrl() }}" controls
                                                width="200"></video>
                                        @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['mp3', 'wav']))
                                            <audio src="{{ $attach->temporaryUrl() }}" controls></audio>
                                        @else
                                            <h1>
                                                <a href="{{ $attach->temporaryUrl() }}" download
                                                    class="text-decoration-none text-white">
                                                    <i style="font-size: 100px;"
                                                        @if (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['zip'])) class="far fa-file-zip"
                                    @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['docs', 'docx', 'doc']))

                                    class="far fa-file-word"
                                    @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['ppt', 'pptx']))

                                    class="far fa-file-powerpoint"
                                    @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['xlsx', 'xlsm', 'xlsb', 'xltx']))

                                    class="far fa-file-excel"
                                    @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['pdf']))

                                    class="far fa-file-pdf"
                                    @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), [
                                            'sql',
                                            'html',
                                            'js',
                                            'jsx',
                                            'ts',
                                            'tsx',
                                            'php',
                                            'py',
                                        ]))

                                    class="far fa-file-code"
                                    @else
                                    class="far fa-file" @endif>
                                                    </i>
                                                </a>
                                            </h1>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <div x-show="uploading">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <progress max="100" x-bind:value="progress" class="w-100 mt-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"></div>
                                </progress>
                            </div>
                            <div class="ms-2">
                                <button type="button" class="btn btn-link text-decoration-none text-white p-0"
                                    x-on:click="cancelUpload">
                                    <i class="far fa-x"></i>
                                </button>
                            </div>
                        </div>
                        <span x-text="`${progress}%`"></span>
                    </div>

                    <form wire:submit.prevent='sendMessage' id="messageForm" class="border-top pt-3">
                        <div class="d-flex w-100 justify-items-center">
                            <div class="w-100 rounded-start-5 rounded-end-2 d-flex flex-column gap-1"
                                style="background-color: #999999;">
                                <div class="d-flex" style="min-width: 100px;">
                                    <div class="text-center d-flex align-items-end">
                                        <div class="d-flex w-100">
                                            <div class="col-6">
                                                <input id="attachment1" type="file" class="form-control d-none"
                                                    name="attachment1"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.rar,.zip,.js,.ts,.jsx,.tsx,.php,.html,.py,.mp3"
                                                    autocomplete="attachment1" autofocus wire:model='attachment'
                                                    multiple>
                                                <label for="attachment1"
                                                    class="form-label text-center text-white mt-3 ms-2"
                                                    style="cursor: pointer;">
                                                    <h4 class="me-2"><i class="far fa-paperclip fs-5"></i></h4>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <input id="attachment" type="file" class="form-control d-none"
                                                    name="attachment" accept="image/*,.mp4,.mov,.avi,.wmv,.mkv"
                                                    autocomplete="attachment" autofocus wire:model='attachment'
                                                    multiple>
                                                <label for="attachment" class="form-label text-center text-white mt-3"
                                                    style="cursor: pointer;">
                                                    <h4 class="me-3"><i class="far fa-image fs-5"></i></h4>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1" style="margin-top: 10px;">
                                        @if ($isReply)
                                            <div class="d-flex justify-content-between">
                                                <div>Replying to <strong>
                                                        @if ($unsentReply === 'unsent')
                                                            unsent message
                                                        @else
                                                            {{ Str::limit($replyContent, 5) ?: 'attachment' }}
                                                        @endif
                                                    </strong></div>
                                                <div>
                                                    <button type="button" wire:click='cancelReply'
                                                        class="btn btn-link btn-sm text-decoration-none text-light">
                                                        <i class="far fa-xmark"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        <textarea @if ($toSeen > 0) wire:click='gcSeen({{ $groupConvo->id }})' @endif
                                            class="form-control mb-2" id="textarea" rows='1' name="" placeholder="Type a message"
                                            wire:loading.attr='readonly' wire:target='sendMessage' wire:model='message' rows="1"
                                            @keydown.enter="submitForm(event)" @keydown.shift.enter="handleShiftEnter(event)" oninput="adjustTextArea(this)"></textarea>
                                    </div>
                                    <div class="mb-1 d-flex align-items-end">
                                        <div class="dropup mt-1 dropup">
                                            <button class="btn btn-lg pe-0" id="btn-emoji" type="button"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                aria-expanded="false" type="button">😄</button>
                                            <ul class="dropdown-menu" id="dropdrop"
                                                style="width: 300px; max-height: 300px; overflow: auto;">
                                                <li class="px-2">
                                                    <div class="my-3">
                                                        <input type="search" class="form-control"
                                                            placeholder="Search emoji" id="search-emoji">
                                                    </div>
                                                    <div class="d-flex flex-wrap justify-content-center align-items-center"
                                                        id="emoji-list">
                                                        @forelse ($emojis as $emoji)
                                                            <span class="emoji-item fs-4"
                                                                data-label="{{ $emoji->label }}"
                                                                style="cursor: pointer;">{{ $emoji->value }}</span>
                                                        @empty
                                                            <div>
                                                                <i class="far fa-face-thinking"></i>
                                                                <span class="emoji-item fs-6"
                                                                    style="cursor: pointer;">No emoji
                                                                    displayed</span>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                    <div class="py-5" id="no-emoji-found"
                                                        style="display: none; text-align: center;">
                                                        <i class="far fa-face-thinking"></i>
                                                        <span id="no-emoji-message" class="fs-6"></span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-lg ps-1" id="button-send" type="submit"><i
                                                class="far fa-paper-plane text-white fs-5"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" wire:click='sendLike' class="btn btn-link btn-sm"><i
                                        class="fas fa-thumbs-up text-primary" style="font-size: 25px;"></i></button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <style>
            #update-textarea {
                resize: none;
                background-color: #999999 !important;
                color: #f5f5f5 !important;
                border: none !important;
            }

            #update-textarea::placeholder {
                color: white !important;
                opacity: 1;
            }

            #update-textarea:focus {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
            }

            #textarea {
                resize: none;
                background-color: #999999 !important;
                color: #f5f5f5 !important;
                border: none !important;
            }

            #textarea::placeholder {
                color: white !important;
                opacity: 1;
            }

            #textarea:focus {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
            }

            #button-send {
                border: none !important;
            }

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

            .overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.46);
                z-index: 10;
            }

            #inputGcNickname {
                background-color: #999999 !important;
                color: #f5f5f5 !important;
                border: none !important;
                transition: opacity 0.3s ease;
            }

            #inputGcNickname::placeholder {
                color: white !important;
                transition: opacity 0.3s ease;
            }

            #btn-emoji {
                border: none !important;
            }

            #reaction_content:hover {
                background-color: #3131315a;
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

            #formCheck label {
                cursor: pointer;
            }

            #formCheck input {
                cursor: pointer;
            }

            .border {
                border-color: #8597ab !important;
            }
        </style>

        <script>
            document.addEventListener('livewire:navigate', function() {
                initializeChatScroll();
            });

            function initializeChatScroll() {
                const chatMessages = document.getElementById('chatMessages');
                const backToBottomBtn = document.getElementById('backToBottom');

                if (chatMessages) {
                    chatMessages.addEventListener('scroll', function() {
                        const scrollPosition = chatMessages.scrollTop;

                        if (scrollPosition < -200) {
                            backToBottomBtn.style.display = 'block';
                        } else {
                            backToBottomBtn.style.display = 'none';
                        }
                    });
                }

                if (backToBottomBtn) {
                    backToBottomBtn.addEventListener('click', scrollToBottom);
                }
            }

            function scrollToBottom() {
                const chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTo({
                        top: chatMessages.scrollHeight,
                        behavior: 'smooth'
                    });
                }
                const backToBottomBtn = document.getElementById('backToBottom');
                if (backToBottomBtn) {
                    backToBottomBtn.style.display = 'none';
                }
            }

            window.addEventListener('scrollBot', () => {
                scrollToBottom();
            });

            initializeChatScroll();
        </script>

        <script>
            document.addEventListener("livewire:navigated", function() {
                var chatMessages = document.getElementById("chatMessages");
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        </script>
        <script>
            document.addEventListener('livewire:navigated', () => {

                @this.on('toastr', (event) => {
                    const data = event
                    toastr[data[0].type](data[0].message, '', {
                        closeButton: true,
                        "progressBar": true,
                    });
                })
            })
        </script>

        <script>
            function isMediumOrLarger() {
                return window.innerWidth <= 768;
            }

            function submitForm(event) {

                if (!isMediumOrLarger() && !event.shiftKey) {
                    event.preventDefault();
                    @this.call('sendMessage');
                    document.getElementById('textarea').focus();
                }
            }

            function handleShiftEnter(event) {}

            function adjustTextArea(textArea) {
                textArea.style.height = 'auto';

                const newHeight = textArea.scrollHeight;
                const maxHeight = parseInt(window.getComputedStyle(textArea).lineHeight) * 5;

                if (newHeight > maxHeight) {
                    textArea.style.height = `${maxHeight}px`;
                    textArea.style.overflowY = 'scroll';
                } else {
                    textArea.style.height = `${newHeight}px`;
                    textArea.style.overflowY = 'hidden';
                }

                textArea.scrollTop = textArea.scrollHeight;
            }
        </script>

        <script>
            function setModalImage(imageUrl, date, convoId) {
                document.getElementById('modalImage' + convoId).src = imageUrl;
                document.getElementById('imgLink' + convoId).href = imageUrl;
                document.getElementById('date' + convoId).textContent = date;
            }
        </script>

        <script>
            document.addEventListener('livewire:navigated', function() {
                @this.on('closeModal', (data) => {
                    const eventData = Array.isArray(data) ? data[0] : data;
                    $('#toEditMessage').modal('hide');
                    $('#addMembersGc').modal('hide');
                    $(`#reactionsModal${eventData.convoId}`).modal('hide');

                    document.getElementById('toEditMessage').classList.remove('show');
                    document.getElementById('addMembersGc').classList.remove('show');
                    const reactionsModal = document.getElementById(`reactionsModal${eventData.convoId}`);
                    if (reactionsModal) {
                        reactionsModal.classList.remove('show');
                    }
                });
            });
        </script>
        <script>
            function drop_file_component() {
                return {
                    dropingFile: false,
                    uploading: false,
                    progress: 0,
                    handleFileSelect(event) {
                        if (event.target.files.length) {
                            this.uploadFiles(event.target.files)
                        }
                    },
                    handleFileDrop(event) {
                        if (event.dataTransfer.files.length > 0) {
                            this.uploadFiles(event.dataTransfer.files)
                        }
                    },
                    uploadFiles(files) {
                        const $this = this;
                        this.uploading = true
                        @this.uploadMultiple('attachment', files,
                            function(success) {
                                $this.uploading = false
                                $this.progress = 0
                            },
                            function(error) {
                                console.log('error', error)
                                $this.uploading = false;
                                $this.progress = 0;
                            },
                            function(event) {
                                $this.progress = event.detail.progress
                            }
                        )
                    },
                    cancelUpload() {
                        @this.$cancelUpload('attachment');
                        this.uploading = false;
                        this.progress = 0;
                        console.log('Canceling...');
                    },
                    handlePaste(event) {
                        const items = event.clipboardData.items;
                        const files = [];

                        for (const item of items) {
                            if (item.kind === 'file') {
                                files.push(item.getAsFile());
                            }
                        }

                        if (files.length > 0) {
                            event.preventDefault();
                            this.uploadFiles(files);
                        }
                    },
                    init() {
                        const textarea = document.getElementById('textarea');
                        if (textarea) {
                            textarea.addEventListener('paste', this.handlePaste.bind(this));
                        }
                    },
                    removeUpload(filename) {
                        @this.removeUpload('attachment', filename).then(() => {
                            this.uploading = false;
                            this.progress = 0;
                        }).catch(() => {
                            console.error('Error removing upload');
                        });
                    }
                }
            }
        </script>

        <script>
            document.addEventListener('livewire:navigated', function() {
                addEmojiClickListeners();
            });

            function addEmojiClickListeners() {
                document.querySelectorAll('.emoji-item').forEach(function(item) {
                    item.replaceWith(item.cloneNode(true));
                });

                document.querySelectorAll('.emoji-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        const emoji = item.textContent;
                        const textarea = document.getElementById('textarea');
                        if (textarea) {
                            textarea.value += emoji;
                            textarea.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            textarea.focus();
                        }
                    });
                });
            }

            addEmojiClickListeners();
        </script>

        <script>
            document.addEventListener('livewire:navigated', () => {
                const searchInput = document.getElementById('search-emoji');
                const emojiList = document.getElementById('emoji-list');
                const emojis = emojiList.getElementsByClassName('emoji-item');
                const noEmojiFound = document.getElementById('no-emoji-found');
                const noEmojiMessage = document.getElementById('no-emoji-message');

                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    let found = false;

                    Array.from(emojis).forEach(emoji => {
                        const label = emoji.getAttribute('data-label').toLowerCase();
                        if (label.includes(searchTerm)) {
                            emoji.style.display = '';
                            found = true;
                        } else {
                            emoji.style.display = 'none';
                        }
                    });

                    if (found) {
                        noEmojiFound.style.display = 'none';
                    } else {
                        noEmojiFound.style.display = 'block';
                        noEmojiMessage.textContent = `No "${searchTerm}" emoji found`;
                    }
                });
            });
        </script>

    </div>

</div>
