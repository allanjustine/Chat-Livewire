<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We'll Be Back Soon!</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KyZXEJ8vLx0u5LfiyI4z0l4vs6oLZkD0l6WauP5UqDpyKvEXh1/Ow1V2QQB2ugK6" crossorigin="anonymous">

    <!-- Custom Styles -->
    <style>
        body {
            height: 100vh;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .maintenance-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            color: #333;
        }

        .maintenance-container h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .maintenance-container p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .gif-container img {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .contact-info {
            font-size: 1rem;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container maintenance-container">
        <!-- GIF Container -->
        <div class="gif-container">
            <img src="https://media.giphy.com/media/xT0BKhc8u9kdwT2OUQ/giphy.gif" alt="Maintenance GIF">
        </div>

        <!-- Message -->
        <h1>We Are Currently Under Maintenance</h1>
        <p>Sorry for the inconvenience, we are performing scheduled maintenance. We’ll be back online shortly!</p>

        <!-- Retry Button -->
        <a href="/" class="btn btn-primary">Retry</a>

        <!-- Contact Information -->
        <div class="contact-info">
            <p>If you need assistance, feel free to reach out to us at <a
                    href="mailto:support@example.com">support@example.com</a></p>
        </div>
    </div>

    <!-- Bootstrap JS (Optional, for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz4fnFO9gybTlu2a9bU6Bh0vLbHtbZjZGptlfgpLcpCdU9zK9VqaF5zR2Zg" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
        integrity="sha384-pzjw8f+ua7Kw1TIq0tJ9gC4m2xuM4dujHL4pPPR64C3SQjZrdkQm0dpXmj4/s2f8f" crossorigin="anonymous">
    </script>

</body>

</html>
