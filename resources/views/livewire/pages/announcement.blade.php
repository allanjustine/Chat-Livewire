<div>
    <div class="container-fluid mt-4">
        <strong>
            <h2 class="ms-5 text-white">Announcements</h2>
        </strong>
        <div class="d-flex">
            <button class="btn bg-dark-subtle text-dark mx-auto fw-bold" data-bs-toggle="modal"
                data-bs-target="#modalAddEdit" @if ($isEditing) wire:click='cancelEdit' @endif>Add post</button>
        </div>

        <div wire:ignore.self class="modal fade" id="modalAddEdit" tabindex="-1" aria-labelledby="modalAddEditLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
                                    @error('post_title') <span class="text-danger">{{ $message }}</span> @enderror
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
                                    @error('post_category') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-4 mt-2">
                                <label for="post_attachment" class="form-label">Attachment</label>
                                <input type="file" id="post_attachment" multiple class="form-control"
                                    wire:model="post_attachment"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt">

                                @error('post_attachment') <span class="text-danger">{{ $message }}</span> @enderror

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
                            <span class="text-danger">{{ $message }}</span>
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
            <div class="col-md-3 mb-4">
                <div class="bg-primary-subtle sticky-top p-3 rounded shadow-sm">
                    <h5 class="mb-3 fw-bold"><i class="fas text-secondary fa-cloud"></i> Updates</h5>
                    <ul class="list-unstyled">
                        @forelse ($updates as $update)
                        <li class="mb-2"><a href="/updates/{{ $update->post_title }}" wire:navigate
                                class="text-dark"><strong><span class="fs-6">{{ $update->post_title
                                        }}</span></strong></a> - <span class="text-muted fst-italic"
                                style="font-size: 10px;">{{ $update->created_at->diffForHumans() }}</span></li>
                        @empty
                        <li class="text-center"><i class="far fa-cloud-xmark"></i></li>
                        <li class="text-center">No updates yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                @forelse ($announcements as $announcement)
                <div class="mb-4 bg-dark text-light p-4 rounded shadow-sm position-relative">
                    <span class="position-absolute top-0 end-0 pe-2 pt-2" style="font-size: 12px;">Posted on {{
                        $announcement->created_at->format('F d, Y g:i A') }}</span>
                    <div class="text-light">
                        <div class="d-flex align-items-center mb-2">
                            <img @if ($announcement->user->profile_picture === null)
                            src='/images/profile.png'
                            @else
                            src="{{ Storage::url($announcement->user->profile_picture) }}"
                            @endif
                            width="40" height="40"
                            class="rounded-circle me-2" alt="{{ $announcement->user->name }}">
                            <div>
                                <strong>{{ $announcement->user->name }}</strong>
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
                    <div class="mt-3">
                        <h2 class="mb-3">{{ $announcement->post_title }}</h2>
                    </div>
                    <div>
                        <ul class="list-unstyled">
                            @foreach ($announcement->post_attachment as $file)
                            <li class="mb-2">
                                <a href="{{ Storage::url($file) }}" download="{{ $file }}">
                                    {{ basename($file) }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="text-break">
                        {!! $announcement->post_content !!}
                    </div>
                    <hr>
                    <div class="d-flex gap-3 mt-3 justify-content-center justify-content-between">
                        @if ($announcement->likes->contains('user_id', auth()->user()->id))
                        <span class="position-absolute" style="margin-top: -13px; font-size: 12px;">
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
                            <button class="btn btn-link text-decoration-none"
                                wire:click='unlike({{ $announcement->id }})'>

                                <i class="fas fa-thumbs-up"></i> Like
                            </button>
                        </div>
                        @else
                        @if ($announcement->likes->count() > 0)
                        <span class="position-absolute" style="margin-top: -13px;">
                            @if ($announcement->likes->count() <= 1) {{ $announcement->likes->count() }} liked this post
                                @else
                                {{ $announcement->likes->count() }} likes this post
                                @endif
                        </span>
                        @endif
                        <button class="btn btn-link text-decoration-none text-white"
                            wire:click='like({{ $announcement->id }})'>
                            <i class="far fa-thumbs-up"></i> Like
                        </button>
                        @endif
                        <button class="btn btn-link text-decoration-none text-white">
                            <i class="far fa-comment-dots"></i> Comment
                        </button>
                        <div class="dropstart">
                            <button class="btn btn-link text-decoration-none text-white" role="button"
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
                                    <a class="dropdown-item" href="#"><i class="far fa-copy"></i>
                                        <strong>Copy Link</strong></a>
                                </li>
                                @else
                                <li
                                    onclick="copyLink('{{ url('http://136.239.196.178:5004/updates/' . $announcement->post_title) }}'); return false;">
                                    <a class="dropdown-item" href="#"><i class="far fa-copy"></i>
                                        <strong>Copy Link</strong></a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center mt-5">
                    <p class="text-light"><i class="far fa-mailbox-flag-up fs-1"></i></p>
                    <h1 class="text-light">No posts yet.</h1>
                </div>
                @endforelse
            </div>

            <!-- Right Sidebar: Post Trends -->
            <div class="col-md-3 mb-4">
                <div class="bg-info-subtle sticky-top p-3 rounded shadow-sm" style="z-index: 1;">
                    <h5 class="mb-3 fw-bold"><i class="fas fa-fire text-danger"></i> Post Trends</h5>
                    <ul class="list-unstyled">
                        @forelse ($post_trends as $trend)
                        <li class="mb-2"><a href="/updates/{{ $trend->post_title }}" wire:navigate
                                class="text-dark"><strong><span class="fs-6">{{ $trend->post_title
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

        .text-break p img {
            margin-left: 10px;
            margin-bottom: 10px;
            max-width: 300px;
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
                            [{ 'size': [] }],
                            ['bold', 'italic', 'underline'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            ['image', 'link'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Compose a post...',
                });

                const textarea = document.getElementById('post-content-textarea');

                @this.on('formSubmitted', () => {
                    quill.setContents([]);
                });

                quill.on('text-change', function () {
                    textarea.value = quill.root.innerHTML;
                    textarea.dispatchEvent(new Event('input'));
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

</div>
