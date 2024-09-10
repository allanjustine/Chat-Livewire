<div>
    <div class="container d-flex justify-content-center" wire:poll.10s>
        <div class="col-md-6 col-12 mt-5">
            <div class="bg-dark text-light p-4 rounded shadow-sm position-relative">
                <span class="position-absolute top-0 end-0 pe-2 pt-2" style="font-size: 12px;">Posted on {{
                    $updatesData->created_at->format('F d, Y g:i A') }}</span>
                <div class="text-light">
                    <div class="d-flex align-items-center mb-2">
                        <a href="/profile-info/{{ $updatesData->user->username }}" wire:navigate>
                            <img @if ($updatesData->user->profile_picture === null)
                            src='/images/profile.png'
                            @else
                            src="{{ Storage::url($updatesData->user->profile_picture) }}"
                            @endif
                            width="40" height="40"
                            class="rounded-circle me-2" alt="{{ $updatesData->user->name }}">
                        </a>
                        <div>
                            <a href="/profile-info/{{ $updatesData->user->username }}"
                                class="text-decoration-none text-light" wire:navigate>
                                <strong>{{ $updatesData->user->name }}</strong>
                            </a>
                            <div class="text-light fst-italic mt-1" style="font-size: 10px;">
                                {{ $updatesData->created_at->diffForHumans() < 1 ? 'Just now' : $updatesData->
                                    created_at->diffForHumans() }} |
                                    @if ($updatesData->post_category === 'post')
                                    Just a post
                                    @elseif($updatesData->post_category === 'updates')
                                    Updates
                                    @elseif($updatesData->post_category === 'papers')
                                    Papers
                                    @elseif($updatesData->post_category === 'branch')
                                    To Branches
                                    @elseif($updatesData->post_category === 'ho')
                                    To Head Office
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                {{-- <div class="mt-3">
                    <h2 class="mb-3">{{ $updatesData->post_title }}</h2>
                </div> --}}
                <div>
                    <ul class="list-unstyled">
                        @foreach ($updatesData->post_attachment as $file)
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
                    {!! $updatesData->post_content !!}
                </div>
                @php
                $images = [];
                @endphp

                @foreach ($updatesData->post_attachment as $index => $attachment)
                @php
                $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp'])) {
                $images[] = $attachment;
                }
                @endphp
                @endforeach
                @if (count($images) > 0)
                <div id="carouselExampleIndicators{{ $updatesData->id }}" class="carousel slide">
                    <div class="carousel-indicators">
                        @foreach ($images as $index => $image)
                        <button type="button" data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}"
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
                            <img src="{{ Storage::url($image) }}" class="d-block w-100 carousel-image" alt="..." style="max-height: 400px; cursor: pointer;">
                        </div>
                        @endforeach
                    </div>
                    @if (count($updatesData->post_attachment) > 1)
                    <button class="carousel-control-prev" type="button"
                        data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                        data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
                @endif
                <hr>
                <span class="float-end" style="margin-top: -15px;">
                    <a href="#" class="text-white" style="font-size: 13px;" data-bs-toggle="modal"
                        data-bs-target="#commentPost{{ $updatesData->id }}">
                        {{ $updatesData->comments->count() <= 0 ? '' : $updatesData->comments->count() }}
                            @if ($updatesData->comments->count() <= 0) Be the first to comment @elseif($updatesData->
                                comments->count() == 1)
                                comment
                                @else
                                comments
                                @endif
                    </a></span>
                <div class="d-flex gap-3 mt-3 w-100 justify-content-center justify-content-between">
                    @if ($updatesData->likes->contains('user_id', auth()->user()->id))
                    <span class="position-absolute" style="margin-top: -13px; font-size: 12px;" data-bs-toggle="popover"
                        data-bs-trigger="focus" role="button" tabindex="0" data-bs-trigger="focus" data-bs-html="true"
                        data-bs-content='
                            <div class="popover-list">
                                @foreach ($updatesData->likes as $like)
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
                        @if ($updatesData->likes->count() == 1)
                        You liked this post
                        @elseif ($updatesData->likes->count() == 2)
                        You and 1 other like this post
                        @else
                        You and {{ $updatesData->likes->count() - 1 }} @if ($updatesData->likes->count() - 1 == 1)
                        other
                        @else
                        others
                        @endif
                        liked this post
                        @endif
                    </span>
                    <div class="mt-2">
                        <button class="btn btn-link text-decoration-none" wire:click='unlike({{ $updatesData->id }})'>

                            <i class="fas fa-thumbs-up"></i> Like
                        </button>
                    </div>
                    @else
                    @if ($updatesData->likes->count() > 0)
                    <span class="position-absolute" style="margin-top: -13px; font-size: 12px;" data-bs-toggle="popover"
                        data-bs-trigger="focus" role="button" tabindex="0" data-bs-trigger="focus" data-bs-html="true"
                        data-bs-content='
                            <div class="popover-list">
                                @foreach ($updatesData->likes as $like)
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
                        @if ($updatesData->likes->count() <= 1) {{ $updatesData->likes->count() }} people like this post
                            @else
                            {{ $updatesData->likes->count() }} people likes this post
                            @endif
                    </span>
                    @endif
                    <button class="btn btn-link text-decoration-none mt-2 text-white"
                        wire:click='like({{ $updatesData->id }})'>
                        <i class="far fa-thumbs-up"></i> Like
                    </button>
                    @endif
                    <button class="btn btn-link text-decoration-none mt-2 text-white" type="button"
                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false"
                        aria-controls="flush-collapseOne">
                        <i class="far fa-comment-dots"></i> Comment
                    </button>
                    <div class="dropstart">
                        <button class="btn btn-link text-decoration-none mt-2 text-white" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-gears"></i> More
                        </button>
                        <ul class="dropdown-menu">
                            @if ($updatesData->user_id === auth()->user()->id)
                            <li wire:click='delete({{ $updatesData->id }})'><button class="dropdown-item"><i
                                        class="far fa-trash"></i> <strong>Delete</strong></button></li>
                            <li
                                onclick="copyLink('{{ url('http://136.239.196.178:5004/updates/' . $updatesData->post_title) }}'); return false;">
                                <a class="dropdown-item" href="#"><i class="far fa-copy"></i>
                                    <strong>Copy Link</strong></a>
                            </li>
                            @else
                            <li
                                onclick="copyLink('{{ url('http://136.239.196.178:5004/updates/' . $updatesData->post_title) }}'); return false;">
                                <a class="dropdown-item" href="#"><i class="far fa-copy"></i>
                                    <strong>Copy Link</strong></a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item bg-dark rounded">
                        <div wire:ignore.self id="flush-collapseOne" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <div class="d-flex">
                                    <textarea class="form-control flex-grow-1 me-2" rows="2"
                                        placeholder='Write a comment to "{{ $updatesData->user->name }}" post...'
                                        wire:model='comment_content'></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm"
                                        wire:click='postComment({{ $updatesData->id }})'>
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
                <div class="mt-3 rounded" style="background-color: #6060603c;">
                    @foreach ($updatesData->comments->sortByDesc('created_at') as $comment)
                    <div class="mb-2">
                        <div style="position: absolute;" class="mt-1 ms-2">
                            <a href="/profile-info/{{ $comment->user->username }}">
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
                                <a class="text-light" href="/profile-info/{{ $comment->user->username }}">
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
                                    class="text-light text-decoration-none" style="font-size: 11px;">Delete</a>
                                @endif
                                <span style="font-size: 11px;">
                                    @if ($comment->created_at->diffForHumans() < 1)
                                        Just now
                                    @else
                                        {{ $comment->created_at->diffForHumans() }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="commentPost{{ $updatesData->id }}" tabindex="-1"
        aria-labelledby="commentPost{{ $updatesData->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content bg-secondary">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-light" id="commentPost{{ $updatesData->id }}Label">{{
                        $updatesData->user->name }}&apos;s post</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="text-light p-4 rounded shadow-sm position-relative">
                        <span class="position-absolute top-0 end-0 pe-2 pt-2" style="font-size: 12px;">Posted on {{
                            $updatesData->created_at->format('F d, Y g:i A') }}</span>
                        <div class="text-light">
                            <div class="d-flex align-items-center mb-2">
                                <a href="/profile-info/{{ $updatesData->user->username }}" wire:navigate>
                                    <img @if ($updatesData->user->profile_picture === null)
                                    src='/images/profile.png'
                                    @else
                                    src="{{ Storage::url($updatesData->user->profile_picture) }}"
                                    @endif
                                    width="40" height="40"
                                    class="rounded-circle me-2" alt="{{ $updatesData->user->name }}">
                                </a>
                                <div>
                                    <a href="/profile-info/{{ $updatesData->user->username }}"
                                        class="text-decoration-none text-light" wire:navigate>
                                        <strong>{{ $updatesData->user->name }}</strong>
                                    </a>
                                    <div class="text-light fst-italic mt-1" style="font-size: 10px;">
                                        {{ $updatesData->created_at->diffForHumans() < 1 ? 'Just now' : $updatesData->
                                            created_at->diffForHumans() }} |
                                            @if ($updatesData->post_category === 'post')
                                            Just a post
                                            @elseif($updatesData->post_category === 'updates')
                                            Updates
                                            @elseif($updatesData->post_category === 'papers')
                                            Papers
                                            @elseif($updatesData->post_category === 'branch')
                                            To Branches
                                            @elseif($updatesData->post_category === 'ho')
                                            To Head Office
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- <div class="mt-3">
                            <h2 class="mb-3">{{ $updatesData->post_title }}</h2>
                        </div> --}}
                        <div>
                            <ul class="list-unstyled">
                                @foreach ($updatesData->post_attachment as $file)
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
                            {!! $updatesData->post_content !!}
                        </div>
                        @php
                        $images = [];
                        @endphp
                        @foreach ($updatesData->post_attachment as $index => $attachment)
                        @php
                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'ico', 'webp'])) {
                        $images[] = $attachment;
                        }
                        @endphp
                        @endforeach
                        @if (count($images) > 0)
                        <div id="carouselExampleIndicators{{ $updatesData->id }}" class="carousel slide">
                            <div class="carousel-indicators">
                                @foreach ($images as $index => $image)
                                <button type="button"
                                    data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}"
                                    data-bs-slide-to="{{ $index }}"
                                    class="{{ $index === 0 ? 'active' : '' }}" aria-current="true"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach ($images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ Storage::url($image) }}" class="d-block w-100" alt="..." style="max-height: 600px;">
                                </div>
                                @endforeach
                            </div>
                            @if (count($updatesData->post_attachment) > 1)
                            <button class="carousel-control-prev" type="button"
                                data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                data-bs-target="#carouselExampleIndicators{{ $updatesData->id }}"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            @endif
                        </div>
                        @endif
                        <hr>
                        <span class="float-end" style="margin-top: -15px;">
                            <a href="#" class="text-white" style="font-size: 13px;">
                                {{ $updatesData->comments->count() <= 0 ? '' : $updatesData->comments->count() }}
                                    @if ($updatesData->comments->count() <= 0) Be the first to comment
                                        @elseif($updatesData->comments->count() == 1)
                                        comment
                                        @else
                                        comments
                                        @endif
                            </a></span>
                        <div class="d-flex gap-3 w-100 mt-3 justify-content-center justify-content-between">
                            @if ($updatesData->likes->contains('user_id', auth()->user()->id))
                            <span class="position-absolute" style="margin-top: -13px; font-size: 12px;"
                                data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                                data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                                    <div class="popover-list">
                                        @foreach ($updatesData->likes as $like)
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
                                @if ($updatesData->likes->count() == 1)
                                You liked this post
                                @elseif ($updatesData->likes->count() == 2)
                                You and 1 other like this post
                                @else
                                You and {{ $updatesData->likes->count() - 1 }} @if ($updatesData->likes->count() - 1
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
                                    wire:click='unlike({{ $updatesData->id }})'>

                                    <i class="fas fa-thumbs-up"></i> Like
                                </button>
                            </div>
                            @else
                            @if ($updatesData->likes->count() > 0)
                            <span class="position-absolute" style="margin-top: -13px; font-size: 12px;"
                                data-bs-toggle="popover" data-bs-trigger="focus" role="button" tabindex="0"
                                data-bs-trigger="focus" data-bs-html="true" data-bs-content='
                                    <div class="popover-list">
                                        @foreach ($updatesData->likes as $like)
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
                                @if ($updatesData->likes->count() <= 1) {{ $updatesData->likes->count() }} people
                                    like this post
                                    @else
                                    {{ $updatesData->likes->count() }} people likes this post
                                    @endif
                            </span>
                            @endif
                            <button class="btn btn-link text-decoration-none mt-2 text-white"
                                wire:click='like({{ $updatesData->id }})'>
                                <i class="far fa-thumbs-up"></i> Like
                            </button>
                            @endif
                            <button class="btn btn-link text-decoration-none mt-2 text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                                <i class="far fa-comment-dots"></i> Comment
                            </button>
                        </div>
                    </div>
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item bg-secondary rounded">
                            <div wire:ignore.self id="flush-collapseOne" class="accordion-collapse collapse"
                                data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="d-flex">
                                        <textarea class="form-control flex-grow-1 me-2" rows="2"
                                            placeholder='Write a comment to "{{ $updatesData->user->name }}" post...'
                                            wire:model='comment_content'></textarea>
                                        <button type="button" wire:click='postComment({{ $updatesData->id }})'
                                            class="btn btn-primary btn-sm">
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
                    <div class="px-4 mt-3 mx-2 rounded" style="background-color: #2a2a2a58;">
                        @foreach ($updatesData->comments->sortByDesc('created_at') as $comment)
                        <div class="mb-2">
                            <div style="position: absolute;" class="mt-1">
                                <a href="/profile-info/{{ $comment->user->username }}">
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
                                    <a class="text-light" href="/profile-info/{{ $comment->user->username }}">
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
                                        class="text-light text-decoration-none" style="font-size: 11px;">Delete</a>
                                    @endif
                                    <span style="font-size: 11px;">
                                        @if ($comment->created_at->diffForHumans() < 1)
                                            Just now
                                        @else
                                            {{ $comment->created_at->diffForHumans() }}
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

    <style>
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

        textarea {
            resize: none;
            background-color: #999999 !important;
            color: #f5f5f5 !important;
            border: none !important;
        }

        textarea::placeholder {
            color: white !important;
            opacity: 1;
        }

        textarea:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
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
