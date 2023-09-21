@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center bravo-login-form-page bravo-login-page">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>
                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    {{ __('If you did not receive the email') }}, <button class="btn btn-primary">{{ __('click here to request another') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
