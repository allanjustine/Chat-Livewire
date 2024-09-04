<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

// Blade call
// Toastr

<script>
document.addEventListener('livewire:navigated',()=>{
@this.on('success', (event) => {
const data=event
toastr[data[0].type](data[0].message, '', {
closeButton: true,
"progressBar": true,
});
})
});

    </script>

    //Sweetalert2

    <script>
        document.addEventListener('livewire:navigated',()=>{

        @this.on('success',(event)=>{
            const data=event
            swal.fire({
                icon:data[0]['icon'],
                title:data[0]['title'],
                text:data[0]['text'],
                html: "You will redirected to Login page <br>Thank you!",
            }).then(function () {
                Livewire.navigate('/login');
        });
        })

        // confirm delete using sweetalert2
        @this.on('delete-prompt',(event)=>{
            swal.fire({
                title:'Are you sure?',
                text:'You are about to delete this record, this action is irreversible',
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#3085d6',
                showCancelButtonColor:'#d33',
                confirmButtonText:'Yes, Delete it!',
            }).then((result)=>{
                if(result.isConfirmed){
                    @this.dispatch('goOn-Delete')

                    @this.on('deleted',(event)=>{
                       swal.fire({
                        title:'Deleted',
                        text:'Your record has been deleted',
                        icon:'success',
                       })
                    })
                }
            })
        })


    })
    </script>


    // Controller call
        //swal
        $this->dispatch('success', [
             'title'       =>          'Registered',
             'text'        =>          'Registered successfully',
             'icon'        =>          'success'
         ]);
         //toastr
        $this->dispatch('success', [
            'type'          =>          'success',
            'message'       =>          'Registered successfully',
        ]);



    <script data-navigate-once src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

// Messages type code
// $urlPattern = '@(https?://[^\s<>"\'()]+\.[^\s<>"\'()]+)@i';

        // foreach ($convos as $convo) {
        //     $convo->message = preg_replace($urlPattern, '<a href="$1" target="_blank" style="color: #095ad2;" title="$1"><strong>$1</strong></a>', $convo->message);
        // }



//script from chatMessages


        <script>
        document.addEventListener("livewire:navigated", function() {
            var chatMessages = document.getElementById("chatMessages");
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    </script>
    <script>
        document.addEventListener("livewire:navigated", function() {
            Livewire.hook('message.processed', (message, component) => {
                var chatMessages = document.getElementById("chatMessages");
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        });
    </script>
