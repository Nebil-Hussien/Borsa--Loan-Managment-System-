@extends('core::layouts.auth')
@section('title')
    {{ trans_choice('user::general.login', 1) }}
@endsection
@section('css')
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <style type="text/css">
        body.login-page {
            background-color: #003466;
            background-size: cover;

        }

        .pwerd-e {
            color: aqua;
            text-align: center;
            font-family: "Comfortaa", sans-serif;
            font-weight: 280;

        }

        @media screen and (min-width: 769px)and (max-device-width: 1920px) {

            /* STYLES HERE */

            .login-logo img {
                height: 10%;
                width: 50%;
            }

            .service_button {
                position: absolute;
                width: 500px;
                height: 50px;
                float: left;
                right: 150px;
                bottom: 190px;
                
            }

            .login-logo {
                margin-bottom: 10px;
                margin-top: 10px;
                border-radius: 5px;
                background-color: transparent;
            }

            .login-box-msg {
                padding: 0;
                margin-top: 8px;

            }

            .login-box {
                background-color: transparent;
                position: absolute;
                left: 0px;
                width: 45%;
                padding: 10px;
                margin-left: 40px;

            }

            .card {
                background: transparent;
            }

            .form-label,
            .custom-control-label {
                color: #fabe00;
            }

            .login-box-msg {
                color: white;
            }

            #register {
                color: white;
            }


        }

        @media screen and (min-device-width: 481px) and (max-device-width: 768px) {

            /* STYLES HERE */
            .login-logo img {
                height: 10%;
                width: 50%;
            }

            .login-logo {
                margin-bottom: 5%;
                margin-top: 12%;
            }

            .login-box-msg {
                padding: 0;

            }

            .login-box {

                background: transparent;
                float: left;
                display: block;
                width: 100%;
                padding: 5%;
                margin-left: 5%;

            }

            .card {
                background: transparent;
            }

            .form-label,
            .custom-control-label {
                color: #fabe00;
            }

            .login-box-msg {
                color: white;
            }
        }

        @media only screen and (max-device-width: 480px) {

            /* STYLES HERE */
            .login-logo img {
                height: 10%;
                width: 50%;
            }

            .login-logo {
                margin-bottom: 5%;
                margin-top: 12%;
            }

            .login-box-msg {
                padding: 0;

            }

            .login-box {

                background: transparent;
                float: left;
                display: block;
                width: 100%;
                padding: 5%;
                margin-left: 5%;

            }

            .card {
                background: transparent;
            }

            .form-label,
            .custom-control-label {
                color: #fabe00;
            }

            .login-box-msg {
                color: white;
            }
        }
    </style>
@endsection
@section('content')
    @yield('css')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}" class="logo-link text-center">
                <img class="logo-light logo-img logo-img-lg" src="{{ asset('storage/uploads/Capture.PNG') }}"
                    srcset="{{ asset('storage/uploads/Capture.PNG') }} 2x" alt="logo">
            </a>
        </div>
        <p class="pwerd-e">Powered By elebat</p>
        <div class="card">
            <div class="card-body login-card-body" style="background-color: transparent">
                <form method="post" action="{{ route('login') }}" style="width: 75%">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="email">{{ trans_choice('user::general.email', 1) }}</label>
                        </div>
                        <input type="email" class="form-control form-control-lg  @error('email') is-invalid @enderror"
                            style="border-radius: 5px; border: 2px solid #fabe00; background-color:transparent" name="email"
                            placeholder="{{ trans_choice('user::general.email', 1) }}" value="{{ old('email') }}"
                            required autocomplete="email" id="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label"
                                for="password">{{ trans_choice('user::general.password', 1) }}</label>
                            {{-- <a class="link link-primary link-sm" tabindex="-1" href="{{ route('password.request') }}">
                                {{ trans_choice('user::general.forgot_password', 1) }}
                            </a> --}}
                        </div>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                                data-target="password">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" name="password"
                                style="border-radius: 5px; border: 2px solid #fabe00; background-color:transparent"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                placeholder="{{ trans_choice('user::general.password', 1) }}" required
                                autocomplete="password" id="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" {{ old('remember') ? 'checked' : '' }}
                                id="remember">
                            <label class="custom-control-label"
                                for="remember">{{ trans_choice('user::general.remember_me', 1) }}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary btn-block"
                            style="border-radius: 5px;background-color:#fabe00;color:black">{{ trans_choice('user::general.login', 1) }}</button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
    <div class="service_button">
                <a href="/services"><button class="btn btn-lg btn-primary btn-block"
                style="border-radius: 5px;background-color:red;color:black">{{ trans_choice('user::general.services', 1) }}</button></a>
    </div>
    
@endsection
