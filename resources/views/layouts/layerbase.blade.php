
@if(isset($isisolatepage))
    <!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Styles -->
        <link href="{{ asset('layer/css/layui.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body style="background-color: #f8f8f8;">
        <div id="app" class="clearfix">
            @yield('content')
        </div>
        <script src="{{ asset('layer/layui.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
    </html>
@else
    @yield('content')
@endif

@yield('script')