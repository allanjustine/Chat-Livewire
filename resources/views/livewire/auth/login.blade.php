<div>
    <div class="w-100 d-flex justify-content-center align-items-center" style="height: 91.5vh; background: url('/images/bg.gif') no-repeat center center fixed;
        background-size: cover;">
        <div class="card shadow py-2 col-12 col-sm-10 col-md-5 col-lg-4 col-xl-3" style="opacity: 0.97">
            <h4 class="text-center">Login</h4>
            <div class="card-body">
                <form wire:submit.prevent='login'>
                    <div class="mb-3">
                        <label for="username_or_email" class="form-label">Username/Email</label>
                        <input type="text" class="form-control" id="username_or_email" name="username_or_email"
                            wire:model='username_or_email'>
                        @error('username_or_email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" wire:model='password'>
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary form-control btn-sm">
                        <span wire:loading.remove>Login</span>
                        <div wire:loading>
                            <span class="spinner-grow spinner-grow-sm"></span>
                            <span class="spinner-grow spinner-grow-sm"></span>
                            <span class="spinner-grow spinner-grow-sm"></span>
                        </div>
                    </button>
                </form>
            </div>
            <div class="card-footer text-center ">
                <a href="/register" wire:navigate class="btn btn-link">Register an account</a>
            </div>
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