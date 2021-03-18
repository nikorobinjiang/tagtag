@extends('layouts.app_vue_single')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
                    <auth-form 
                        error_info="{{ $errors }}" 
                        form_action="{{ route('login') }}"
                        old_email="{{ old('email') }}"
                        pw_request="{{ route('password.request') }}"
                        remember="{{ old('remember') }}"
                        >
                    </auth-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
