<!doctype html>
<html lang="{{ str_replace('_', '-', $app->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Raleway', -apple-system, sans-serif;
            background-color: #efefef;
            height: 100vh;
            font-size: 16px;
        }

        h1 {
            color: #333333;
            margin: 0;
        }

        .container {
            height: 100vh;
            margin-top: 5rem;
            margin-left: 10rem;
        }

        .content {
            color: #333333;
        }

        a {
            color: rgb(27, 19, 61);
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ $title }}</h1>
    <div class="content">{{ $slot }}</div>
</div>
</body>
</html>