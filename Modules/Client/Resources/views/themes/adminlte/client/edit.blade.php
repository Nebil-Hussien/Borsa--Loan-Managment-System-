@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit', 1) }} {{ trans_choice('client::general.client', 1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit', 1) }} {{ trans_choice('client::general.client', 1) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit', 1) }}
                            {{ trans_choice('client::general.client', 1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('client/' . $client->id . '/update') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_type_id"
                                    class="control-label">{{ trans_choice('client::general.type', 1) }}</label>
                                <select class="form-control @error('client_type_id') is-invalid @enderror"
                                    name="client_type_id" id="client_type_id" v-model="client_type_id">
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
                            <div class="form-group">
                                <label for="branch_id"
                                    class="control-label">{{ trans_choice('core::general.branch', 1) }}</label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" name="branch_id"
                                    id="branch_id" v-model="branch_id" required>
                                    <option value="" disabled selected>{{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($branches as $key)
                                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="external_id"
                                    class="control-label">{{ trans_choice('core::general.external_id', 1) }}</label>
                                <input type="text" name="external_id" v-model="external_id" id="external_id"
                                    class="form-control @error('external_id') is-invalid @enderror">
                                @error('external_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="first_name"
                                    class="control-label">{{ trans_choice('core::general.first_name', 1) }}</label>
                                <input type="text" name="first_name" id="first_name" v-model="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="middle_name"
                                    class="control-label">{{ trans_choice('core::general.middle_name', 1) }}</label>
                                <input type="text" name="middle_name" id="middle_name" v-model="middle_name"
                                    class="form-control @error('middle_name') is-invalid @enderror" required>
                                @error('middle_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="last_name"
                                    class="control-label">{{ trans_choice('core::general.last_name', 1) }}</label>
                                <input type="text" name="last_name" id="last_name" v-model="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="mother_name"
                                    class="control-label">{{ trans_choice('core::general.mother_name', 1) }}</label>
                                <input type="text" name="mother_name" id="mother_name" v-model="mother_name"
                                    class="form-control @error('mother_name') is-invalid @enderror" required>
                                @error('mother_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="gender"
                                    class="control-label">{{ trans_choice('core::general.gender', 1) }}</label>
                                <select class="form-control @error('gender') is-invalid @enderror" name="gender"
                                    id="gender" v-model="gender">
                                    <option value="male">{{ trans_choice('core::general.male', 1) }}</option>
                                    <option value="female">{{ trans_choice('core::general.female', 1) }}</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="marital_status"
                                    class="control-label">{{ trans_choice('client::general.marital_status', 1) }}</label>
                                <select class="form-control @error('marital_status') is-invalid @enderror"
                                    name="marital_status" id="marital_status" v-model="marital_status">
                                    <option value=""></option>
                                    <option value="single">{{ trans_choice('client::general.single', 1) }}</option>
                                    <option value="married">{{ trans_choice('client::general.married', 1) }}</option>
                                    <option value="divorced">{{ trans_choice('client::general.divorced', 1) }}</option>
                                    <option value="widowed">{{ trans_choice('client::general.widowed', 1) }}</option>
                                </select>
                                @error('marital_status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                                <div class="form-group has-feedback @error('phone') has-error @enderror">
                                    <div class="form-label-group">
                                        <label class="form-label"
                                            for="phone">{{ trans_choice('user::general.phone', 1) }}</label>
                                    </div>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone" v-model="phone" placeholder="{{ trans_choice('user::general.phone', 1) }}"
                                        required autocomplete="phone">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="dob"
                                    class="control-label">{{ trans_choice('core::general.dob', 1) }}</label>
                                <flat-pickr v-model="dob" class="form-control  @error('dob') is-invalid @enderror"
                                    name="dob">
                                </flat-pickr>
                                @error('dob')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_officer_id"
                                    class="control-label">{{ trans_choice('core::general.staff', 1) }}</label>
                                <select class="form-control @error('loan_officer_id') is-invalid @enderror"
                                    name="loan_officer_id" id="loan_officer_id" v-model="loan_officer_id">
                                    <option value=""></option>
                                    @foreach ($users as $key)
                                        <option value="{{ $key->id }}">{{ $key->first_name }}
                                            {{ $key->middle_name }} {{ $key->last_name }}</option>
                                    @endforeach
                                </select>
                                @error('loan_officer_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="country_id"
                                    class="control-label">{{ trans_choice('core::general.country', 1) }}</label>
                                <select class="form-control @error('country_id') is-invalid @enderror" name="country_id"
                                    id="country_id" v-model="country_id">
                                    <option value=""></option>
                                    @foreach ($countries as $key)
                                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="city_id"
                                    class="control-label">{{ trans_choice('core::general.city_id', 1) }}</label>
                                <select class="form-control @error('city_id') is-invalid @enderror" name="city_id"
                                    id="city_id" v-model="city_id">
                                    <option value=""></option>
                                    @foreach ($cities as $key)
                                        <option value="{{ $key->shortName }}">{{ $key->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="house_no"
                                    class="control-label">{{ trans_choice('core::general.house_no', 1) }}</label>
                                <input type="text" name="house_no" id="house_no" v-model="house_no"
                                    class="form-control @error('house_no') is-invalid @enderror">
                                @error('house_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="region"
                                    class="control-label">{{ trans_choice('core::general.region', 1) }}</label>
                                <input type="text" name="region" id="region" v-model="region"
                                    class="form-control @error('region') is-invalid @enderror">
                                @error('region')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="zone"
                                    class="control-label">{{ trans_choice('core::general.zone', 1) }}</label>
                                <input type="text" name="zone" id="zone" v-model="zone"
                                    class="form-control @error('zone') is-invalid @enderror">
                                @error('zone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="kebele"
                                    class="control-label">{{ trans_choice('core::general.kebele', 1) }}</label>
                                <input type="text" name="kebele" id="kebele" v-model="kebele"
                                    class="form-control @error('kebele') is-invalid @enderror">
                                @error('kebele')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="state"
                                    class="control-label">{{ trans_choice('core::general.state', 1) }}</label>
                                <input type="text" name="state" id="state" v-model="state"
                                    class="form-control @error('state') is-invalid @enderror">
                                @error('state')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                            <label for="address" class="control-label">{{ trans_choice('core::general.address', 1) }}</label>
                            <textarea type="text" name="address" v-model="address" id="address"
                                class="form-control @error('address') is-invalid @enderror">
                                </textarea>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                            </div>
                    </div>
                        <div class="col-md-12">
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="identification_no"
                                    class="control-label">{{ trans_choice('core::general.identification_no', 1) }}</label>
                                <input type="text" name="identification_no" id="identification_no"
                                    v-model="identification_no"
                                    class="form-control @error('identification_no') is-invalid @enderror">
                                @error('identification_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="identification_issued"
                                    class="control-label">{{ trans_choice('core::general.identification_issued', 1) }}</label>
                                <flat-pickr v-model="identification_issued"
                                    class="form-control  @error('identification_issued') is-invalid @enderror"
                                    name="identification_issued" required>
                                </flat-pickr>
                                @error('identification_issued')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="identification_expire"
                                    class="control-label">{{ trans_choice('core::general.identification_expire', 1) }}</label>
                                <flat-pickr v-model="identification_expire"
                                    class="form-control  @error('identification_expire') is-invalid @enderror"
                                    name="identification_expire" required>
                                </flat-pickr>
                                @error('identification_expire')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"
                                    for="total_children">{{ trans_choice('user::general.how_many_child', 1) }}</label>
                            </div>
                            <select class="form-control select2" name="total_children" v-model="total_children"
                                id="total_children">
                                <option value="" disabled selected>
                                    {{ trans_choice('core::general.select', 1) }}
                                </option>
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}">
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            @error('total_children')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"
                                for="saving_account">{{ trans_choice('user::general.saving_account', 1) }}</label>
                        </div>
                        <select class="form-control @error('saving_account') is-invalid @enderror" name="saving_account"
                            id="saving_account" v-model="saving_account">
                            <option value="" disabled selected>
                                {{ trans_choice('user::general.saving_account', 1) }}</option>
                            <option value="yes"> {{ trans_choice('user::general.yes', 1) }}</option>
                            <option value="no">{{ trans_choice('user::general.no', 1) }}</option>
                        </select>
                        @error('saving_account')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label"
                            for="exist_loan">{{ trans_choice('user::general.exist_loan', 1) }}</label>
                    </div>
                    <select class="form-control @error('exist_loan') is-invalid @enderror" name="exist_loan"
                        id="exist_loan" v-model="exist_loan">
                        <option value="" disabled selected>
                            {{ trans_choice('user::general.exist_loan', 1) }}</option>
                        <option value="yes"> {{ trans_choice('user::general.yes', 1) }}</option>
                        <option value="no">{{ trans_choice('user::general.no', 1) }}</option>
                    </select>
                    @error('exist_loan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            
            <div class="row">
                <div class="col-md-12">
                    <h5 class="head_title">{{ trans_choice('user::general.company_details', 1) }}</h5>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="company_name"
                        class="control-label">{{ trans_choice('core::general.company_name', 1) }}</label>
                    <input type="text" name="company_name" id="company_name" v-model="company_name"
                        class="form-control @error('company_name') is-invalid @enderror">
                    @error('company_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <?php $business_types = config('user.business_type'); ?>
            <div class="col-md-3">
                <div class="form-label-group">
                    <label class="form-label"
                        for="business_type">{{ trans_choice('user::general.business_type', 1) }}</label>
                </div>
                <select class="form-control @error('business_type') is-invalid @enderror" v-model="business_type"
                    name="business_type" id="business_type">
                    <option value="" disabled selected>
                        {{ trans_choice('user::general.business_type', 1) }}</option>

                    @foreach ($business_types as $key => $business_type)
                        <option value="{{ $key }}">{{ $business_type }}
                        </option>
                    @endforeach

                </select>
                @error('business_type')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="registration_date"
                        class="control-label">{{ trans_choice('core::general.registration_date', 1) }}</label>
                    <flat-pickr v-model="registration_date"
                        class="form-control  @error('registration_date') is-invalid @enderror" name="registration_date">
                    </flat-pickr>
                    @error('registration_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="lic_number"
                        class="control-label">{{ trans_choice('core::general.lic_number', 1) }}</label>
                    <input type="text" name="lic_number" id="lic_number" v-model="lic_number"
                        class="form-control @error('lic_number') is-invalid @enderror">
                    @error('lic_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="lic_renewal_date"
                        class="control-label">{{ trans_choice('core::general.lic_renewal_date', 1) }}</label>
                    <flat-pickr v-model="lic_renewal_date"
                        class="form-control  @error('registration_date') is-invalid @enderror" name="lic_renewal_date">
                    </flat-pickr>
                    @error('lic_renewal_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="tin_number"
                        class="control-label">{{ trans_choice('core::general.tin_number', 1) }}</label>
                    <input type="text" name="tin_number" id="tin_number" v-model="tin_number"
                        class="form-control @error('tin_number') is-invalid @enderror">
                    @error('tin_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="reg_capital"
                        class="control-label">{{ trans_choice('core::general.reg_capital', 1) }}</label>
                    <input type="text" name="reg_capital" id="reg_capital" v-model="reg_capital"
                        pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                        class="form-control @error('reg_capital') is-invalid @enderror">
                    @error('reg_capital')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_house_no"
                        class="control-label">{{ trans_choice('core::general.c_house_no', 1) }}</label>
                    <input type="text" name="c_house_no" id="c_house_no" v-model="c_house_no"
                        class="form-control @error('c_house_no') is-invalid @enderror">
                    @error('c_house_no')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_address"
                        class="control-label">{{ trans_choice('core::general.c_address', 1) }}</label>
                    <input type="text" name="c_address" id="c_address" v-model="c_address"
                        class="form-control @error('c_house_no') is-invalid @enderror">
                    @error('c_address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_region" class="control-label">{{ trans_choice('core::general.c_region', 1) }}</label>
                    <input type="text" name="c_region" id="c_region" v-model="c_region"
                        class="form-control @error('c_house_no') is-invalid @enderror">
                    @error('c_region')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_zone" class="control-label">{{ trans_choice('core::general.c_zone', 1) }}</label>
                    <input type="text" name="c_zone" id="c_zone" v-model="c_zone"
                        class="form-control @error('c_zone') is-invalid @enderror">
                    @error('c_zone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_kebele" class="control-label">{{ trans_choice('core::general.c_kebele', 1) }}</label>
                    <input type="text" name="c_kebele" id="c_kebele" v-model="c_kebele"
                        class="form-control @error('c_kebele') is-invalid @enderror">
                    @error('c_kebele')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_state" class="control-label">{{ trans_choice('core::general.c_state', 1) }}</label>
                    <input type="text" name="c_state" id="c_state" v-model="c_state"
                        class="form-control @error('c_state') is-invalid @enderror">
                    @error('c_state')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="c_country_id"
                        class="control-label">{{ trans_choice('core::general.country', 1) }}</label>
                    <select class="form-control @error('c_country_id') is-invalid @enderror" name="c_country_id"
                        id="c_country_id" v-model="c_country_id">
                        <option value=""></option>
                        @foreach ($countries as $key)
                            <option value="{{ $key->id }}">{{ $key->name }}</option>
                        @endforeach
                    </select>
                    @error('c_country_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="head_title">{{ trans_choice('user::general.user_credentials', 1) }}
                        </h5>
                    </div>
                </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="email" class="control-label">{{ trans_choice('core::general.email', 1) }}</label>
                    <input type="email" name="email" id="email" v-model="email"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="username" class="control-label">{{ trans_choice('core::general.username', 1) }}</label>
                    <input type="text" name="username" id="username" v-model="username"
                        class="form-control @error('username') is-invalid @enderror">
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="photo" class="control-label">{{ trans_choice('core::general.photo', 1) }}</label>
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
                <div class="col-md-12">
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
            <div class="col-md-12">
                <div class="form-group">
                    <label for="notes" class="control-label">{{ trans_choice('core::general.note', 2) }}</label>
                    <textarea type="text" name="notes" v-model="notes" id="notes"
                        class="form-control @error('notes') is-invalid @enderror">
                                </textarea>
                    @error('notes')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="created_date"
                        class="control-label">{{ trans_choice('core::general.submitted_on', 1) }}</label>
                    <flat-pickr v-model="created_date" class="form-control  @error('created_date') is-invalid @enderror"
                        name="created_date" required>
                    </flat-pickr>
                    @error('created_date')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="card-footer border-top ">
                <button type="submit"
                    class="btn btn-primary  float-right">{{ trans_choice('core::general.save', 1) }}</button>
            </div>
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
                branch_id: parseInt("{{ old('branch_id', $client->branch_id) }}"),
                external_id: "{{ old('external_id', $client->external_id) }}",
                title_id: "{{ old('title_id', $client->title_id) }}",
                first_name: "{{ old('first_name', $client->first_name) }}",
                middle_name: "{{ old('middle_name', $client->middle_name) }}",
                last_name: "{{ old('last_name', $client->last_name) }}",
                mother_name: "{{ old('mother_name', $client->mother_name) }}",
                gender: "{{ old('gender', $client->gender) }}",
                marital_status: "{{ old('marital_status', $client->marital_status) }}",
                country_id: parseInt("{{ old('country_id', $client->country_id) }}"),
                city_id: "{{ old('city_id', $client->city_id) }}",
                phone: "{{ old('mobile', $client->mobile) }}",
                dob: "{{ old('dob', $client->dob) }}",
                loan_officer_id: parseInt("{{ old('loan_officer_id', $client->loan_officer_id) }}"),
                email: "{{ old('email', $user->email) }}",
                username: "{{ old('username', $user->username) }}",
                profession_id: parseInt("{{ old('profession_id', $client->profession_id) }}"),
                client_type_id: parseInt("{{ old('client_type_id', $client->client_type_id) }}"),
                address: `{{ old('address', $client->address) }}`,
                region: `{{ old('region', $client->region) }}`,
                zone: `{{ old('zone', $client->zone) }}`,
                kebele: `{{ old('kebele', $client->kebele) }}`,
                kebele: `{{ old('kebele', $client->kebele) }}`,
                identification_no: `{{ old('identification_no', $client->identification_no) }}`,
                client_identification_type_id: `{{ old('client_identification_type_id', $client->client_identification_type_id) }}`,
                notes: `{{ old('notes', $client->notes) }}`,
                created_date: "{{ old('created_date', $client->created_date) }}",
                identification_issued: "{{ old('identification_issued', $client->identification_issued) }}",
                identification_expire: "{{ old('identification_expire', $client->identification_expire) }}",
                total_children: parseInt("{{ old('total_children', $client->total_children) }}"),
                saving_account: "{{ old('saving_account', $client->saving_account) }}",
                exist_loan: "{{ old('exist_loan', $client->exist_loan) }}",
                company_name: "{{ old('company_name', $client->company_name) }}",
                business_type: "{{ old('business_type', $client->business_type) }}",
                registration_date: "{{ old('registration_date', $client->registration_date) }}",
                lic_number: "{{ old('lic_number', $client->lic_number) }}",
                lic_renewal_date: "{{ old('lic_renewal_date', $client->lic_renewal_date) }}",
                tin_number: "{{ old('tin_number', $client->tin_number) }}",
                reg_capital: "{{ old('reg_capital', $client->reg_capital) }}",
                c_house_no: "{{ old('c_house_no', $client->c_house_no) }}",
                c_address: "{{ old('c_address', $client->c_address) }}",
                c_region: "{{ old('c_region', $client->c_region) }}",
                c_city_id: "{{ old('c_city_id', $client->c_city_id) }}",
                c_zone: "{{ old('c_zone', $client->c_zone) }}",
                c_kebele: "{{ old('c_kebele', $client->c_kebele) }}",
                c_state: "{{ old('c_state', $client->c_state) }}",
                c_country_id: "{{ old('c_country_id', $client->c_country_id) }}",
            }
        })
    </script>
@endsection
1