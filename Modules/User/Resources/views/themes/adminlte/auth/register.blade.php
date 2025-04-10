@extends('core::layouts.auth')
@section('title')
    {{ trans_choice('user::general.register', 1) }}
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datepickr/bootstrap-datepicker.min.css') }}">
    <style type="text/css">
        .register-box {
            width: 100% !important;
        }

        .head_title {
            border-bottom: 2px solid #007bff;
            color: #007bff;
            text-transform: uppercase;
        }

        @media screen and (min-width: 769px)and (max-device-width: 1920px) {
            .login-logo img {
                height: 5%;
                width: 25%;
            }

            .login-logo {
                margin-bottom: 10px;
                margin-top: 10px
            }
        }

        @media screen and (min-device-width: 481px) and (max-device-width: 768px) {
            .login-logo img {
                height: 5%;
                width: 25%;
            }

            .login-logo {
                margin-bottom: 0px;
                margin-top: 0px
            }

        }

        @media only screen and (max-device-width: 480px) {
            .login-logo img {
                height: 5%;
                width: 25%;
            }

            .login-logo {
                margin-bottom: 10px;
                margin-top: 10px
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="login-box register-box">
            <div class="login-logo">
                <a href="{{ url('/') }}" class="logo-link text-center">
                    <img class="logo-light logo-img logo-img-lg" src="{{ asset('storage/uploads/Capture.PNG') }}"
                        srcset="{{ asset('storage/uploads/borsa_logo.jpg') }} 2x" alt="logo">
                </a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">{{ trans_choice('user::general.register_msg', 1) }}</p>
                    <form method="post" action="{{ route('register') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="signup_type" value="individual" id="signup_type">
                         <div class="row">
                            <div class="col-md-12">
                                <h5 class="head_title">{{ trans_choice('user::general.user_credentials', 1) }}
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="email">{{ trans_choice('user::general.email', 1) }}</label>
                                    </div>
                                    <input type="email" class="form-control  @error('email') is-invalid @enderror"
                                        name="email" placeholder="{{ trans_choice('user::general.email', 1) }}"
                                        value="{{ old('email') }}" required autocomplete="email" id="email"
                                        autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="username">{{ trans_choice('user::general.username', 1) }}</label>
                                    </div>
                                    <input type="username" class="form-control  @error('username') is-invalid @enderror"
                                        name="username" placeholder="{{ trans_choice('user::general.username', 1) }}"
                                        value="{{ old('username') }}" required id="username" autofocus>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="password">{{ trans_choice('user::general.password', 1) }}</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <a tabindex="-1" href="#"
                                            class="form-icon form-icon-right passcode-switch" data-target="password">
                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                        </a>
                                        <input type="password" name="password"
                                            class="form-control  @error('password') is-invalid @enderror"
                                            placeholder="{{ trans_choice('user::general.password', 1) }}" required
                                            autocomplete="off" id="password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="password_confirmation">{{ trans_choice('user::general.password_confirmation', 1) }}</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <a tabindex="-1" href="#"
                                            class="form-icon form-icon-right passcode-switch"
                                            data-target="password_confirmation">
                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                        </a>
                                        <input type="password" name="password_confirmation"
                                            class="form-control  @error('password_confirmation') is-invalid @enderror"
                                            placeholder="{{ trans_choice('user::general.password_confirmation', 1) }}"
                                            required id="password_confirmation">
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="head_title">{{ trans_choice('user::general.applicant_details', 1) }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('first_name') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="first_name">{{ trans_choice('user::general.first_name', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        name="first_name" id="first_name"
                                        placeholder="{{ trans_choice('user::general.first_name', 1) }}"
                                        value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('middle_name') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="middle_name">{{ trans_choice('user::general.middle_name', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                        name="middle_name" id="middle_name"
                                        placeholder="{{ trans_choice('user::general.middle_name', 1) }}"
                                        value="{{ old('middle_name') }}" required autocomplete="middle_name" autofocus>
                                    @error('middle_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('last_name') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="last_name">{{ trans_choice('user::general.last_name', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                        name="last_name" id="last_name"
                                        placeholder="{{ trans_choice('user::general.last_name', 1) }}"
                                        value="{{ old('last_name') }}" required autocomplete="last_name">
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('mother_name') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="mother_name">{{ trans_choice('user::general.mother_name', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('mother_name') is-invalid @enderror"
                                        name="mother_name" id="mother_name"
                                        placeholder="{{ trans_choice('user::general.mother_name', 1) }}"
                                        value="{{ old('mother_name') }}" required autocomplete="mother_name">
                                    @error('mother_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <?php $genders = config('user.genders'); ?>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('gender') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="gender">{{ trans_choice('user::general.gender', 1) }}</label>
                                    </div>
                                    <select class="form-control @error('gender') is-invalid @enderror" name="gender"
                                        id="gender" required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('user::general.gender', 1) }}</option>
                                        @foreach ($genders as $key => $gender)
                                            <option value="{{ $key }}" {{ old('gender') != $key ?: 'selected' }}>
                                                {{ $gender }}</option>
                                        @endforeach

                                    </select>
                                    @error('gender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('phone') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="phone">{{ trans_choice('user::general.phone', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone"
                                        placeholder="{{ trans_choice('user::general.phone', 1) }}"
                                        value="{{ old('phone') }}" required autocomplete="phone">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('dob') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="age">{{ trans_choice('user::general.dob', 1) }}</label>
                                    </div>
                                    <input type="text"
                                        class="datepicker form-control  @error('dob') is-invalid @enderror"
                                        name="dob" id="dob" value="{{ old('dob') }}"
                                        placeholder="{{ trans_choice('user::general.date_format', 1) }}" readonly
                                        autocomplete="dob" autofocus>
                                    @error('dob')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="head_title">{{ trans_choice('user::general.address_details', 1) }}</h5>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group has-feedback @error('house_no') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="house_no">{{ trans_choice('user::general.house_no', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('house_no') is-invalid @enderror"
                                        name="house_no" id="house_no"
                                        placeholder="{{ trans_choice('user::general.house_no', 1) }}"
                                        value="{{ old('house_no') }}" required autocomplete="house_no" autofocus>
                                    @error('house_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group has-feedback @error('address') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="address">{{ trans_choice('user::general.address', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        name="address" id="address"
                                        placeholder="{{ trans_choice('user::general.address', 1) }}"
                                        value="{{ old('address') }}" required autocomplete="address" autofocus>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('region') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="region">{{ trans_choice('user::general.region', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('region') is-invalid @enderror"
                                        name="region" id="region"
                                        placeholder="{{ trans_choice('user::general.region', 1) }}"
                                        value="{{ old('region') }}" required autocomplete="region" autofocus>
                                    @error('region')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('city_id') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="city_id">{{ trans_choice('user::general.city', 1) }}</label>
                                    </div>
                                    <select class="form-control select2" name="city_id" id="city_id" required>
                                        <option value="" disabled selected>Select
                                        </option>
                                        @foreach ($cities as $key)
                                            <option value="{{ $key->shortName }}"
                                                {{ old('city_id') != $key->id ?: 'selected' }}>{{ $key->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('zone') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="zone">{{ trans_choice('user::general.zone', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('zone') is-invalid @enderror"
                                        name="zone" id="zone"
                                        placeholder="{{ trans_choice('user::general.zone', 1) }}"
                                        value="{{ old('zone') }}" autocomplete="zone" autofocus>
                                    @error('zone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('kebele') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="kebele">{{ trans_choice('user::general.kebele', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('kebele') is-invalid @enderror"
                                        name="kebele" id="kebele"
                                        placeholder="{{ trans_choice('user::general.kebele', 1) }}"
                                        value="{{ old('kebele') }}" required autocomplete="kebele" autofocus>
                                    @error('kebele')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('state') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="state">{{ trans_choice('user::general.state', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                        name="state" id="state"
                                        placeholder="{{ trans_choice('user::general.state', 1) }}"
                                        value="{{ old('state') }}" autocomplete="state" autofocus>
                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('country_id') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="country_id">{{ trans_choice('core::general.country', 1) }}</label>
                                    </div>
                                    <select class="form-control select2" name="country_id" id="country_id" required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('core::general.select', 1) }}
                                        </option>
                                        @foreach ($countries as $key)
                                            <option value="{{ $key->id }}"
                                                {{ old('country_id') != $key->id ?: 'selected' }}>{{ $key->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="head_title">{{ trans_choice('user::general.identification', 1) }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('country_id') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="client_identification_type_id">{{ trans_choice('user::general.identification_type', 1) }}</label>
                                    </div>
                                    <select
                                        class="form-control @error('client_identification_type_id') is-invalid @enderror"
                                        name="client_identification_type_id" id="client_identification_type_id"
                                        v-model="client_identification_type_id" required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('core::general.select', 1) }}
                                        </option>
                                        @foreach ($client_identification_types as $key)
                                            <option value="{{ $key->id }}">{{ $key->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_identification_type_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('identification_no') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="identification_no">{{ trans_choice('user::general.identification_no', 1) }}</label>
                                    </div>
                                    <input type="text"
                                        class="form-control @error('identification_no') is-invalid @enderror"
                                        name="identification_no" id="identification_no"
                                        placeholder="{{ trans_choice('user::general.identification_no', 1) }}"
                                        value="{{ old('identification_no') }}" required autocomplete="identification_no"
                                        autofocus>
                                    @error('identification_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('identification_issued') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="identification_issued">{{ trans_choice('user::general.identification_issued', 1) }}</label>
                                    </div>
                                    <input type="text"
                                        class="datepicker form-control  @error('identification_issued') is-invalid @enderror"
                                        name="identification_issued" id="identification_issued"
                                        value="{{ old('identification_issued') }}"
                                        placeholder="{{ trans_choice('user::general.date_format', 1) }}" required readonly
                                        autocomplete="identification_issued" autofocus>
                                    @error('identification_issued')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('identification_expire') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="identification_expire">{{ trans_choice('user::general.identification_expire', 1) }}</label>
                                    </div>
                                    <input type="text"
                                        class="datepicker form-control @error('identification_expire') is-invalid @enderror"
                                        name="identification_expire" id="identification_expire"
                                        placeholder="{{ trans_choice('user::general.date_format', 1) }}"
                                        value="{{ old('identification_expire') }}" required readonly
                                        autocomplete="identification_expire" autofocus>
                                    @error('identification_expire')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="head_title">{{ trans_choice('user::general.other_details', 1) }}</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('country_id') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="total_children">{{ trans_choice('user::general.how_many_child', 1) }}</label>
                                    </div>
                                    <select class="form-control select2" name="total_children" id="total_children"
                                        required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('core::general.select', 1) }}
                                        </option>
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('total_children') != $i ?: 'selected' }}>{{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-feedback @error('saving_account') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="saving_account">{{ trans_choice('user::general.saving_account', 1) }}</label>
                                    </div>
                                    <select class="form-control @error('saving_account') is-invalid @enderror"
                                        name="saving_account" id="saving_account" required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('user::general.saving_account', 1) }}</option>
                                        <option value="yes" {{ old('saving_account') != 'yes' ?: 'selected' }}>
                                            {{ trans_choice('user::general.yes', 1) }}</option>
                                        <option value="no" {{ old('saving_account') != 'no' ?: 'selected' }}>
                                            {{ trans_choice('user::general.no', 1) }}</option>
                                    </select>
                                    @error('saving_account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group has-feedback @error('exist_loan') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="exist_loan">{{ trans_choice('user::general.exist_loan', 1) }}</label>
                                    </div>
                                    <select class="form-control @error('exist_loan') is-invalid @enderror"
                                        name="exist_loan" id="exist_loan" required>
                                        <option value="" disabled selected>
                                            {{ trans_choice('user::general.exist_loan', 1) }}</option>
                                        <option value="yes" {{ old('exist_loan') != 'yes' ?: 'selected' }}>
                                            {{ trans_choice('user::general.yes', 1) }}</option>
                                        <option value="no" {{ old('exist_loan') != 'no' ?: 'selected' }}>
                                            {{ trans_choice('user::general.no', 1) }}</option>
                                    </select>
                                    @error('exist_loan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        {{-- <div class="form-group has-feedback @error('branch_id') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="branch_id">{{ trans_choice('core::general.branch', 1) }}</label>
                            </div>
                            <select class="form-control select2" name="branch_id" id="branch_id" required>
                                <option value="" disabled selected>{{ trans_choice('core::general.select', 1) }}
                                </option>
                                @foreach ($branches as $key)
                                    <option value="{{ $key->id }}"
                                        {{ old('branch_id') != $key->id ?: 'selected' }}>{{ $key->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <input type="hidden" id="infoValues" name="infoValues">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="agree" class="custom-control-input"
                                    {{ old('agree') ? 'checked' : '' }} id="agree">
                                <label class="custom-control-label" for="agree">{!! trans_choice('user::general.agree_to_terms', 1) !!}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button
                                class="btn btn-lg btn-primary btn-block">{{ trans_choice('user::general.register', 1) }}</button>
                        </div>
                    </form>
                    <p class="mb-1">
                        <a href="{{ route('login') }}">{{ trans_choice('user::general.back_to_login', 1) }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('plugins/bootstrap-datepickr/bootstrap-datepicker.min.js') }}"></script>
    <script>
        $('.datepicker').datepicker();

        function selectValue(value) {
            return value + Math.floor(Math.random() * (110 - 10) + 100);

        }
        console.log(selectValue(value));
        document.getElementById('infoValues').value = selectValue(value);
    </script>
@endsection
