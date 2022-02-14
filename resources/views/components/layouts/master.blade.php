<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/admin.css') }}">
    {{ seo()->generate() }}
</head>
<body>
    {{ $slot }}
    <script>
        window.Translations = @json(Localization::getTranslationsForClient());
    </script>
    <script src="{{ mix('js/admin.js') }}"></script>
    @if (session()->has('message'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Toastr.add('{{ session()->get('message') }}')
            })
        </script>
    @endif
    {{ $scripts ?? '' }}
</body>
</html>