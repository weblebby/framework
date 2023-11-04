<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $app->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css', 'vendor/feadmin')
    @seo
    {{ $styles ?? '' }}
</head>
<body>
{{ $slot }}
<script>
  window.Feadmin = {};
</script>
@if (\Feadmin\Support\Features::enabled(\Feadmin\Support\Features::translations(), panel()))
    <script>
      window.Feadmin.Translation = {
        routes: {
          update: @json(panel_route('translations.store'))
        },
        list: @json(Localization::getTranslations())
      };
    </script>
@endif
<script>
  Feadmin.API = {
    baseUrl: @json(panel_route('dashboard')),
  };
</script>
@vite('resources/js/app.js', 'vendor/feadmin')
@if (session()->has('message'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Feadmin.Toastr.add('{{ session()->get('message') }}');
      });
    </script>
@endif
{{ $scripts ?? '' }}
</body>
</html>
