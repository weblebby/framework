<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $app->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @seo
    @stack('before_styles')
    @vite('resources/css/feadmin.css', 'feadmin/build')
    @stack('after_styles')
</head>
<body>
{{ $slot }}
<script>
  window.Feadmin = {};
</script>
@hook(panel()->nameWith('after_js_feadmin_object'))
<script>
  Feadmin.API = {
    baseUrl: @json('https://' . config('app.domains.api')),
  };
</script>
@stack('before_scripts')
@vite('resources/js/feadmin.js', 'feadmin/build')
@if (session()->has('message'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Feadmin.Toastr.add('{{ session()->get('message') }}');
      });
    </script>
@endif
@stack('after_scripts')
</body>
</html>
