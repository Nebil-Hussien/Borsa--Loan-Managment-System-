@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add', 1) }} {{ trans_choice('client::general.client', 1) }}
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-datepickr/bootstrap-datepicker.min.css') }}">
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add', 1) }} {{ trans_choice('client::general.client', 1) }}
                        <a href="#" onclick="window.history.back()"
                            class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back', 1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ url('dashboard') }}">{{ trans_choice('dashboard::general.dashboard', 1) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ url('client') }}">{{ trans_choice('client::general.client', 2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add', 1) }}
                            {{ trans_choice('client::general.client', 1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('client/store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="client_type_id"
                                class="control-label">{{ trans_choice('client::general.type', 1) }}</label>
                            <select class="form-control @error('client_type_id') is-invalid @enderror" name="client_type_id"
                                id="client_type_id" v-model="client_type_id">
                                <option value=""></option>
                                @foreach ($client_types as $key)
                                    <option value="{{ $key->id }}">{{ $key->name }}</option>
                                @endforeach
                            </select>
                            @error('client_type_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group has-feedback @error('branch_id') has-error @enderror">
                            <label for="branch_id">{{ trans_choice('core::general.branch', 1) }}</label>
                            <select class="form-control select2" name="branch_id" id="branch_id" required>
                                <option value="" disabled selected>{{ trans_choice('core::general.select', 1) }}
                                </option>
                                @foreach ($branches as $key)
                                    <option value="{{ $key->id }}" {{ old('branch_id') != $key->id ?: 'selected' }}>
                                        {{ $key->name }}</option>
                                @endforeach
                            </select>
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
                                placeholder="{{ trans_choice('user::general.first_name', 1) }}" required
                                autocomplete="first_name" autofocus>
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
                                placeholder="{{ trans_choice('user::general.middle_name', 1) }}" required
                                autocomplete="middle_name" autofocus>
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
                                placeholder="{{ trans_choice('user::general.last_name', 1) }}" required
                                autocomplete="last_name">
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
                                placeholder="{{ trans_choice('user::general.mother_name', 1) }}" required
                                autocomplete="mother_name">
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
                                name="phone" id="phone" placeholder="{{ trans_choice('user::general.phone', 1) }}"
                                required autocomplete="phone">
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
                                    for="dob">{{ trans_choice('user::general.dob', 1) }}</label>
                            </div>
                            <input type="date" class="datepicker form-control  @error('dob') is-invalid @enderror"
                                name="dob" id="dob"
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}" autocomplete="dob"
                                autofocus>
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
                                placeholder="{{ trans_choice('user::general.house_no', 1) }}" required
                                autocomplete="house_no" autofocus>
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
                                placeholder="{{ trans_choice('user::general.address', 1) }}" required
                                autocomplete="address" autofocus>
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
                                name="address" id="region"
                                placeholder="{{ trans_choice('user::general.region', 1) }}" required
                                autocomplete="region" autofocus>
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
                                    <option value="{{ $key->shortName }}" {{ old('city_id') != $key->id ?: 'selected' }}>
                                        {{ $key->name }}
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
                                name="zone" id="zone" placeholder="{{ trans_choice('user::general.zone', 1) }}"
                                autocomplete="zone" autofocus>
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
                                placeholder="{{ trans_choice('user::general.kebele', 1) }}" required
                                autocomplete="kebele" autofocus>
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
                                name="state" id="state" placeholder="{{ trans_choice('user::general.state', 1) }}"
                                autocomplete="state" autofocus>
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
                                        {{ old('country_id') != $key->id ?: 'selected' }}>
                                        {{ $key->name }}
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
                        <div class="form-group has-feedback @error('client_identification_type_id') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="client_identification_type_id">{{ trans_choice('user::general.identification_type', 1) }}</label>
                            </div>
                            <select class="form-control @error('client_identification_type_id') is-invalid @enderror"
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
                            <input type="text" class="form-control @error('identification_no') is-invalid @enderror"
                                name="identification_no" id="identification_no"
                                placeholder="{{ trans_choice('user::general.identification_no', 1) }}" required
                                autocomplete="identification_no" autofocus>
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
                            <input type="date"
                                class="datepicker form-control  @error('identification_issued') is-invalid @enderror"
                                name="identification_issued" id="identification_issued"
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}" required
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
                            <input type="date"
                                class="datepicker form-control @error('identification_expire') is-invalid @enderror"
                                name="identification_expire" id="identification_expire"
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}" required
                                autocomplete="identification_expire" autofocus>
                            @error('identification_expire')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div id="other" class="row">
                    <div class="col-md-12">
                        <h5 class="head_title">{{ trans_choice('user::general.other_details', 1) }}
                            {{ trans_choice('user::general.only_individual', 1) }}</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('total_children') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="total_children">{{ trans_choice('user::general.how_many_child', 1) }}</label>
                            </div>
                            <select class="form-control select2" name="total_children" id="total_children">
                                <option value="" selected>
                                    {{ trans_choice('core::general.select', 1) }}
                                </option>
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('total_children') != $i ?: 'selected' }}>
                                        {{ $i }}
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
                                name="saving_account" id="saving_account">
                                <option value="" selected>
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
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('exist_loan') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="exist_loan">{{ trans_choice('user::general.exist_loan', 1) }}</label>
                            </div>
                            <select class="form-control @error('exist_loan') is-invalid @enderror" name="exist_loan"
                                id="exist_loan">
                                <option value="" selected>
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
                                name="email" placeholder="{{ trans_choice('user::general.email', 1) }}" required
                                autocomplete="email" id="email" autofocus>
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
                                name="username" placeholder="{{ trans_choice('user::general.username', 1) }}" required
                                id="username" autofocus>
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
                                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                                    data-target="password">
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
                                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch"
                                    data-target="password_confirmation">
                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                </a>
                                <input type="password" name="password_confirmation"
                                    class="form-control  @error('password_confirmation') is-invalid @enderror"
                                    placeholder="{{ trans_choice('user::general.password_confirmation', 1) }}" required
                                    id="password_confirmation">
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div id="company_section" class="row">
                    <div class="col-md-12">
                        <h5 class="head_title">{{ trans_choice('user::general.company_details', 1) }}</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('company_name') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="company_name">{{ trans_choice('user::general.company_name', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                name="company_name" id="company_name"
                                placeholder="{{ trans_choice('user::general.company_name', 1) }}"
                                autocomplete="company_name" autofocus>
                            @error('company_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <?php $business_types = config('user.business_type'); ?>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('business_type') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="business_type">{{ trans_choice('user::general.business_type', 1) }}</label>
                            </div>
                            <select class="form-control @error('business_type') is-invalid @enderror"
                                name="business_type" id="business_type">
                                <option value="" disabled selected>
                                    {{ trans_choice('user::general.business_type', 1) }}</option>

                                @foreach ($business_types as $key => $business_type)
                                    <option value="{{ $key }}"
                                        {{ old('business_type') != $key ?: 'selected' }}>
                                        {{ $business_type }}
                                    </option>
                                @endforeach

                            </select>
                            @error('business_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('registration_date') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="registration_date">{{ trans_choice('user::general.registration_date', 1) }}</label>
                            </div>
                            <input type="date"
                                class="datepicker form-control @error('registration_date') is-invalid @enderror"
                                name="registration_date" id="registration_date"
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}"
                                autocomplete="registration_date" autofocus>
                            @error('registration_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('lic_number') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="lic_number">{{ trans_choice('user::general.lic_number', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('lic_number') is-invalid @enderror"
                                name="lic_number" id="lic_number"
                                placeholder="{{ trans_choice('user::general.lic_number', 1) }}"
                                autocomplete="lic_number" autofocus>
                            @error('lic_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('lic_renewal_date') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="lic_renewal_date">{{ trans_choice('user::general.lic_renewal_date', 1) }}</label>
                            </div>
                            <input type="date"
                                class="datepicker form-control @error('lic_renewal_date') is-invalid @enderror"
                                name="lic_renewal_date" id="lic_renewal_date"
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}"
                                autocomplete="lic_renewal_date" autofocus>
                            @error('lic_renewal_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('tin_number') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="tin_number">{{ trans_choice('user::general.tin_number', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('tin_number') is-invalid @enderror"
                                name="tin_number" id="tin_number"
                                placeholder="{{ trans_choice('user::general.tin_number', 1) }}"
                                autocomplete="tin_number" autofocus>
                            @error('tin_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('reg_capital') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="reg_capital">{{ trans_choice('user::general.reg_capital', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('reg_capital') is-invalid @enderror"
                                name="reg_capital" id="reg_capital" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                placeholder="{{ trans_choice('user::general.reg_capital', 1) }}"
                                autocomplete="reg_capital" autofocus>
                            @error('reg_capital')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <fieldset id="company_section" class="row">
                    <div class="col-md-12">
                        <h5 class="head_title">{{ trans_choice('user::general.c_address_details', 1) }}</h5>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group has-feedback @error('c_house_no') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_house_no">{{ trans_choice('user::general.c_house_no', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_house_no') is-invalid @enderror"
                                name="c_house_no" id="c_house_no"
                                placeholder="{{ trans_choice('user::general.c_house_no', 1) }}"
                                autocomplete="c_house_no" autofocus>
                            @error('c_house_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-feedback @error('c_address') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_address">{{ trans_choice('user::general.c_address', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_address') is-invalid @enderror"
                                name="c_address" id="c_address"
                                placeholder="{{ trans_choice('user::general.c_address', 1) }}"
                                autocomplete="c_address" autofocus>
                            @error('c_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_region') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_region">{{ trans_choice('user::general.c_region', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_region') is-invalid @enderror"
                                name="c_region" id="c_region"
                                placeholder="{{ trans_choice('user::general.c_region', 1) }}"
                                autocomplete="c_region" autofocus>
                            @error('c_region')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_city_id') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_city_id">{{ trans_choice('user::general.city', 1) }}</label>
                            </div>
                            <select class="form-control select2" name="c_city_id" id="c_city_id">
                                <option value="" disabled selected>
                                    {{ trans_choice('core::general.select', 1) }}
                                </option>
                                @foreach ($cities as $key)
                                    <option value="{{ $key->shortName }}"
                                        {{ old('c_city_id') != $key->id ?: 'selected' }}>{{ $key->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_zone') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_zone">{{ trans_choice('user::general.c_zone', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_zone') is-invalid @enderror"
                                name="c_zone" id="c_zone"
                                placeholder="{{ trans_choice('user::general.c_zone', 1) }}" autocomplete="c_zone"
                                autofocus>
                            @error('c_zone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_kebele') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_kebele">{{ trans_choice('user::general.c_kebele', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_kebele') is-invalid @enderror"
                                name="c_kebele" id="c_kebele"
                                placeholder="{{ trans_choice('user::general.c_kebele', 1) }}"
                                autocomplete="c_kebele" autofocus>
                            @error('c_kebele')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_state') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_state">{{ trans_choice('user::general.c_state', 1) }}</label>
                            </div>
                            <input type="text" class="form-control @error('c_state') is-invalid @enderror"
                                name="c_state" id="c_state"
                                placeholder="{{ trans_choice('user::general.c_state', 1) }}" autocomplete="c_state"
                                autofocus>
                            @error('c_state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group has-feedback @error('c_country_id') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="c_country_id">{{ trans_choice('core::general.country', 1) }}</label>
                            </div>
                            <select class="form-control select2" name="c_country_id" id="c_country_id">
                                <option value="" disabled selected>
                                    {{ trans_choice('core::general.select', 1) }}
                                </option>
                                @foreach ($countries as $key)
                                    <option value="{{ $key->id }}"
                                        {{ old('c_country_id') != $key->id ?: 'selected' }}>{{ $key->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="photo"
                                class="control-label">{{ trans_choice('core::general.photo', 1) }}</label>
                            <input type="file" name="photo" id="photo"
                                class="form-control @error('photo') is-invalid @enderror">
                            @error('photo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    @foreach ($custom_fields as $custom_field)
                        <?php
                        $field = custom_field_build_form_field($custom_field);
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                @if ($custom_field->type == 'radio')
                                    <label class="control-label"
                                        for="field_{{ $custom_field->id }}">{{ $field['label'] }}</label>
                                    {!! $field['html'] !!}
                                @else
                                    <label class="control-label"
                                        for="field_{{ $custom_field->id }}">{{ $field['label'] }}</label>
                                    {!! $field['html'] !!}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-12">
                        <div class="form-group has-feedback @error('created_date') has-error @enderror">
                            <div class="form-label-group">
                                <label class="form-label"
                                    for="created_date">{{ trans_choice('core::general.submitted_on', 1) }}</label>
                            </div>
                            <input type="date"
                                class="datepicker form-control @error('created_date') is-invalid @enderror"
                                name="created_date" id="created_date" value="<?php echo date('Y-m-d'); ?>" 
                                placeholder="{{ trans_choice('user::general.date_format', 1) }}"
                                autocomplete="created_date" autofocus>
                            @error('created_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

            </div>

            <div class="card-footer border-top ">
                <button type="submit"
                    class="btn btn-primary  float-right">{{ trans_choice('core::general.save', 1) }}</button>
            </div>
            </div><!-- .card-preview -->
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                branch_id: parseInt("{{ old('branch_id') }}"),
                external_id: "{{ old('external_id') }}",
                title_id: "{{ old('title_id') }}",
                first_name: "{{ old('first_name') }}",
                middle_name: "{{ old('first_name') }}",
                last_name: "{{ old('last_name') }}",
                gender: "{{ old('gender') }}",
                marital_status: "{{ old('marital_status') }}",
                country_id: parseInt("{{ old('country_id') }}"),
                mobile: "{{ old('mobile') }}",
                dob: "{{ old('dob') }}",
                loan_officer_id: parseInt("{{ old('loan_officer_id') }}"),
                email: "{{ old('email') }}",
                profession_id: parseInt("{{ old('profession_id') }}"),
                client_type_id: parseInt("{{ old('client_type_id') }}"),
                active: "{{ old('active', 1) }}",
                address: `{{ old('address') }}`,
                notes: `{{ old('notes') }}`,
                created_date: "{{ old('created_date', date('Y-m-d')) }}",
            }
        });
    </script>
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
