@extends('core::layouts.auth')
@section('title')
    {{ trans_choice('user::general.login', 1) }}
@endsection
@section('scripts')
    <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" data-auto-replace-svg="nest"></script>
@endsection
@section('css')
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <style type="text/css">
        body.login-page {
            background-color: #ffc800;
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
                height: 50%;
                width: 50%;
            }

            .login-logo {
                background-color: #003466;
                /* background-size: cover; */
                position: fixed;
                right: 0px;
                width: 50%;
                min-height: 100%;
                padding: 0;
                top: 0;
                margin-right: 0%;
            }

            .login-box {
                background-color: #ffc800;
                position: absolute;
                left: 0px;
                width: 50%;
                padding: 0;
                margin-left: 10%;
                margin-top: 5%;

            }

            .services {
                width: 100px;
                hight: 100px;
            }
            .icon{
                position: fixed;
                bottom:5%;
            }

        }

        @media screen and (min-device-width: 481px) and (max-device-width: 768px) {

            /* STYLES HERE */

            .login-logo img {
                height: 50%;
                width: 50%;
                float: center;
            }

            .login-logo {
                background-color: #003466;
                /* background-size: cover; */
                position: fixed;
                right: 0px;
                width: 50%;
                min-height: 100%;
                padding: 0;
                top: 0;
                margin-right: 0%;
            }

            .login-box {
                background-color: #ffc800;
                position: absolute;
                left: 0px;
                width: 50%;
                padding: 0;
                margin-left: 10%;
                margin-top: 5%;

            }
             .services {
                width: 100px;
                hight: 100px;
            }
            .icon{
                position: fixed;
                bottom:5%;
            }

        }

        @media only screen and (max-device-width: 480px) {


            .login-logo img {
                height: 80%;
                width: 100%;
                margin-top: 100%;
            }

            .login-logo {
                background-color: #003466;
                /* background-size: cover; */
                position: fixed;
                right: 0px;
                width: 50%;
                min-height: 100%;
                padding: 0;
                top: 0;
                margin-right: 0%;
            }

            .login-box {
                background-color: #ffc800;
                position: absolute;
                left: 0px;
                width: 50%;
                padding: 0;
                margin-left: 10%;
                margin-top: 5%;

            }
             .services {
                width: 100px;
                hight: 100px;
            }
            .icon{
                position: fixed;
                bottom:5%;
            }
        }
    </style>
@endsection
@section('content')
    @yield('css')
    <div class="login-box">
        <div class="miro_loan">
            <a href="{{ url('services/mircoLoan') }}">
                <img class="services" src="{{ asset('storage/uploads/miro_loan1.png') }}" alt="micro_loan"
                    style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white">{{ trans_choice('user::general.micro_loan', 1) }}</p>
            </a>
        </div>
        <div class="rate_bussiness">
            <a href="">
                <img class="services" src="{{ asset('storage/uploads/rate_yourBus.png') }}" alt="rate_bussiness"
                    style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white">{{ trans_choice('user::general.rate_bussiness', 1) }}</p>
            </a>
        </div>
        <div class="lease_finance">
            <a href="">
                <img class="services" src="{{ asset('storage/uploads/lease_finance.png') }}" alt="lease_finance"
                    style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white;">{{ trans_choice('user::general.lease_finance', 1) }}</p>
            </a>
        </div>
        <div class="micro_insurance">
            <a href="">
                <img class="services" src="{{ asset('storage/uploads/micro_insurance.png') }}" alt="micro_insurance"
                    style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white;">{{ trans_choice('user::general.micro_insurance', 1) }}</p>
            </a>
        </div>
        <div class="payment">
            <a href="">
                <img class="services" src="{{ asset('storage/uploads/payment.png') }}"
                    alt="payment"style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white;">{{ trans_choice('user::general.payment', 1) }}</p>
            </a>
        </div>
        <div class="nearest_service_outlet">
            <a href="">
                <img class="services" src="{{ asset('storage/uploads/nearest.png') }}"
                    alt="nearest_service_outlet"style="border-style: inset; border: solid white 5px; padding:15px">
                <p style="color:white;">{{ trans_choice('user::general.nearest_service_outlet', 1) }}</p>
            </a>
        </div>
    </div>
    <div class="login-logo">
        <a href="{{ url('/') }}" class="logo-link text-center">
            <img class="logo-light logo-img logo-img-lg" src="{{ asset('storage/uploads/Capture.PNG') }}"
                srcset="{{ asset('storage/uploads/Capture.PNG') }} 4x" alt="logo">
            <p style="color: white">Powered by elebat solution </p>
        </a>
        <a href="{{url('/')}}"><i style="color:white;" class="fas fa-home icon"></i></a>
    </div>
@endsection
