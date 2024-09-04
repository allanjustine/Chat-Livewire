<div>
    <div class="container d-flex justify-content-center mt-5">
        <div class="col-md-6 mt-5">
            <div class="mb-4 bg-dark text-light p-4 rounded shadow-sm position-relative">
                <span class="position-absolute top-0 end-0 pe-2 pt-2" style="font-size: 12px;">Posted on {{
                    $updatesData->created_at->format('F d, Y g:i A') }}</span>
                <div class="text-light">
                    <div class="d-flex align-items-center mb-2">
                        <img @if ($updatesData->user->profile_picture === null)
                        src='/images/profile.png'
                        @else
                        src="{{ Storage::url($updatesData->user->profile_picture) }}"
                        @endif
                        width="40" height="40"
                        class="rounded-circle me-2" alt="{{ $updatesData->user->name }}">
                        <div>
                            <strong>{{ $updatesData->user->name }}</strong>
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
                <div class="mt-3">
                    <h2 class="mb-3">{{ $updatesData->post_title }}</h2>
                </div>
                <div>
                    <ul class="list-unstyled">
                        @foreach ($updatesData->post_attachment as $file)
                        <li class="mb-2">
                            <a href="{{ Storage::url($file) }}" download="{{ $file }}">
                                {{ basename($file) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="text-break">
                    {!! $updatesData->post_content !!}
                </div>
                <hr>
                <div class="d-flex gap-3 mt-3 justify-content-center justify-content-between">
                    @if ($updatesData->likes->contains('user_id', auth()->user()->id))
                    <span class="position-absolute" style="margin-top: -13px; font-size: 12px;">
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
                    <div>
                        <button class="btn btn-link text-decoration-none" wire:click='unlike({{ $updatesData->id }})'>

                            <i class="fas fa-thumbs-up"></i> Like
                        </button>
                    </div>
                    @else
                    @if ($updatesData->likes->count() > 0)
                    <span class="position-absolute" style="margin-top: -13px;">
                        @if ($updatesData->likes->count() <= 1) {{ $updatesData->likes->count() }} liked this post
                            @else
                            {{ $updatesData->likes->count() }} likes this post
                            @endif
                    </span>
                    @endif
                    <button class="btn btn-link text-decoration-none text-white"
                        wire:click='like({{ $updatesData->id }})'>
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
