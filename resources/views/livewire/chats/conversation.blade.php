<div>
    <div class="d-flex bg-secondary position-relative text-white" style="height: 91.5vh;" x-data="drop_file_component()"
        x-on:drop="dropingFile = false" x-on:drop.prevent="
        handleFileDrop($event)
    " x-on:dragover.prevent="dropingFile = true" x-on:dragleave.prevent="dropingFile = false"
        x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
        x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
        x-on:livewire-upload-error="uploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress">
        <div class="overlay" x-show='dropingFile'></div>
        <div wire:ignore.self class="offcanvas offcanvas-start bg-dark text-white overflow-y-auto"
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
                @forelse ($combined as $item)
                @if ($item->type === 'groupChat')
                <a @if ($item->unseen_count > 0) wire:click='gcSeen({{ $item->id }})' @endif
                    href="/gc/{{ $item->group_chat_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        <span class="online-dot"></span>
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="35" height="35"
                            alt="Group Chat Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $item->group_chat_name }}</span>
                        @if ($item->unseen_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $item->unseen_count }}
                        </span>
                        @endif
                    </li>
                </a>
                @elseif ($item->type === 'user')
                <a href="/chats/{{ $item->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($item->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $item->id }})'
                    @endif>
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        @if ($item->status === 'online')
                        <span class="online-dot"></span>
                        @elseif ($item->status === 'away')
                        <span class="away-dot"></span>
                        @else
                        <span class="offline-dot"></span>
                        @endif
                        <img src="{{ $item->profile_picture ? Storage::url($item->profile_picture) : '/images/profile.png' }}"
                            width="35" height="35" alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $item->name }}</span>
                        @if ($item->unseen_sender_chats_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $item->unseen_sender_chats_count }}
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
        <div class="col-3 bg-light overflow-auto d-none d-md-block bg-dark">
            <div class="py-3 px-2 sticky-top bg-dark">
                <h3>Chats</h3>
                <input type="search" class="form-control" placeholder="Search conversation..."
                    wire:model.live.debounce.500ms='search'>
            </div>
            <ul class="list-group">

                {{-- @forelse ($users as $user)
                <a href="/chats/{{ $user->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($user->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $user->id }})'
                    @endif
                    >
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        @if ($user->status === 'online') <span class="online-dot"></span> @elseif($user->status ===
                        'away') <span class="away-dot"></span> @else <span class="offline-dot"></span> @endif
                        <img @if ($user->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($user->profile_picture) }}"
                        @endif width="35" height="35"
                        alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $user->name }}</span>
                        @if ($user->unseen_sender_chats_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">{{
                            $user->unseen_sender_chats_count }}</span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">No conversation.</p>
                @endforelse
                @forelse ($groupChats as $gc)
                <a wire:click='seen({{ $gc->id }})' href="/gc/{{ $gc->group_chat_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="35" height="35"
                            alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $gc->group_chat_name }}</span>
                        @if ($gc->unseen_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $gc->unseen_count }}
                        </span>
                        @endif
                    </li>
                </a>
                @empty
                <p class="text-center mt-5">
                    @if ($search)
                    No "{{ $search }}" found.
                    @else
                    No conversation yet.
                    @endif
                </p>
                @endforelse --}}
                @forelse ($combined as $item)
                @if ($item->type === 'groupChat')
                <a @if ($item->unseen_count > 0) wire:click='gcSeen({{ $item->id }})' @endif
                    href="/gc/{{ $item->group_chat_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1">
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        <span class="online-dot"></span>
                        <img src="https://cdn-icons-png.flaticon.com/512/166/166258.png" width="35" height="35"
                            alt="Group Chat Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $item->group_chat_name }}</span>
                        @if ($item->unseen_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $item->unseen_count }}
                        </span>
                        @endif
                    </li>
                </a>
                @elseif ($item->type === 'user')
                <a href="/chats/{{ $item->user_token }}" wire:navigate
                    class="mt-1 text-decoration-none rounded shadow mx-1" @if ($item->unseen_sender_chats_count > 0)
                    wire:click='seen({{ $item->id }})'
                    @endif>
                    <li class="list-group-item d-flex align-items-center bg-secondary text-white">
                        @if ($item->status === 'online')
                        <span class="online-dot"></span>
                        @elseif ($item->status === 'away')
                        <span class="away-dot"></span>
                        @else
                        <span class="offline-dot"></span>
                        @endif
                        <img src="{{ $item->profile_picture ? Storage::url($item->profile_picture) : '/images/profile.png' }}"
                            width="35" height="35" alt="Profile Image" class="rounded-circle">
                        <span class="ms-2 text-start" style="font-size: 12px;">{{ $item->name }}</span>
                        @if ($item->unseen_sender_chats_count > 0)
                        <span class="badge text-bg-primary ms-auto" style="font-size: 7px;">
                            {{ $item->unseen_sender_chats_count }}
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
            <nav class="navbar navbar-secondary bg-secondary border">
                <div class="container-fluid d-flex align-items-center">
                    <button class="btn btn-link text-decoration-none d-md-none d-sm-block text-black" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                        <i class="far fa-bars"></i>
                    </button>
                    <a class="navbar-brand d-flex align-items-center" href="/profile-info/{{ $userConvo->username }}"
                        wire:navigate>
                        <img @if ($userConvo->profile_picture === null)
                        src="/images/profile.png"
                        @else
                        src="{{ Storage::url($userConvo->profile_picture) }}"
                        @endif alt="Profile Image" width="30" height="30"
                        class="d-inline-block align-top rounded-circle me-2">
                        <span class="text-white fs-6"><strong>{{ $userConvo->name }}</strong>@if($userConvo->nickname)
                            <span class="text-white fst-italic"> - ({{ $userConvo->nickname }})</span>@endif</span>
                    </a>
                    <div class="dropdown ms-auto">
                        <a class="nav-link" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="far fa-circle-info fs-4"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end pe-3" aria-labelledby="navbarDropdownMenuLink">
                            <li>
                                <button class="dropdown-item btn btn-link text-decoration-none"
                                    wire:click='deleteConversation({{ $userConvo->id }})'>
                                    <div class="d-flex">
                                        <span class="col-2"><i class="far fa-trash"></i></span>
                                        <span class="col-10"><strong>Delete Conversation</strong></span>
                                    </div>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item btn btn-link text-decoration-none" href="#">
                                    <div class="d-flex">
                                        <span class="col-2"><i class="far fa-circle-info"></i></span>
                                        <span class="col-10"><strong>Report Conversation</strong></span>
                                    </div>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


            <div class="bg-danger" wire:offline>
                <h6 class="text-center" style="font-size: 12px;">Whoops, your device has lost connection. The web page
                    you are viewing is offline.</h6>
            </div>
            <div id="chatMessages" class="flex-grow-1 overflow-auto p-3 d-flex flex-column-reverse position-relative">

                <button id="backToBottom" onclick="scrollToBottom()" class="btn btn-dark rounded-circle"
                    style="position: fixed; display: none; left: 60%; transform: translateX(-50%); bottom: 90px; z-index: 9999;">
                    <i class="far fa-arrow-down"></i>
                </button>

                @php
                $latestMessageTimestamp = $convos->isNotEmpty() ? $convos->first()->created_at : null;
                $unreadSeparatorShown = false;

                @endphp
                @forelse ($convos as $index => $convo)

                @if ($convo->created_at->eq($latestMessageTimestamp) && auth()->user()->id === $convo->sender_id)
                <span class="text-white text-end" style="font-size: 12px;" wire:loading.remove
                    wire:target='sendMessage'>
                    @if ($userConvo->status === 'offline')
                    {{ $convo->is_seen ? 'Seen' : 'Sent' }}
                    @else
                    {{ $convo->is_seen ? 'Seen' : 'Delivered' }}
                    @endif
                </span>
                <span class="text-white text-end" style="font-size: 12px;" wire:loading wire:target='sendMessage'>
                    Sending...
                </span>
                @endif

                @if ($convo->sender_id === auth()->user()->id)
                @if (!empty($convo->message) || !empty($convo->attachment))
                <div class="p-2" style="font-size: 13px;">
                    <div class="d-flex justify-content-end">
                        @if ($convo->status === 'unsent')
                        <div class="dropstart mt-2 drop-set">
                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical text-white"></i>
                            </button>
                            <ul class="dropdown-menu shadow drop-set-menu"
                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                <li><a class="dropdown-item btn-link text-decoration-none btn" href="#"
                                        wire:click='deleteForYou({{ $convo->id }})'>
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-trash"></i></span>
                                            <span class="col-10"><strong>Remove</strong></span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="border shadow p-2 me-2 rounded-start-4 rounded-bottom-4 justify-content-end"
                            title="@if ($userConvo->status === 'offline') {{ $convo->is_seen ? 'Seen' : 'Sent' }} @else {{ $convo->is_seen ? 'Seen' : 'Delivered' }} @endif, {{ $convo->created_at->format('l, g:i A') }}">
                            <span class="fst-italic text-muted mt-2" style='font-size: 12px;'>
                                {{ auth()->user()->id ? 'You unsent a message' : 'Message unsent' }}
                            </span>
                        </div>
                        @else
                        <div class="dropdown mt-2 drop-set">
                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical text-white"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu"
                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                <li>
                                    <a class="dropdown-item btn-link text-decoration-none btn" href="#"
                                        data-bs-toggle="modal" data-bs-target="#toEditMessage"
                                        wire:click='editMessage({{ $convo->id }})'>
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-pen"></i></span>
                                            <span class="col-10"><strong>Edit</strong></span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item btn-link text-decoration-none btn" href="#">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-reply"></i></span>
                                            <span class="col-10"><strong>Reply</strong></span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item btn-link text-decoration-none btn" href="#"
                                        wire:click='removeForEveryone({{ $convo->id }})'>
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-comment-xmark"></i></span>
                                            <span class="col-10"><strong>Unsent</strong></span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item btn-link text-decoration-none btn" href="#"
                                        wire:click='deleteForYou({{ $convo->id }})'>
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-trash"></i></span>
                                            <span class="col-10"><strong>Remove</strong></span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropstart mt-2 drop-set">
                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-smile text-white"></i>
                            </button>
                            {{-- <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu" id="dropdrop"
                                style="width: 300px; max-height: 300px; overflow: auto;"
                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                <li class="px-2">
                                    <div class="my-3">
                                        <input type="search" class="form-control" placeholder="Search emoji"
                                            id="search-emoji">
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-center align-items-center"
                                        id="emoji-list">
                                        @forelse ($emojis as $emoji)
                                        <span class="emoji-item fs-4" data-label="{{ $emoji->label }}"
                                            style="cursor: pointer;">{{ $emoji->value }}</span>
                                        @empty
                                        <div>
                                            <i class="far fa-face-thinking"></i>
                                            <span class="emoji-item fs-6" style="cursor: pointer;">No emoji
                                                displayed</span>
                                        </div>
                                        @endforelse
                                    </div>
                                    <div class="py-5" id="no-emoji-found" style="display: none; text-align: center;">
                                        <i class="far fa-face-thinking"></i>
                                        <span id="no-emoji-message" class="fs-6"></span>
                                    </div>
                                </li>
                            </ul> --}}
                        </div>
                        <div wire:ignore class="modal" id="toEditMessage" tabindex="-1"
                            aria-labelledby="toEditMessageLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content bg-secondary">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5 text-white" id="toEditMessageLabel">
                                            Updating a message...
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea class="form-control" id="update-textarea" rows='3' name=""
                                            placeholder="Type a message" wire:model='messageEdit'></textarea>
                                        @error('messageEdit')
                                        <span class="text-danger mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="modal-footer">
                                        <button wire:loading.attr="disabled" wire:target="update" type="button"
                                            wire:click='update' class="btn btn-primary">
                                            <span wire:loading.remove wire:target="update">Save
                                                changes</span><span wire:loading wire:target="update">Saving...</span>
                                        </button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border p-2 me-2 rounded-start-4 rounded-bottom-4 justify-content-end shadow"
                            title="@if ($userConvo->status === 'offline') {{ $convo->is_seen ? 'Seen' : 'Sent' }} @else {{ $convo->is_seen ? 'Seen' : 'Delivered' }} @endif, {{ $convo->created_at->format('l, g:i A') }}"
                            style="max-width: 85%;">

                            {{-- Display the message if it exists --}}
                            @if (!empty($convo->message))
                            <p class="text-break">
                                @if($convo->message === '(y)')
                                <i class="fa-solid fa-thumbs-up text-primary" style="font-size: 60px;"></i>
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
                                <a class="text-decoration-none" href="#" data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{$convo->id}}">
                                    <img wire:click.prevent="setActiveImage({{ $index }})"
                                        src="{{ Storage::url($attach) }}" style="max-width: 200px;"
                                        alt="Attachment Image" class="mb-2 me-2">
                                </a>
                                @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                <audio controls class="mb-2">
                                    <source src="{{ Storage::url($attach) }}" type="audio/{{ $extension }}">
                                    Your browser does not support the audio element.
                                </audio>
                                @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                <video controls width="250" class="mb-2">
                                    <source src="{{ Storage::url($attach) }}" type="video/{{ $extension }}">
                                    Your browser does not support the video element.
                                </video>
                                @else
                                <a href="{{ Storage::url($attach) }}" download class="text-white text-decoration-none">
                                    <i style="font-size: 100px;" @if (in_array($extension, ['zip']))
                                        class="far fa-file-zip me-2" @elseif(in_array($extension, ['docs', 'docx'
                                        , 'doc' ])) class="far fa-file-word me-2" @elseif(in_array($extension,
                                        ['ppt', 'pptx' ])) class="far fa-file-powerpoint me-2"
                                        @elseif(in_array($extension, ['xlsx', 'xlsm' , 'xlsb' , 'xltx' ]))
                                        class="far fa-file-excel me-2" @elseif (in_array($extension, ['pdf']))
                                        class="far fa-file-pdf me-2" @elseif(in_array($extension, ['sql', 'html' , 'js'
                                        , 'jsx' , 'ts' , 'tsx' , 'php' , 'py' ])) class="far fa-file-code me-2" @else
                                        class="fas fa-file-zipper me-2" @endif>
                                    </i>
                                </a>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            <small>@if ($convo->created_at->diffForHumans() < 1) Just now @else {{ $convo->
                                    created_at->diffForHumans() }} @endif</small>
                        </div>
                        @endif
                        <a href="/profile-info/{{ $convo->sender->username }}" wire:navigate>
                            <img @if ($convo->sender->profile_picture === null)
                            src="/images/profile.png"
                            @else
                            src="{{ Storage::url($convo->sender->profile_picture) }}"
                            @endif
                            style="width: 30px; height: 30px;" alt="Profile Image"
                            class="rounded-circle"></a>
                    </div>
                </div>
                @endif

                @else
                {{-- Display the message with attachments --}}
                @if (!empty($convo->attachment) || !empty($convo->message))
                <div class="p-2" style="font-size: 13px;">
                    <div class="d-flex">
                        {{-- Profile Image --}}
                        <a href="/profile-info/{{ $convo->sender->username }}" wire:navigate>
                            <img @if ($convo->sender->profile_picture === null)
                            src="/images/profile.png"
                            @else
                            src="{{ Storage::url($convo->sender->profile_picture) }}"
                            @endif
                            style="width: 30px; height: 30px;" alt="Profile Image"
                            class="rounded-circle">
                        </a>

                        {{-- Message and Attachments --}}
                        <div class="border p-2 ms-2 rounded-end-4 rounded-bottom-4 shadow"
                            title="@if ($userConvo->status === 'offline') {{ $convo->is_seen ? 'You seen this' : 'Sent' }} @else {{ $convo->is_seen ? 'You seen this' : 'Delivered' }} @endif, {{ $convo->created_at->format('l, g:i A') }}"
                            style="max-width: 85%;">
                            <strong>{{ $convo->sender->name }}</strong>
                            {{-- If message is unsent --}}
                            @if ($convo->status === 'unsent')
                            <p class="text-break">
                                <span class="fst-italic text-muted mt-2" style='font-size: 12px;'>
                                    Message unsent
                                </span>
                            </p>
                            @else
                            {{-- Display message if available --}}
                            @if (!empty($convo->message))
                            <p class="text-break">
                                @if ($convo->message === '(y)')
                                <i class="fa-solid fa-thumbs-up text-primary" style="font-size: 60px;"></i>
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
                                <a class="text-decoration-none" href="#" data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{$convo->id}}">
                                    <img wire:click.prevent="setActiveImage({{ $index }})"
                                        src="{{ Storage::url($attach) }}" style="max-width: 200px;"
                                        alt="Attachment Image" class="mb-2 me-2">
                                </a>
                                @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                <audio controls class="mb-2 me-2">
                                    <source src="{{ Storage::url($attach) }}" type="audio/{{ $extension }}">
                                    Your browser does not support the audio element.
                                </audio>
                                @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                <video controls width="250" class="mb-2 me-2">
                                    <source src="{{ Storage::url($attach) }}" type="video/{{ $extension }}">
                                    Your browser does not support the video element.
                                </video>
                                @else
                                <a href="{{ Storage::url($attach) }}" download class="text-white text-decoration-none">
                                    <i style="font-size: 100px;" @if (in_array($extension, ['zip']))
                                        class="far fa-file-zip me-2" @elseif(in_array($extension, ['docs', 'docx'
                                        , 'doc' ])) class="far fa-file-word me-2" @elseif(in_array($extension,
                                        ['ppt', 'pptx' ])) class="far fa-file-powerpoint me-2"
                                        @elseif(in_array($extension, ['xlsx', 'xlsm' , 'xlsb' , 'xltx' ]))
                                        class="far fa-file-excel me-2" @elseif(in_array($extension, ['pdf']))
                                        class="far fa-file-pdf me-2" @elseif(in_array($extension, ['sql', 'html' , 'js'
                                        , 'jsx' , 'ts' , 'tsx' , 'php' , 'py' ])) class="far fa-file-code me-2" @else
                                        class="fas fa-file-zipper me-2" @endif></i>
                                </a>
                                @endif
                                @endforeach
                            </p>
                            @endif
                            @endif
                            <small>@if ($convo->created_at->diffForHumans() < 1) Just now @else {{ $convo->
                                    created_at->diffForHumans() }} @endif</small>
                        </div>
                        <div class="dropstart mt-2 drop-set">
                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-smile text-white"></i>
                            </button>
                            {{-- <ul class="dropdown-menu dropdown-menu-end shadow drop-set-menu" id="dropdrop"
                                style="width: 300px; max-height: 300px; overflow: auto;"
                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                <li class="px-2">
                                    <div class="my-3">
                                        <input type="search" class="form-control" placeholder="Search emoji"
                                            id="search-emoji">
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-center align-items-center"
                                        id="emoji-list">
                                        @forelse ($emojis as $emoji)
                                        <span class="emoji-item fs-4" data-label="{{ $emoji->label }}"
                                            style="cursor: pointer;">{{ $emoji->value }}</span>
                                        @empty
                                        <div>
                                            <i class="far fa-face-thinking"></i>
                                            <span class="emoji-item fs-6" style="cursor: pointer;">No emoji
                                                displayed</span>
                                        </div>
                                        @endforelse
                                    </div>
                                    <div class="py-5" id="no-emoji-found" style="display: none; text-align: center;">
                                        <i class="far fa-face-thinking"></i>
                                        <span id="no-emoji-message" class="fs-6"></span>
                                    </div>
                                </li>
                            </ul> --}}
                        </div>
                        <div class="dropdown mt-2 drop-set">
                            <button class="btn btn-sm btn-link text-decoration-none" type="button"
                                id="dropdownMenuButton{{ $convo->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical text-white"></i>
                            </button>
                            <ul class="dropdown-menu shadow drop-set-menu"
                                aria-labelledby="dropdownMenuButton{{ $convo->id }}">
                                <li><a class="dropdown-item btn-link text-decoration-none btn" href="#">
                                        <div class="d-flex">
                                            <span class="col-2"><i class="far fa-reply"></i></span>
                                            <span class="col-10"><strong>Reply</strong></span>
                                        </div>
                                    </a>
                                </li>
                                <li><a class="dropdown-item btn-link text-decoration-none btn" href="#"
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
                <div wire:ignore.self class="modal fade" id="imageModal{{$convo->id}}" tabindex="-1"
                    aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content bg-secondary">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">
                                    <span class="text-center bg-dark text-white rounded-1 px-1"
                                        style="font-size: 12px;">
                                        Sent on: <span id="date">{{ $convo->created_at->format('F d, Y \a\t h:i A')
                                            }}</span>
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <div id="carouselExample{{$convo->id}}" class="carousel slide">
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
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#carouselExample{{$convo->id}}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#carouselExample{{$convo->id}}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!$convo->is_seen && $convo->receiver_id === auth()->user()->id && $convo->id === $lastUnseen->id)
                <div class="d-flex align-items-center justify-content-center" wire:key='{{ $convo->id }}'>
                    <hr class="custom-hr flex-grow-1" style="margin: 0 0.5rem;">
                    <span class="mx-2 text-white" style="font-size: 11px;">
                        Unread messages
                    </span>
                    <hr class="custom-hr flex-grow-1" style="margin: 0 0.5rem;">
                </div>
                @endif
                @empty
                <p class="text-center">Start a chat with <strong>{{ $userConvo->name }}</strong></p>
                @endforelse
                @if($convos->count() < $messageCount) <p class="text-center">
                    <button wire:loading.attr='disabled' wire:target="loadMoreMessage" type="button"
                        class="btn btn-link btn-sm text-white text-decoration-none" wire:click='loadMoreMessage'>
                        <span wire:loading.remove style="font-size: 12px;" wire:target="loadMoreMessage">Load
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
                        class="btn btn-dark btn-sm position-absolute top-0 start-0" style="z-index: 99;">Remove
                        all</button>
                    @foreach ($attachment as $index => $attach)
                    @if ($attach)
                    <div class="me-2 position-relative" wire:key="attachment-{{ $index }}">
                        <div class="position-absolute w-100 d-flex justify-content-end" id="image">
                            <button type="button" wire:click='removeTempUrlImg({{ $index }})'
                                class="btn btn-link text-decoration-none text-black" style="margin-right: -8px;"><i
                                    class="fas fa-circle-xmark fs-5"></i></button>
                        </div>
                        <!-- Display the attach based on its type -->
                        @if (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['jpg',
                        'jpeg', 'png', 'gif', 'ico', 'webp']))
                        <img src="{{ $attach->temporaryUrl() }}" width="100" class="img-thumbnail" alt="Attach Image">
                        @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['mp4',
                        'avi', 'mov']))
                        <video src="{{ $attach->temporaryUrl() }}" controls width="200"></video>
                        @elseif (in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION), ['mp3',
                        'wav']))
                        <audio src="{{ $attach->temporaryUrl() }}" controls></audio>
                        @else
                        <h1>
                            <a href="{{ $attach->temporaryUrl() }}" download class="text-decoration-none text-white">
                                <i style="font-size: 100px;" @if ((in_array(pathinfo($attach->getClientOriginalName(),
                                    PATHINFO_EXTENSION),
                                    ['zip'])))

                                    class="far fa-file-zip"
                                    @elseif ((in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION),
                                    ['docs', 'docx', 'doc'])))

                                    class="far fa-file-word"
                                    @elseif ((in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION),
                                    ['ppt', 'pptx'])))

                                    class="far fa-file-powerpoint"
                                    @elseif ((in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION),
                                    ['xlsx', 'xlsm', 'xlsb', 'xltx'])))

                                    class="far fa-file-excel"
                                    @elseif ((in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION),
                                    ['pdf'])))

                                    class="far fa-file-pdf"
                                    @elseif ((in_array(pathinfo($attach->getClientOriginalName(), PATHINFO_EXTENSION),
                                    ['sql', 'html', 'js' , 'jsx' , 'ts' , 'tsx' , 'php' , 'py' ])))

                                    class="far fa-file-code"
                                    @else
                                    class="far fa-file"
                                    @endif
                                    >
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
                            <progress max="100" x-bind:value="progress" class="w-100 mt-2"></progress>
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
                                                autocomplete="attachment1" autofocus wire:model='attachment' multiple>
                                            <label for="attachment1" class="form-label text-center text-white mt-3 ms-2"
                                                style="cursor: pointer;">
                                                <h4 class="me-2"><i class="far fa-paperclip fs-5"></i></h4>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input id="attachment" type="file" class="form-control d-none"
                                                name="attachment" accept="image/*,.mp4,.mov,.avi,.wmv,.mkv"
                                                autocomplete="attachment" autofocus wire:model='attachment' multiple>
                                            <label for="attachment" class="form-label text-center text-white mt-3"
                                                style="cursor: pointer;">
                                                <h4 class="me-3"><i class="far fa-image fs-5"></i></h4>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                </div>
                                <div class="flex-grow-1" style="margin-top: 10px;">
                                    <textarea class="form-control mb-2" id="textarea" @if ($toSeen> 0)
                                            wire:click='seen({{ $userConvo->id }})'
                                        @endif
                                        rows='1' name=""
                                        placeholder="Type a message" wire:loading.attr='readonly'
                                        wire:target='sendMessage' wire:model='message' rows="{{ $rowCount }}"
                                        @keydown.enter="submitForm(event)"
                                        @keydown.shift.enter="handleShiftEnter(event)"
                                        oninput="adjustTextArea(this)"></textarea>
                                </div>
                                <div class="mb-1 d-flex align-items-end">
                                    <div class="dropup mt-1 dropup">
                                        <button class="btn btn-lg pe-0" id="btn-emoji" type="button"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
                                            type="button">😄</button>
                                        <ul class="dropdown-menu" id="dropdrop"
                                            style="width: 300px; max-height: 300px; overflow: auto;">
                                            <li class="px-2">
                                                <div class="my-3">
                                                    <input type="search" class="form-control" placeholder="Search emoji"
                                                        id="search-emoji">
                                                </div>
                                                <div class="d-flex flex-wrap justify-content-center align-items-center"
                                                    id="emoji-list">
                                                    @forelse ($emojis as $emoji)
                                                    <span class="emoji-item fs-4" data-label="{{ $emoji->label }}"
                                                        style="cursor: pointer;">{{ $emoji->value }}</span>
                                                    @empty
                                                    <div>
                                                        <i class="far fa-face-thinking"></i>
                                                        <span class="emoji-item fs-6" style="cursor: pointer;">No emoji
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

        #btn-emoji {
            border: none !important;
        }
    </style>

    <script>
        document.addEventListener('livewire:navigate', function () {
            initializeChatScroll();
        });

        function initializeChatScroll() {
            const chatMessages = document.getElementById('chatMessages');
            const backToBottomBtn = document.getElementById('backToBottom');

            if (chatMessages) {
                chatMessages.addEventListener('scroll', function () {
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
        function isMediumOrLarger() {
            return window.innerWidth <= 768;
        }

        function submitForm(event) {
            if (!isMediumOrLarger() && !event.shiftKey) {
                event.preventDefault();
                @this.call('sendMessage');
            }
        }

        function handleShiftEnter(event) {
        }

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
        document.addEventListener('livewire:navigated', function () {
        @this.on('closeModal', () => {
            $('#toEditMessage').modal('hide');

            document.getElementById('toEditMessage').classList.remove('show');
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
                        function (success) {
                            $this.uploading = false
                            $this.progress = 0
                        },
                        function(error) {
                            console.log('error', error)
                        },
                        function (event) {
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
                    @this.removeUpload('attachment', filename)
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
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
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

            searchInput.addEventListener('input', function () {
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
