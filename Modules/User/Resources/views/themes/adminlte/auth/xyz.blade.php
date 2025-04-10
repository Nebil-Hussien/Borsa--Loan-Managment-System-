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

            .btn_holder {
                float: left;
                margin-left: 25%;
                width: 50%;
                
            }
            .btn-new{
                width: 200%;
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
                float: right;
                right: 0px;
                width: 50%;
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
          /* STYLES HERE */

            .login-logo img {
                height: 15%;
                width: 100%;
                align:center;
            }

            .btn_holder {
                float: center;
                margin-left: 10px;
                margin-left: 10px;
                width: 100%;
                
            }
            .btn-new{
                width: 100%;
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
                float: left;
                right: 0px;
                width: 100%;
                padding: 10px;
                margin-left: 10px;
                margin-left: 10px;

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

        @media only screen and (max-device-width: 480px) {

           /* STYLES HERE */

            .login-logo img {
                height: 25%;
                width: 100%;
            }

            .btn_holder {
                float: left;
                margin-left: 3%;
                width: 100%;
                
            }
            .btn-new{
                width: 100%;
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
                float: center;
                right: 0px;
                width: 100%;
                padding: 10px;
                margin-left: 10px;
                margin-right: 10px;

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
            <div class="btn_holder">
                @if (\Modules\Setting\Entities\Setting::where('setting_key', 'user.enable_registration')->first()->setting_value == 'yes')
                <hr>
                    <div class="mb-1 col-md-6 col-xs-12">
                    <a href="{{ route('register') }}"><button class="btn btn-lg btn-primary btn-block btn-new"  style="border-radius:15px;background-color:#fabe00;color:black;font:bold;">{{ trans_choice('user::general.register_msg', 1) }}</button></a>
                </div>
                <div class="mb-1 col-md-6 col-xs-12">
                    <a
                        href="{{ route('register') }}?type=company"><button class="btn btn-lg btn-primary btn-block btn-new"  style="border-radius:15px;background-color:#fabe00;color:black;font:bold;">{{ trans_choice('user::general.register_msg_company', 1) }}</button></a>
                </div>
                <div class="mb-1 col-md-6 col-xs-12">
                    <a
                        href="{{ route('login') }}"><button class="btn btn-lg btn-primary btn-block btn-new" style="border-radius:15px;background-color:red;color:black;font:bold;" >{{ trans_choice('user::general.sign_in', 1) }}</button></a>
                </div>
            </div>
            @endif
    </div>
        
@endsection
