<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .not-found-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            background-color: #f8f9fa;
        }

        .not-found-container h1 {
            font-size: 6rem;
            font-weight: bold;
        }

        .not-found-container p {
            font-size: 1.2rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="not-found-container">
        <img src="https://www.myphukettravel.com/assets/front-end/images/404.gif" alt="Not Found GIF"
            class="img-fluid mb-4" style="max-width: 400px;">
        <p>Oops! The page you are looking for does not exist.</p>
        <button type="button" onclick="goBack()" class="btn btn-primary mt-3">Go Back</button>
    </div>

    <script>
        function goBack()
        {
            window.history.back();
        }
    </script>
    <script data-navigate-once src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script data-navigate-once src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script data-navigate-once src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
