@extends('layouts.vue_single')

@section('content')
<common-msg
    :content="{{ isset($content)?json_encode($content):'' }}">
</common-msg>
@endsection
