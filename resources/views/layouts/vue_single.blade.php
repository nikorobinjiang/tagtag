<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<?php $vue_ele_id = '_' . str_random(16); ?>
<body>
    <div id="{{ $vue_ele_id }}">
    @yield('content')
    </div>
</body>

<link rel="stylesheet" href="{{ mix('css/main.css') }}">
<script src="{{ mix('js/main.js') }}"></script>

<style>
body {
    margin: 0px;
}
</style>
@yield('script')
<script>
    var {{ $vue_ele_id }} = new Vue({
        el: '#{{ $vue_ele_id }}',
        store: VueStore
    });

    $(document).on('click', ".am-close", function(){
        {{ $vue_ele_id }}.$destroy();
    });
</script>
