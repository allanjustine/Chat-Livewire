<div>
    <div class="container-fluid mt-4" wire:poll.10s>
        <strong>
            <h2 class="ms-5 text-white">Announcements</h2>
        </strong>
        <div class="d-flex">
            <button class="btn bg-dark-subtle text-dark mx-auto fw-bold" data-bs-toggle="modal"
                data-bs-target="#modalAddEdit" @if ($isEditing) wire:click='cancelEdit' @endif><i
                    class="far fa-plus"></i> Add post</button>
        </div>

        <div wire:ignore.self class="modal fade" id="modalAddEdit" tabindex="-1" aria-labelledby="modalAddEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content bg-secondary">
                    <form wire:submit.prevent="{{ $isEditing ? 'updatePost' : 'addPost' }}">
                        <div class="modal-header">
                            <h1 class="modal-title text-light fs-5" id="modalAddEditLabel">
                                {{ $isEditing ? 'Updating post...' : 'Adding post...' }}
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-light">
                            <div class="d-flex gap-2">
                                <div class="mb-2 col-6">
                                    <label for="post_title" class="form-label">Title</label>
                                    <input type="text" id="post_title" class="form-control" wire:model="post_title"
                                        placeholder="Enter a post title">
                                    @error('post_title') <span class="text-danger bg-primary-subtle rounded">{{ $message
                                        }}</span> @enderror
                                </div>

                                <div class="mb-2 col-6">
                                    <label for="post_category" class="form-label">Category</label>
                                    <select class="form-select" wire:model="post_category">
                                        <option disabled>Select category</option>
                                        <option value="post">Just a post</option>
                                        <option value="updates">Updates</option>
                                        <option value="papers">Papers</option>
                                        <option value="branch">For Branch</option>
                                        <option value="ho">For Head Office</option>
                                    </select>
                                    @error('post_category') <span class="text-danger bg-primary-subtle rounded">{{
                                        $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4 mt-2">
                                <label for="post_attachment" class="form-label">Attachment</label>
                                <input type="file" id="post_attachment" multiple class="form-control"
                                    accept=".jpg,.jpeg,.png,.gif,.ico,.webp,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.html,.css,.php,.js,.ts,.py,.java,.c,.cpp,.rb,.go,.swift,.rs,.scala,.pl,.r"
                                    wire:model="post_attachment">
                                @error('post_attachment.*') <span class="text-danger bg-primary-subtle rounded">{{
                                    $message }}</span> @enderror

                                <span class="text-light mt-2" wire:loading wire:target="post_attachment">
                                    <i class="fa-duotone fa-solid fa-spinner-third fa-spin"></i>
                                    Uploading...
                                </span>
                            </div>
                            <div class="mb-2" wire:ignore>
                                <textarea id="post-content-textarea" wire:model="post_content" class="form-control mb-3"
                                    style="display:none; max-height: 200px;"></textarea>
                                <div id="editor-container"></div>
                            </div>
                            @error('post_content')
                            <span class="text-danger bg-primary-subtle rounded">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr='disabled'
                                wire:target='post_attachment'>
                                <span wire:loading wire:target="{{ $isEditing ? 'updatePost' : 'addPost' }}">
                                    <i class="fa-duotone fa-solid fa-spinner-third fa-spin"></i>
                                    {{ $isEditing ? 'Updating...' : 'Posting...' }}
                                </span>
                                <span wire:loading.remove wire:target="{{ $isEditing ? 'updatePost' : 'addPost' }}">
                                    <i class="far fa-pen-to-square"></i>
                                    {{ $isEditing ? 'Update' : 'Post' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mt-3">
            <div class="col-md-3 col-lg-4 mb-4">
                <div class="bg-primary-subtle sticky-top p-3 rounded shadow-sm" style="z-index: 1;">
                    <h5 class="mb-3 fw-bold"><i class="fas text-secondary fa-cloud"></i> Updates</h5>
                    <ul class="list-unstyled">
                        @forelse ($updates as $update)
                        <li class="mb-2"><a href="/updates/{{ $update->post_title }}" wire:navigate
                                class="text-dark"><strong><span class="fs-6">{{ Str::limit($update->post_title, 30)
                                        }}</span></strong></a> - <span class="text-muted fst-italic"
                                style="font-size: 10px;">{{ $update->created_at->diffForHumans() }}</span></li>
                        @empty
                        <li class="text-center"><i class="far fa-cloud-xmark"></i></li>
                        <li class="text-center">No updates yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                @forelse ($announcements as $announcement)
                <div class="mb-4 bg-dark text-light p-4 rounded shadow-sm position-relative">
                    <span class="position-absolute top-0 end-0 pe-2 pt-2" style="font-size: 12px !important;">Posted on {{
                        $announcement->created_at->format('F d, Y g:i A') }}</span>
                    <div class="text-light">
                        <div class="d-flex align-items-center mb-2">
                            <a href="/profile-info/{{ $announcement->user->username }}" wire:navigate>
                                <img @if ($announcement->user->profile_picture === null)
                                src='/images/profile.png'
                                @else
                                src="{{ Storage::url($announcement->user->profile_picture) }}"
                                @endif
                                width="40" height="40"
                                class="rounded-circle me-2" alt="{{ $announcement->user->name }}">
                            </a>
                            <div>
                                <a href="/profile-info/{{ $announcement->user->username }}"
                                    class="text-decoration-none text-light" wire:navigate>
                                    <strong>{{ $announcement->user->name }}</strong>
                                </a>
                                <div class="text-light fst-italic mt-1" style="font-size: 10px;">
                                    {{ $announcement->created_at->diffForHumans() < 1 ? 'Just now' : $announcement->
                                        created_at->diffForHumans() }} |
                                        @if ($announcement->post_category === 'post')
                                        Just a post
                                        @elseif($announcement->post_category === 'updates')
                                        Updates
                                        @elseif($announcement->post_category === 'papers')
                                        Papers
                                        @elseif($announcement->post_category === 'branch')
                                        To Branches
                                        @elseif($announcement->post_category === 'ho')
                                        To Head Office
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    {{-- <div class="mt-3">
                        <h2 class="mb-3">{{ $announcement->post_title }}</h2>
                    </div> --}}
                    <div>
                        <ul class="list-unstyled">
                            @foreach ($announcement->post_attachment as $file)
                            @php
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            @endphp
                            <li class="mb-2">
                                @if (in_array($extension, [
                                'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar',
                                'txt', 'html', 'css', 'php', 'js', 'ts', 'py', 'java', 'c', 'cpp', 'rb', 'go', 'swift',
                                'rs', 'scala', 'pl', 'r'
                                ]))
                                <a href="{{ Storage::url($file) }}" download="{{ $file }}">
                                    {{ basename($file) }}
                                </a>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="text-break">
                        {!! $announcement->post_content !!}
                    </div>
                    @php
                    $images = [];
                    @endphp

                    @foreach ($announcement->post_attachment as $index => $attachment)
                    @php
                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp'])) {
                    $images[] = $attachment;
                    }
                    @endphp
                    @endforeach
                    @if (count($images) > 0)
                    <div id="carouselExampleIndicators{{ $announcement->id }}" class="carousel slide">
                        <div class="carousel-indicators">
                            @foreach ($images as $index => $image)
                            <button type="button" data-bs-target="#carouselExampleIndicators{{ $announcement->id }}"
                                data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"
                                aria-current="true" aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div wire:ignore id="image-overlay" class="image-overlay">
                            <span class="close">&times;</span>
                            <img id="overlay-image" class="overlay-image">
                        </div>
                        <div class="carousel-inner">
                            @foreach ($images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ Storage::url($image) }}" class="d-block w-100 carousel-image" alt="..."
                                    style="max-height: 400px; cursor: pointer;">
                            </div>
                            @endforeach
                        </div>
                        @if (count($announcement->post_attachment) > 1)
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carouselExampleIndicators{{ $announcement->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carouselExampleIndicators{{ $announcement->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        @endif
                    </div>
                    @endif
                    <hr>
                    <span class="float-end" style="margin-top: -15px;"><a href="#" class="text-white"
                            style="font-size: 12px !important;" data-bs-toggle="modal"
                            data-bs-target="#commentPost{{ $announcement->id }}">{{ $announcement->comments->count() <=
                                0 ? '' : $announcement->comments->count() }}
                                @if ($announcement->comments->count() <= 0) Be the first to comment
                                    @elseif($announcement->comments->count() == 1)
                                    comment
                                    @else
                                    comments
                                    @endif
                        </a></span>
                    <div class="d-flex gap-3 mt-3 justify-content-center w-100 justify-content-between">
                        @if ($announcement->likes->contains('user_id', auth()->user()->id))
                        <span class="position-absolute" style="margin-top: -13px; font-size: 12px; cursor: pointer;"
                            data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                            data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                            <div class="popover-list">
                                @foreach ($announcement->likes as $like)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item p-0 mb-2"><a class="text-decoration-none text-dark" href="/profile-info/{{ $like->user->username }}">
                                            <img @if ($like->user->profile_picture === null)
                                                src="/images/profile.png"
                                                @else
                                                src="{{ Storage::url($like->user->profile_picture) }}"
                                                @endif
                                                width="30" height="30"
                                                class="rounded-circle me-2" alt="{{ $like->user->name }}">
                                                {{ $like->user->name }}
                                            </a></li>
                                    </ul>
                                @endforeach
                            </div>
                            ' data-bs-title="People who like this post" data-bs-placement="top">
                            @if ($announcement->likes->count() == 1)
                            You liked this post
                            @elseif ($announcement->likes->count() == 2)
                            You and 1 other like this post
                            @else
                            You and {{ $announcement->likes->count() - 1 }} @if ($announcement->likes->count() - 1 == 1)
                            other
                            @else
                            others
                            @endif
                            liked this post
                            @endif
                        </span>
                        <div>
                            <button class="btn btn-link text-decoration-none mt-2"
                                wire:click='unlike({{ $announcement->id }})'>

                                <i class="fas fa-thumbs-up"></i> Like
                            </button>
                        </div>
                        @else
                        @if ($announcement->likes->count() > 0)
                        <span class="position-absolute" style="margin-top: -13px; font-size: 12px;"
                            data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                            data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                            <div class="popover-list">
                                @foreach ($announcement->likes as $like)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item p-0 mb-2"><a class="text-decoration-none text-dark" href="/profile-info/{{ $like->user->username }}">
                                            <img @if ($like->user->profile_picture === null)
                                                src="/images/profile.png"
                                                @else
                                                src="{{ Storage::url($like->user->profile_picture) }}"
                                                @endif
                                                width="30" height="30"
                                                class="rounded-circle me-2" alt="{{ $like->user->name }}">
                                                {{ $like->user->name }}
                                            </a></li>
                                    </ul>
                                @endforeach
                            </div>
                            ' data-bs-title="People who like this post" data-bs-placement="top">
                            @if ($announcement->likes->count() <= 1) {{ $announcement->likes->count() }} people like
                                this post
                                @else
                                {{ $announcement->likes->count() }} people likes this post
                                @endif
                        </span>
                        @endif
                        <button class="btn btn-link text-decoration-none text-white mt-2"
                            wire:click='like({{ $announcement->id }})'>
                            <i class="far fa-thumbs-up"></i> Like
                        </button>
                        @endif
                        <button class="btn btn-link text-decoration-none text-white mt-2" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne{{ $announcement->id }}" aria-expanded="false"
                            aria-controls="flush-collapseOne{{ $announcement->id }}">
                            <i class="far fa-comment-dots"></i> Comment
                        </button>
                        <div class="dropstart">
                            <button class="btn btn-link text-decoration-none text-white mt-2" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-gears"></i> More
                            </button>
                            <ul class="dropdown-menu">
                                @if ($announcement->user_id === auth()->user()->id)
                                <li data-bs-toggle="modal" data-bs-target="#modalAddEdit"
                                    wire:click='edit({{ $announcement->id }})'><button class="dropdown-item"><i
                                            class="far fa-pen"></i> <strong>Edit</strong></button></li>
                                <li wire:click='delete({{ $announcement->id }})'><button class="dropdown-item"><i
                                            class="far fa-trash"></i> <strong>Delete</strong></button></li>
                                <li
                                    onclick="copyLink('{{ url('http://136.239.196.178:5004/updates/' . $announcement->post_title) }}'); return false;">
                                    <a class="dropdown-item" href="#"><i class="fas fa-link"></i>
                                        <strong>Copy Link</strong></a>
                                </li>
                                @else
                                @role('admin')
                                <li wire:click='delete({{ $announcement->id }})'><button class="dropdown-item"><i
                                            class="far fa-trash"></i> <strong>Delete</strong></button></li>
                                @endrole
                                <li
                                    onclick="copyLink('{{ url('http://136.239.196.178:5004/updates/' . $announcement->post_title) }}'); return false;">
                                    <a class="dropdown-item" href="#"><i class="fas fa-link"></i>
                                        <strong>Copy Link</strong></a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item bg-dark rounded">
                            <div wire:ignore.self id="flush-collapseOne{{ $announcement->id }}"
                                class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="d-flex">
                                        <textarea id="commentTextArea" class="form-control flex-grow-1 me-2" rows="2"
                                            placeholder='Write a comment to "{{ $announcement->user->name }}" post...'
                                            wire:model='comment_content'></textarea>
                                        <button type="submit" class="btn btn-primary btn-sm"
                                            wire:click='postComment({{ $announcement->id }})'>
                                            <div class="d-flex gap-2 align-items-center"><i
                                                    class="far fa-comment-arrow-up"></i><span>Comment</span>
                                            </div>
                                        </button>
                                    </div>
                                    @error('comment_content')
                                    <span class="text-danger bg-primary-subtle rounded">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3 rounded" style="background-color: #6060603c;">
                        @if ($lastComment = $announcement->comments->last())
                        <div class="mb-2">
                            <div style="position: absolute;" class="mt-1 ms-2">
                                <a href="/profile-info/{{ $lastComment->user->username }}" wire:navigate>
                                    <img @if ($lastComment->user->profile_picture === null)
                                    src="/images/profile.png"
                                    @else
                                    src="{{ Storage::url($lastComment->user->profile_picture) }}"
                                    @endif
                                    alt="Profile Image"
                                    class="img-fluid rounded-circle border shadow mt-2"
                                    style="width: 45px; height: 45px;"></a>
                            </div>

                            <div class="ms-5">
                                <footer class="px-4 py-2 text-white mb-2 mt-3 footer-comment">
                                    <a class="text-light" href="/profile-info/{{ $lastComment->user->username }}"
                                        wire:navigate>
                                        <strong>{{ $lastComment->user->name }}</strong>
                                    </a>
                                    <br>
                                    <span class="text-wrap">
                                        {{ $lastComment->comment_content }}
                                    </span>

                                </footer>
                                <div class="d-flex align-items-center gap-3 text-light"
                                    style="margin-top: -8px; margin-left: 20px;">
                                    <a style="font-size: 11px;">Like</a>
                                    @if ($lastComment->user_id === auth()->user()->id)
                                    <a href="#" wire:click='deleteComment({{ $lastComment->id }})'
                                        class="text-light text-decoration-none" style="font-size: 11px;">Delete</a>
                                    @endif
                                    <span style="font-size: 11px;">
                                        @if ($lastComment->created_at->diffForHumans() < 1) Just now @else {{
                                            $lastComment->created_at->diffForHumans() }}
                                            @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div wire:ignore.self class="modal fade" id="commentPost{{ $announcement->id }}" tabindex="-1"
                    aria-labelledby="commentPost{{ $announcement->id }}Label" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content bg-secondary">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5 text-light" id="commentPost{{ $announcement->id }}Label">{{
                                    $announcement->user->name }}&apos;s post</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body m-0 p-0">
                                <div class="text-light p-4 rounded shadow-sm position-relative">
                                    <span class="position-absolute top-0 end-0 pe-2 pt-2"
                                        style="font-size: 12px !important;">Posted on {{
                                        $announcement->created_at->format('F d, Y g:i A') }}</span>
                                    <div class="text-light">
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="/profile-info/{{ $announcement->user->username }}" wire:navigate>
                                                <img @if ($announcement->user->profile_picture === null)
                                                src='/images/profile.png'
                                                @else
                                                src="{{ Storage::url($announcement->user->profile_picture) }}"
                                                @endif
                                                width="40" height="40"
                                                class="rounded-circle me-2" alt="{{ $announcement->user->name }}">
                                            </a>
                                            <div>
                                                <a href="/profile-info/{{ $announcement->user->username }}"
                                                    class="text-decoration-none text-light" wire:navigate>
                                                    <strong>{{ $announcement->user->name }}</strong>
                                                </a>
                                                <div class="text-light fst-italic mt-1" style="font-size: 10px;">
                                                    {{ $announcement->created_at->diffForHumans() < 1 ? 'Just now' :
                                                        $announcement->
                                                        created_at->diffForHumans() }} |
                                                        @if ($announcement->post_category === 'post')
                                                        Just a post
                                                        @elseif($announcement->post_category === 'updates')
                                                        Updates
                                                        @elseif($announcement->post_category === 'papers')
                                                        Papers
                                                        @elseif($announcement->post_category === 'branch')
                                                        To Branches
                                                        @elseif($announcement->post_category === 'ho')
                                                        To Head Office
                                                        @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    {{-- <div class="mt-3">
                                        <h2 class="mb-3">{{ $announcement->post_title }}</h2>
                                    </div> --}}
                                    <div>
                                        <ul class="list-unstyled">
                                            @foreach ($announcement->post_attachment as $file)
                                            @php
                                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                                            @endphp
                                            <li class="mb-2">
                                                @if (in_array($extension, [
                                                'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar',
                                                'txt', 'html', 'css', 'php', 'js', 'ts', 'py', 'java', 'c', 'cpp', 'rb',
                                                'go', 'swift',
                                                'rs', 'scala', 'pl', 'r'
                                                ]))
                                                <a href="{{ Storage::url($file) }}" download="{{ $file }}">
                                                    {{ basename($file) }}
                                                </a>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="text-break">
                                        {!! $announcement->post_content !!}
                                    </div>
                                    @php
                                    $images = [];
                                    @endphp
                                    @foreach ($announcement->post_attachment as $index => $attachment)
                                    @php
                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp'])) {
                                    $images[] = $attachment;
                                    }
                                    @endphp
                                    @endforeach
                                    @if (count($images) > 0)
                                    <div id="carouselExampleIndicators{{ $announcement->id }}" class="carousel slide">
                                        <div class="carousel-indicators">
                                            @foreach ($images as $index => $image)
                                            <button type="button"
                                                data-bs-target="#carouselExampleIndicators{{ $announcement->id }}"
                                                data-bs-slide-to="{{ $index }}"
                                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="true"
                                                aria-label="Slide {{ $index + 1 }}"></button>
                                            @endforeach
                                        </div>
                                        <div class="carousel-inner">
                                            @foreach ($images as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ Storage::url($image) }}" class="d-block w-100" alt="..."
                                                    style="max-height: 600px;">
                                            </div>
                                            @endforeach
                                        </div>
                                        @if (count($announcement->post_attachment) > 1)
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carouselExampleIndicators{{ $announcement->id }}"
                                            data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carouselExampleIndicators{{ $announcement->id }}"
                                            data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                        @endif
                                    </div>
                                    @endif
                                    <hr>
                                    <span class="float-end" style="margin-top: -15px;"><a href="#" class="text-white"
                                            style="font-size: 12px !important;">{{ $announcement->comments->count() <= 0 ? '' :
                                                $announcement->comments->count() }}
                                                @if ($announcement->comments->count() <= 0) Be the first to comment
                                                    @elseif ($announcement->comments->count() == 1)
                                                    comment
                                                    @else
                                                    comments
                                                    @endif
                                        </a></span>
                                    <div class="d-flex gap-3 w-100 mt-3 justify-content-center justify-content-between">
                                        @if ($announcement->likes->contains('user_id', auth()->user()->id))
                                        <span class="position-absolute" style="margin-top: -13px; font-size: 12px;"
                                            data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                                            data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                                                <div class="popover-list">
                                                    @foreach ($announcement->likes as $like)
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item p-0 mb-2"><a class="text-decoration-none text-dark" href="/profile-info/{{ $like->user->username }}">
                                                                <img @if ($like->user->profile_picture === null)
                                                                    src="/images/profile.png"
                                                                    @else
                                                                    src="{{ Storage::url($like->user->profile_picture) }}"
                                                                    @endif
                                                                    width="30" height="30"
                                                                    class="rounded-circle me-2" alt="{{ $like->user->name }}">
                                                                    {{ $like->user->name }}
                                                                </a></li>
                                                        </ul>
                                                    @endforeach
                                                </div>
                                                ' data-bs-title="People who like this post" data-bs-placement="top">
                                            @if ($announcement->likes->count() == 1)
                                            You liked this post
                                            @elseif ($announcement->likes->count() == 2)
                                            You and 1 other like this post
                                            @else
                                            You and {{ $announcement->likes->count() - 1 }}
                                            @if ($announcement->likes->count() - 1
                                            == 1)
                                            other
                                            @else
                                            others
                                            @endif
                                            liked this post
                                            @endif
                                        </span>
                                        <div class="mt-2">
                                            <button class="btn btn-link text-decoration-none"
                                                wire:click='unlike({{ $announcement->id }})'>

                                                <i class="fas fa-thumbs-up"></i> Like
                                            </button>
                                        </div>
                                        @else
                                        @if ($announcement->likes->count() > 0)
                                        <span class="position-absolute" style="margin-top: -13px; font-size: 12px;"
                                            data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                                            data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                                                <div class="popover-list">
                                                    @foreach ($announcement->likes as $like)
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item p-0 mb-2"><a class="text-decoration-none text-dark" href="/profile-info/{{ $like->user->username }}">
                                                                <img @if ($like->user->profile_picture === null)
                                                                    src="/images/profile.png"
                                                                    @else
                                                                    src="{{ Storage::url($like->user->profile_picture) }}"
                                                                    @endif
                                                                    width="30" height="30"
                                                                    class="rounded-circle me-2" alt="{{ $like->user->name }}">
                                                                    {{ $like->user->name }}
                                                                </a></li>
                                                        </ul>
                                                    @endforeach
                                                </div>
                                                ' data-bs-title="People who like this post" data-bs-placement="top">
                                            @if ($announcement->likes->count() <= 1) {{ $announcement->likes->count() }}
                                                people
                                                like this post
                                                @else
                                                {{ $announcement->likes->count() }} people likes this post
                                                @endif
                                        </span>
                                        @endif
                                        <button class="btn btn-link text-decoration-none mt-2 text-white"
                                            wire:click='like({{ $announcement->id }})'>
                                            <i class="far fa-thumbs-up"></i> Like
                                        </button>
                                        @endif
                                        <button class="btn btn-link text-decoration-none mt-2 text-white" type="button">
                                            <i class="far fa-comment-dots"></i>
                                            Comment
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex mx-3 mt-2">
                                    <textarea id="commentTextArea" class="form-control flex-grow-1 me-2" rows="2"
                                        placeholder='Write a comment to "{{ $announcement->user->name }}" post...'
                                        wire:model='comment_content'></textarea>
                                    <button type="button" wire:click='postComment({{ $announcement->id }})'
                                        class="btn btn-primary btn-sm">
                                        <div class="d-flex gap-2 align-items-center"><i
                                                class="far fa-comment-arrow-up"></i><span>Comment</span>
                                        </div>
                                    </button>
                                </div>
                                @error('comment_content')
                                <span class="mx-3 mt-2">
                                    <span class="text-danger bg-primary-subtle rounded">{{ $message }}</span>
                                </span>
                                @enderror
                                <div class="px-4 mt-3 rounded mx-2">
                                    @foreach ($announcement->comments->sortByDesc('created_at') as $comment)
                                    <div class="mb-1 rounded" style="background-color: #2a2a2a58;">
                                        <div style="position: absolute;" class="mt-1 ms-2">
                                            <a href="/profile-info/{{ $comment->user->username }}" wire:navigate>
                                                <img @if ($comment->user->profile_picture === null)
                                                src="/images/profile.png"
                                                @else
                                                src="{{ Storage::url($comment->user->profile_picture) }}"
                                                @endif
                                                alt="Profile Image"
                                                class="img-fluid rounded-circle border shadow mt-2"
                                                style="width: 45px; height: 45px;"></a>
                                        </div>

                                        <div class="ms-5">
                                            <footer class="px-4 py-2 text-white mb-2 mt-3 footer-comment">
                                                <a class="text-light"
                                                    href="/profile-info/{{ $comment->user->username }}" wire:navigate>
                                                    <strong>{{ $comment->user->name }}</strong>
                                                </a>
                                                <br>
                                                <span class="text-wrap">
                                                    {{ $comment->comment_content }}
                                                </span>

                                            </footer>
                                            <div class="d-flex align-items-center gap-3 text-light"
                                                style="margin-top: -8px; margin-left: 20px;">
                                                <a style="font-size: 11px;">Like</a>
                                                @if ($comment->user_id === auth()->user()->id)
                                                <a href="#" wire:click='deleteComment({{ $comment->id }})'
                                                    class="text-light text-decoration-none"
                                                    style="font-size: 11px;">Delete</a>
                                                @endif
                                                <span style="font-size: 11px;">
                                                    @if ($comment->created_at->diffForHumans() < 1) Just now @else {{
                                                        $comment->created_at->diffForHumans() }}
                                                        @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center mt-5">
                    <p class="text-light"><i class="far fa-mailbox-flag-up fs-1"></i></p>
                    <h1 class="text-light">No posts yet.</h1>
                </div>
                @endforelse
                @if ($allPost > $load)
                <div class="d-flex justify-content-center mb-2">
                    <button type="button" wire:click='loadMore' wire:loading.attr='disabled'
                        class="btn btn-link text-decoration-none text-white">
                        <span wire:loading.remove wire:target='loadMore'>Load more</span>
                        <span class="spinner-border" wire:loading wire:target='loadMore'></span>
                    </button>
                </div>
                @endif
            </div>

            <!-- Right Sidebar: Post Trends -->
            <div class="col-md-3 col-lg-4 mb-4">
                <div class="bg-info-subtle sticky-top p-3 rounded shadow-sm" style="z-index: 1;">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-fire text-danger"></i> Post Trends</h5>
                    <ul class="list-unstyled">
                        @forelse ($post_trends as $trend)
                        <li class="mb-2"><a href="/updates/{{ $trend->post_title }}" wire:navigate
                                class="text-dark"><strong><span class="fs-6">{{ Str::limit($trend->post_title,
                                        30)
                                        }}</span></strong></a> - <span class="text-muted fst-italic">{{
                                $trend->likes_count }} {{ $trend->likes_count > 1 ?
                                'likes' : 'like' }}</span></li>
                        @empty
                        <li class="text-center"><i class="far fa-fire"></i></li>
                        <li class="text-center">No post trends yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        select {
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
            transition: opacity 0.3s ease;
        }

        input {
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
            transition: opacity 0.3s ease;
        }

        input::placeholder {
            color: white !important;
            transition: opacity 0.3s ease;
        }

        .ql-editor {
            max-height: 200px;
            color: white;
        }

        .ql-picker-label {
            color: white;
        }

        .ql-align-center {
            text-align: center !important;
        }

        .ql-align-right {
            text-align: right !important;
        }

        .ql-align-justify {
            text-align: justify !important;
        }

        #editor-container {
            max-height: 400px;
            width: 100%;
        }

        .ql-size-huge {
            font-size: 30px;
        }

        .ql-size-large {
            font-size: 20px;
        }

        .ql-size-small {
            font-size: 13px;
        }

        .popover-list {
            max-height: 200px;
            max-width: 250px;
            overflow-y: auto;
        }

        #commentTextArea {
            resize: none;
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
        }

        #commentTextArea::placeholder {
            color: white !important;
            opacity: 1;
        }

        #commentTextArea:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .ql-editor {
            min-height: 100px;
        }

        .image-overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .overlay-image {
            max-width: 60%;
            max-height: 70%;
            transition: transform 0.25s ease;
        }

        .image-overlay.zoomed .overlay-image {
            transform: scale(1.3);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #bbb;
        }
    </style>

    <script>
        document.addEventListener('livewire:navigated', function () {
            initializeQuill();

            Livewire.hook('message.processed', (message, component) => {
                if (document.getElementById('editor-container') && !document.querySelector('.ql-editor')) {
                    initializeQuill();
                }
            });
        });
        function initializeQuill() {
            if (!document.querySelector('.ql-editor')) {

                const quill = new Quill('#editor-container', {
                    theme: 'snow',
                    modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                ['blockquote', 'code-block'],

                                [{ 'header': 1 }, { 'header': 2 }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'script': 'sub'}, { 'script': 'super' }],
                                [{ 'indent': '-1'}, { 'indent': '+1' }],
                                [{ 'direction': 'rtl' }],

                                [{ 'size': ['small', false, 'large', 'huge'] }],
                                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'font': [] }],
                                [{ 'align': [] }],

                                ['clean']
                            ]
                        },
                    placeholder: 'Compose a post...',
                });

                const textarea = document.getElementById('post-content-textarea');

                @this.on('formSubmitted', () => {
                    quill.setContents([]);
                });

                quill.on('text-change', function (delta, oldDelta, source) {
                    textarea.value = quill.root.innerHTML;
                    textarea.dispatchEvent(new Event('input'));
                    $('#editor-container img').remove();
                });

                quill.root.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                quill.root.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                @this.on('setPostContent', content => {
                    quill.root.innerHTML = content;
                });
            }
        }

    </script>

    <script>
        document.addEventListener('livewire:navigated', function () {
            @this.on('close-modal', () => {
                $('#modalAddEdit').modal('hide');

                document.getElementById('modalAddEdit').classList.remove('show');
            });
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
        function copyLink(link) {
        const tempInput = document.createElement('input');
        tempInput.value = link;
        document.body.appendChild(tempInput);

        tempInput.select();
        tempInput.setSelectionRange(0, 99999);

        document.execCommand('copy');

        document.body.removeChild(tempInput);

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
        };

        toastr.success('Link copied to clipboard!', 'Success');
    }
    </script>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const overlay = document.getElementById('image-overlay');
            const overlayImage = document.getElementById('overlay-image');
            const closeButton = document.querySelector('.close');
            const carouselImages = document.querySelectorAll('.carousel-image');

            carouselImages.forEach(image => {
                image.addEventListener('click', () => {
                    overlayImage.src = image.src;
                    overlay.style.display = 'flex';
                    setTimeout(() => overlay.classList.add('zoomed'), 10);
                });
            });

            closeButton.addEventListener('click', () => {
                overlay.classList.remove('zoomed');
                setTimeout(() => overlay.style.display = 'none', 250);
            });

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    overlay.classList.remove('zoomed');
                    setTimeout(() => overlay.style.display = 'none', 250);
                }
            });
        });

    </script>

</div>
