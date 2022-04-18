<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/feadmin.css', 'vendor/feadmin') }}">
    {{ seo()->generate() }}
    {{ $styles ?? '' }}
</head>
<body>
    {{ $slot }}
    <script>
        window.Feadmin = {
            @if (\Feadmin\Features::enabled(\Feadmin\Features::translations(), panel()))
            Translation: {
                routes: {
                    update: @json(panel_route('translations.store'))
                },
                list: @json(Localization::getTranslations())
            }
            @endif
        }
    </script>
    <script src="{{ mix('js/feadmin.js', 'vendor/feadmin') }}"></script>
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