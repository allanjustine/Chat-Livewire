<div>
    <div class="container mt-5">
        <div class="profile-header">
            <img class="border shadow" @if ($profileData->profile_picture === null)
            src="/images/profile.png"
            @else
            src="{{ Storage::url($profileData->profile_picture) }}"
            @endif
            alt="{{ $profileData->name }}">
            <div>
                <strong>
                    <h2 class="text-light">{{ $profileData->name }}</h2>
                </strong>
                <div class="text-light">({{ $profileData->nickname ?: 'No nickname' }})</div>
                {{-- <p>2.3K friends</p> --}}
                <div class="mt-3">
                    <a class="btn btn-primary" href="/chats/{{ $profileData->user_token }}" wire:navigate><i
                            class="far fa-paper-plane-top"></i> Send message</a>
                </div>
            </div>
        </div>

        <hr>

        <ul class="nav nav-tabs">
            <li class="nav-items">
                <strong><a class="nav-link bg-dark-subtle text-dark" href="#">About</a></strong>
            </li>
        </ul>

        <div class="text-white intro-section bg-dark">
            <h4>Intro</h4>
            <div class="d-flex gap-5">
                <div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-calendar"></i>
                            </div>
                            <div>{{ \Carbon\Carbon::parse($profileData->date_of_birth)->format('F d, Y') }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-location-dot"></i>
                            </div>
                            <div>{{ $profileData->address }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-input-numeric"></i>
                            </div>
                            <div>{{ $profileData->age }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-phone"></i>
                            </div>
                            <div>{{ $profileData->phone_number }}</div>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                @if ($profileData->gender === 'Male')
                                <i class="far fa-mars"></i>
                                @elseif($profileData->gender === 'Female')
                                <i class="far fa-venus"></i>
                                @else
                                <i class="fa-thin fa-mars-and-venus-burst"></i>
                                @endif
                            </div>
                            <div>{{ $profileData->gender }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-book-user"></i>
                            </div>
                            <div class="text-break">{{ $profileData->bio ?: 'No bio' }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-envelope"></i>
                            </div>
                            <div>{{ $profileData->email }}</div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2" style="width: 20px;">
                                <i class="far fa-user"></i>
                            </div>
                            <div>{{ $profileData->username }}</div>
                        </li>
                    </ul>
                </div>
            </div>
            @if (auth()->user()->id === $profileData->id)
            <a class="btn btn-primary" href="/profile" wire:navigate><i class="far fa-pen"></i> Edit details</a>
            @endif
        </div>
    </div>

    <style>
        .profile-header {
            display: flex;
            align-items: center;
            padding: 20px;
        }

        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .nav-tabs {
            margin-top: 20px;
        }

        .nav-items {
            width: 20%;
            text-align: center;
        }

        .intro-section {
            padding: 20px;
            border-radius: 0 0 11px 11px;
            margin-bottom: 20px;
        }
    </style>
</div>
