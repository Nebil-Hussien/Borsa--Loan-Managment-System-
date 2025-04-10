@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit', 1) }} {{ trans_choice('loan::general.loan', 1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit', 1) }} {{ trans_choice('loan::general.loan', 1) }}
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
                                href="{{ url('loan') }}">{{ trans_choice('loan::general.loan', 2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit', 1) }}
                            {{ trans_choice('loan::general.loan', 1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/' . $loan->id . '/update') }}">
            {{ csrf_field() }}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <input type="hidden" name="loan_product_id" value="{{ $loan_product->id }}" />
                    <input type="hidden" name="client_id" value="{{ $client->id }}" />
                    <div v-if="stage==1">
                        <h3>{{ trans_choice('loan::general.term', 2) }}</h3>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="applied_amount"
                                        class="control-label">{{ trans_choice('loan::general.principal', 1) }}</label>
                                    <input type="number" name="applied_amount" id="applied_amount"
                                        pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                        class="form-control @error('applied_amount') is-invalid @enderror numeric"
                                        v-model="applied_amount" required>
                                    @error('applied_amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fund_id"
                                        class="control-label">{{ trans_choice('loan::general.fund', 1) }}</label>
                                    <v-select label="name" :options="funds" :reduce="fund => fund.id"
                                        v-model="fund_id">
                                        <template #search="{attributes, events}">
                                            <input autocomplete="off"
                                                class="vs__search @error('fund_id') is-invalid @enderror"
                                                v-bind="attributes" v-bind:required="!fund_id" v-on="events" />
                                        </template>
                                    </v-select>
                                    <input type="hidden" name="fund_id" v-model="fund_id">
                                    @error('fund_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_term"
                                        class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                        {{ trans_choice('loan::general.term', 1) }}</label>
                                    <input type="text" name="loan_term" id="loan_term"
                                        class="form-control @error('loan_term') is-invalid @enderror numeric"
                                        v-model="loan_term" required>
                                    @error('loan_term')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="repayment_frequency"
                                        class="control-label">{{ trans_choice('loan::general.repayment', 1) }}
                                        {{ trans_choice('loan::general.frequency', 1) }}</label>
                                    <input type="text" name="repayment_frequency" id="repayment_frequency"
                                        v-model="repayment_frequency"
                                        class="form-control @error('repayment_frequency') is-invalid @enderror numeric"
                                        required>
                                    @error('repayment_frequency')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="repayment_frequency_type"
                                        class="control-label">{{ trans_choice('core::general.type', 1) }}</label>
                                    <select class="form-control  @error('repayment_frequency_type') is-invalid @enderror"
                                        name="repayment_frequency_type" v-model="repayment_frequency_type"
                                        id="repayment_frequency_type" required>
                                        <option value=""></option>
                                        <option value="days">{{ trans_choice('loan::general.day', 2) }}</option>
                                        <option value="weeks">{{ trans_choice('loan::general.week', 2) }}</option>
                                        <option value="months">{{ trans_choice('loan::general.month', 2) }}</option>
                                    </select>
                                    @error('repayment_frequency_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="interest_rate" class="control-label">
                                        {{ trans_choice('loan::general.interest', 1) }}
                                        {{ trans_choice('loan::general.rate', 1) }}
                                        <span v-if="loan_product.interest_rate_type=='month'">
                                            (% {{ trans_choice('loan::general.per', 1) }}
                                            {{ trans_choice('loan::general.month', 1) }})
                                        </span>

                                        <span v-if="loan_product.interest_rate_type=='year'">
                                            (% {{ trans_choice('loan::general.per', 1) }}
                                            {{ trans_choice('loan::general.year', 1) }}
                                            )
                                        </span>
                                    </label>
                                    <input type="text" name="interest_rate" id="interest_rate" v-model="interest_rate"
                                        class="form-control @error('interest_rate') is-invalid @enderror text" required>
                                    @error('interest_rate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expected_disbursement_date"
                                        class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                                        {{ trans_choice('loan::general.disbursement', 1) }}
                                        {{ trans_choice('core::general.date', 1) }}</label>
                                    <flat-pickr v-model="expected_disbursement_date"
                                        class="form-control  @error('expected_disbursement_date') is-invalid @enderror"
                                        name="expected_disbursement_date" id="expected_disbursement_date" required>
                                    </flat-pickr>
                                    @error('expected_disbursement_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div v-if="stage==1">
                            <h3>{{ trans_choice('core::general.setting', 2) }}</h3>
                            <div class="row gy-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="loan_officer_id"
                                            class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                            {{ trans_choice('loan::general.officer', 1) }}</label>
                                        <v-select label="full_name" :options="users" :reduce="user => user.id"
                                            v-model="loan_officer_id">
                                            <template #search="{attributes, events}">
                                                <input autocomplete="off"
                                                    class="vs__search @error('loan_officer_id') is-invalid @enderror"
                                                    v-bind="attributes" v-bind:required="!loan_officer_id" v-on="events" />
                                            </template>
                                        </v-select>
                                        <input type="hidden" name="loan_officer_id" v-model="loan_officer_id">
                                        @error('loan_officer_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="loan_purpose_id"
                                            class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                            {{ trans_choice('loan::general.purpose', 1) }}</label>
                                        <v-select label="name" :options="loan_purposes"
                                            :reduce="loan_purpose => loan_purpose.id" v-model="loan_purpose_id">
                                            <template #search="{attributes, events}">
                                                <input autocomplete="off"
                                                    class="vs__search @error('loan_purpose_id') is-invalid @enderror"
                                                    v-bind="attributes" v-bind:required="!loan_purpose_id" v-on="events" />
                                            </template>
                                        </v-select>
                                        <input type="hidden" name="loan_purpose_id" v-model="loan_purpose_id">
                                        @error('loan_purpose_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="expected_first_payment_date"
                                            class="control-label">{{ trans_choice('loan::general.expected', 1) }}
                                            {{ trans_choice('loan::general.first_payment_date', 1) }}</label>
                                        <flat-pickr v-model="expected_first_payment_date"
                                            class="form-control  @error('expected_first_payment_date') is-invalid @enderror"
                                            name="expected_first_payment_date" id="expected_first_payment_date" required>
                                        </flat-pickr>
                                        @error('expected_first_payment_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @foreach ($custom_fields as $custom_field)
                                <?php
                                $field = custom_field_build_form_field($custom_field, $loan->id);
                                ?>
                                <div class="row gy-4">
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
                                </div>
                            @endforeach
                        </div>
                        <div v-if="stage==1">
                            <h3>{{ trans_choice('loan::general.charge', 2) }}</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ trans_choice('core::general.name', 1) }}</th>
                                        <th>{{ trans_choice('core::general.type', 1) }}</th>
                                        <th>{{ trans_choice('core::general.amount', 1) }}</th>
                                        <th>{{ trans_choice('loan::general.collected_on', 1) }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="charges_table_body">
                                    <tr v-for="(charge,index) in selected_charges" v-bind:id="charge.charge.id">
                                        <td>@{{ charge.charge.name }}</td>
                                        <td>@{{ charge.charge.loan_charge_option_id }}</td>
                                        <td>
                                            <span v-if="charge.charge.allow_override=='0'">
                                                <input v-bind:name="'charges['+charge.charge.id+']'" type="hidden"
                                                    v-bind:value="charge.charge.amount">
                                                @{{ charge.charge.amount }}
                                            </span>
                                            <span v-if="charge.charge.allow_override=='1'">
                                                <input v-bind:name="'charges['+charge.charge.id+']'" type="number"
                                                    pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                    class="form-control numeric" v-bind:value="charge.charge.amount"
                                                    required>
                                            </span>
                                        </td>
                                        <td>@{{ charge.charge.loan_charge_type_id }}</td>
                                        <td><i class="fa fa-remove" v-on:click="remove_charge" v-bind:data-id="index"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="loan_charges"
                                            class="control-label">{{ trans_choice('loan::general.charge', 2) }}</label>
                                        <select class="form-control @error('loan_charges') is-invalid @enderror"
                                            name="loan_charges" id="loan_charges" v-model="selected_charge">
                                            <option value=""></option>
                                            <option v-for="(charge,index) in loan_product_charges" v-bind:value="index">
                                                @{{ charge.charge.name }}
                                            </option>
                                        </select>
                                        @error('loan_charges')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="control-label"></label>
                                    <button type="button" v-on:click="add_charge" class="btn btn-info"
                                        style="margin-top:20px">{{ trans_choice('core::general.add', 1) }}
                                        {{ trans_choice('core::general.to', 1) }}
                                        {{ trans_choice('loan::general.product', 1) }}</button>
                                </div>
                            </div>
                        </div>

                        {{-- KYC --}}
                        <h5> {{ trans_choice('loan::general.kyc', 1) }}</h5>
                        <div class="card card-bordered card-preview">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bussiness_sector_id"
                                                class="control-label">{{ trans_choice('loan::general.bussiness_sector', 1) }}</label>
                                            <select class="form-control @error('bussiness_sector_id') is-invalid @enderror"
                                                v-model="bussiness_sector_id" name="bussiness_sector_id"
                                                id="bussiness_sector_id" required>
                                                <option value=""></option>
                                                <option value="1">
                                                    {{ trans_choice('loan::general.bussiness_sector_chicken_farming', 1) }}
                                                </option>
                                                <option value="2">
                                                    {{ trans_choice('loan::general.bussiness_sector_animal_farming', 1) }}
                                                </option>
                                                <option value="3">
                                                    {{ trans_choice('loan::general.bussiness_sector_diary_product', 1) }}
                                                </option>
                                                <option value="4">
                                                    {{ trans_choice('loan::general.bussiness_sector_merchandise', 1) }}
                                                </option>
                                                <option value="5">
                                                    {{ trans_choice('loan::general.bussiness_sector_woodwork_metal', 1) }}
                                                </option>
                                                <option value="6">
                                                    {{ trans_choice('loan::general.bussiness_sector_garment', 1) }}
                                                </option>
                                                <option value="7">
                                                    {{ trans_choice('loan::general.bussiness_sector_petty_trade', 1) }}
                                                </option>
                                                <option value="8">
                                                    {{ trans_choice('loan::general.bussiness_sector_other', 1) }}
                                                </option>
                                            </select>
                                            @error('bussiness_sector_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reason_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.reason_bussiness_sector', 1) }}</label>
                                            <input type="text" v-model="reason_bussiness" name="reason_bussiness"
                                                id="reason_bussiness"
                                                class="form-control @error('reason_bussiness') is-invalid @enderror text"
                                                required>
                                            @error('reason_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Skills --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="knowledge_id"
                                                class="control-label">{{ trans_choice('loan::general.taken_skills', 1) }}</label>
                                            <select class="form-control @error('knowledge_id') is-invalid @enderror"
                                                v-model="knowledge_id" name="knowledge_id" id="knowledge_id" required>
                                                <option value=""></option>
                                                <option value="1">
                                                    {{ trans_choice('loan::general.bussiness_managment', 1) }}
                                                </option>
                                                <option value="2">
                                                    {{ trans_choice('loan::general.financial_services', 1) }}
                                                </option>
                                                <option value="3">
                                                    {{ trans_choice('loan::general.cash_management', 1) }}
                                                </option>
                                                <option value="4">
                                                    {{ trans_choice('loan::general.budegeting', 1) }}</option>
                                                <option value="5">
                                                    {{ trans_choice('loan::general.responsible_borrowing', 1) }}
                                                </option>
                                                <option value="6">{{ trans_choice('loan::general.saving', 1) }}
                                                </option>
                                            </select>
                                            @error('knowledge_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="owners_of_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.ownres_of_bussiness', 1) }}</label>
                                            <input type="text" v-model="owners_of_bussiness" name="owners_of_bussiness"
                                                id="owners_of_bussiness"
                                                class="form-control @error('owners_of_bussiness') is-invalid @enderror text"
                                                required>
                                            @error('owners_of_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_name"
                                                class="control-label">{{ trans_choice('loan::general.bank_name', 1) }}</label>
                                            <input type="text" v-model="bank_name" name="bank_name" id="bank_name"
                                                class="form-control @error('bank_name') is-invalid @enderror text"
                                                required>
                                            @error('bank_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_branch_name"
                                                class="control-label">{{ trans_choice('loan::general.bank_branch_name', 1) }}</label>
                                            <input type="text" v-model="bank_branch_name" name="bank_branch_name"
                                                id="bank_branch_name"
                                                class="form-control @error('bank_branch_name') is-invalid @enderror text"
                                                required>
                                            @error('bank_branch_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_saving_account"
                                                class="control-label">{{ trans_choice('loan::general.bank_account', 1) }}</label>
                                            <input type="text" v-model="bank_saving_account" name="bank_saving_account"
                                                id="bank_saving_account"
                                                class="form-control @error('bank_saving_account') is-invalid @enderror text"
                                                v-model='bank_saving_account' required>
                                            @error('bank_saving_account')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="martial_status"
                                                class="control-label">{{ trans_choice('loan::general.martial_status', 1) }}</label>
                                            <select class="form-control @error('martial_status') is-invalid @enderror"
                                                v-model="martial_status" name="martial_status" id="martial_status"
                                                onChange="spouse_tinNumber(this.value)" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}</option>
                                            </select>
                                            @error('physical_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tin_number"
                                                class="control-label">{{ trans_choice('loan::general.tin_number', 1) }}</label>
                                            <input type="text" v-model="tin_number" name="tin_number" id="tin_number"
                                                class="form-control @error('tin_number') is-invalid @enderror text"
                                                required>
                                            @error('tin_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset id="tin_number2" class="coll-md-6">
                                            <div class="form-group">
                                                <label for="tin_number2"
                                                    class="control-label">{{ trans_choice('loan::general.tin_number2', 1) }}</label>
                                                <input type="text" v-model="tin_number2" name="tin_number2" id="tin_number2"
                                                    class="form-control @error('tin_number2') is-invalid @enderror text">
                                                @error('tin_number2')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="physical_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.physical_bussiness', 1) }}</label>
                                            <select class="form-control @error('physical_bussiness') is-invalid @enderror"
                                                v-model="physical_bussiness" name="physical_bussiness"
                                                id="physical_bussiness" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}</option>
                                            </select>
                                            @error('physical_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="loan_before"
                                                class="control-label">{{ trans_choice('loan::general.loan_before', 1) }}</label>
                                            <select class="form-control @error('loan_before') is-invalid @enderror"
                                                v-model="loan_before" name="loan_before" id="loan_before"
                                                onChange="Loan_beforeChange(this.value)" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}</option>
                                            </select>
                                            @error('loan_before')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row gy-4">
                                        <fieldset id="f2" class="coll-md-6">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="number_rounds_loan"
                                                        class="control-label">{{ trans_choice('loan::general.round_of_loan', 1) }}</label>
                                                    <input type="numeric" v-model="number_rounds_loan"
                                                        name="number_rounds_loan" id="number_rounds_loan"
                                                        class="form-control @error('number_rounds_loan') is-invalid @enderror numeric">
                                                    @error('number_rounds_loan')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="repayment_history_status"
                                                        class="control-label">{{ trans_choice('loan::general.repayment_history_status', 1) }}</label>
                                                    <select
                                                        class="form-control @error('repayment_history_status') is-invalid @enderror"
                                                        v-model="repayment_history_status" name="repayment_history_status"
                                                        id="repayment_history_status">
                                                        <option value=""></option>
                                                        <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                        </option>
                                                        <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                        </option>
                                                    </select>
                                                    @error('repayment_history_status')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="reason_histroy_loan"
                                                        class="control-label">{{ trans_choice('loan::general.reason_history_status', 1) }}</label>
                                                    <input type="text" v-model="reason_histroy_loan"
                                                        name="reason_histroy_loan" id="reason_histroy_loan"
                                                        class="form-control @error('reason_histroy_loan') is-invalid @enderror text">
                                                    @error('reason_histroy_loan')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- KYC END --}}


                        {{-- Bussiness Plan --}}
                        <h5> {{ trans_choice('loan::general.bussiness_plan', 1) }} </h5>
                        <div class="card card-bordered card-preview">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="    _market_size"
                                                class="control-label">{{ trans_choice('loan::general.estimated_market_size', 1) }}</label>
                                            <input type="numeric" v-model="estimated_market_size"
                                                name="estimated_market_size" id="estimated_market_size"
                                                pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('estimated_market_size') is-invalid @enderror numeric"
                                                required>
                                            @error('estimated_market_size')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pricing_strategy"
                                                class="control-label">{{ trans_choice('loan::general.pricing_strategy', 1) }}</label>
                                            <input type="text" v-model="pricing_strategy" name="pricing_strategy"
                                                id="pricing_strategy"
                                                class="form-control @error('pricing_strategy') is-invalid @enderror text"
                                                required>
                                            @error('pricing_strategy')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_buying_power"
                                                class="control-label">{{ trans_choice('loan::general.customer_buying_power', 1) }}</label>
                                            <input type="numeric" v-model="customer_buying_power"
                                                name="customer_buying_power" id="customer_buying_power" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('customer_buying_power') is-invalid @enderror numeric"
                                                required>
                                            @error('customer_buying_power')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="market_potential"
                                                class="control-label">{{ trans_choice('loan::general.market_potential', 1) }}</label>
                                            <input type="numeric" v-model="market_potential" name="market_potential"
                                                id="market_potential" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('market_potential') is-invalid @enderror numeric"
                                                required>
                                            @error('market_potential')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sales_approach"
                                                class="control-label">{{ trans_choice('loan::general.sales_approach', 1) }}</label>
                                            <input type="text" v-model="sales_approach" name="sales_approach"
                                                id="sales_approach"
                                                class="form-control @error('sales_approach') is-invalid @enderror text"
                                                required>
                                            @error('sales_approach')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- Expected Risk of a bussiness --}}

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expected_risk_of_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.expected_risk_of_bussiness', 1) }}</label>
                                            <input type="text" v-model="expected_risk_of_bussiness"
                                                name="expected_risk_of_bussiness" id="expected_risk_of_bussiness"
                                                class="form-control @error('expected_risk_of_bussiness') is-invalid @enderror text"
                                                required>
                                            @error('expected_risk_of_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="direct_employee"
                                                class="control-label">{{ trans_choice('loan::general.direct_employee', 1) }}</label>
                                            <input type="numeric" v-model="direct_employee" name="direct_employee"
                                                id="direct_employee" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('direct_employee') is-invalid @enderror numeric"
                                                required>
                                            @error('direct_employee')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="indirect_employee"
                                                class="control-label">{{ trans_choice('loan::general.indirect_employee', 1) }}</label>
                                            <input type="numeric" v-model="indirect_employee" name="indirect_employee"
                                                id="indirect_employee" 
                                                class="form-control @error('indirect_employee') is-invalid @enderror numeric"
                                                required>
                                            @error('indirect_employee')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="supplier_amount"
                                                class="control-label">{{ trans_choice('loan::general.supplier_amount', 1) }}</label>
                                            <select class="form-control @error('supplier_amount') is-invalid @enderror"
                                                v-model="supplier_amount" name="supplier_amount" id="supplier_amount"
                                                required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}</option>
                                            </select>
                                            @error('supplier_amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bussness Plan End --}}
                        {{-- Source of Captial|Finacing --}}

                        <h5>{{ trans_choice('loan::general.source_of_captial', 1) }}</h5>
                        <div class="card card-bordered card-preview">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <label for="own_capital"
                                                class="control-label">{{ trans_choice('loan::general.own_capital', 1) }}</label>
                                            <input type="numeric" v-model="own_capital" name="own_capital" id="own_capital" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('own_capital') is-invalid @enderror numeric"
                                                required>
                                            @error('own_capital')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <label for="tila_support_loan"
                                                class="control-label">{{ trans_choice('loan::general.tila_support_loan', 1) }}</label>
                                            <input type="numeric" v-model="tila_support_loan" name="tila_support_loan"
                                                id="tila_support_loan" disabled
                                                class="form-control @error('tila_support_loan') is-invalid @enderror numeric"
                                                required>
                                            @error('tila_support_loan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="other_source_of_income"
                                                class="control-label">{{ trans_choice('loan::general.other_source_income', 1) }}</label>
                                            <input type="numeric" v-model="other_source_of_income" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="other_source_of_income" id="other_source_of_income"
                                                class="form-control @error('other_source_of_income') is-invalid @enderror numeric"
                                                required>
                                            @error('other_source_of_income')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="total_captial"
                                                class="control-label">{{ trans_choice('loan::general.total_captial', 1) }}</label>
                                            <input type="numeric" v-model="total_captial" name="total_captial" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                id="total_captial"
                                                class="form-control @error('total_captial') is-invalid @enderror numeric"
                                                required>
                                            @error('total_captial')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{-- End  Source of Captial --}}

                        {{-- Financial Plan --}}
                        <h5> {{ trans_choice('loan::general.financial_plan', 2) }} </h5>
                        <div class="card card-bordered card-preview">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <h3>{{ trans_choice('loan::general.current_asset', 2) }}</h3>
                                        <div class="form-group">
                                            <label for="current_asset_amount"
                                                class="control-label">{{ trans_choice('loan::general.curret_asset_amount', 1) }}</label>
                                            <input type="numeric" v-model="current_asset_amount" name="current_asset_amount" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                id="current_asset_amount"
                                                class="form-control @error('current_asset_amount') is-invalid @enderror numeric"
                                                required>
                                            @error('current_asset_amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--Fixed Asset start-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fixed_asset_amount"
                                                class="control-label">{{ trans_choice('loan::general.fixed_asset_amount', 2) }}</label>
                                            <input type="text"
                                                class="form-control @error('fixed_asset_amount') is-invalid @enderror number"
                                                v-model="fixed_asset_amount" name="fixed_asset_amount"
                                                id="fixed_asset_amount" />
                                            @error('fixed_asset_amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="current_liability_amount"
                                                class="control-label">{{ trans_choice('loan::general.current_liability_amount', 1) }}</label>
                                            <input type="numeric" v-model="current_liability_amount" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="current_liability_amount" id="current_liability_amount"
                                                class="form-control @error('current_liability_amount') is-invalid @enderror numeric"
                                                required>
                                            @error('current_liability_amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="long_term_liabilit_amount"
                                                class="control-label">{{ trans_choice('loan::general.long_term_liability_amount', 1) }}</label>
                                            <input type="numeric" v-model="long_term_liabilit_amount" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="long_term_liabilit_amount" id="long_term_liabilit_amount"
                                                class="form-control @error('long_term_liabilit_amount') is-invalid @enderror numeric"
                                                required>
                                            @error('long_term_liabilit_amount')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="annual_income_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.annualy_income', 1) }}</label>
                                            <input type="numeric" v-model="annual_income_bussiness" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="annual_income_bussiness" id="annual_income_bussiness"
                                                class="form-control @error('annual_income_bussiness') is-invalid @enderror numeric"
                                                required>
                                            @error('annual_income_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- Monthly Income --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monthly_income_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.monthly_income_bussiness', 1) }}</label>
                                            <input type="numeric" v-model="monthly_income_bussiness" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="monthly_income_bussiness" id="monthly_income_bussiness"
                                                class="form-control @error('monthly_income_bussiness') is-invalid @enderror numeric"
                                                disabled>
                                            @error('monthly_income_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Daily Income --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="daily_income_bussiness"
                                                class="control-label">{{ trans_choice('loan::general.daily_income_bussiness', 1) }}</label>
                                            <input type="numeric" v-model="daily_income_bussiness" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                name="daily_income_bussiness" id="daily_income_bussiness"
                                                class="form-control @error('daily_income_bussiness') is-invalid @enderror numeric"
                                                disabled>
                                            @error('daily_income_bussiness')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="annual_expense"
                                                class="control-label">{{ trans_choice('loan::general.annualy_expense', 1) }}</label>
                                            <input type="numeric" v-model="annual_expense" name="annual_expense" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                id="annual_expense"
                                                class="form-control @error('annual_expense') is-invalid @enderror numeric"
                                                required>
                                            @error('annual_expense')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monthly_expense"
                                                class="control-label">{{ trans_choice('loan::general.monthly_expense', 1) }}</label>
                                            <input type="numeric" v-model="monthly_expense" name="monthly_expense" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                id="monthly_expense"
                                                class="form-control @error('monthly_expense') is-invalid @enderror numeric"
                                                disabled>
                                            @error('monthly_expense')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="daily_expesnse"
                                                class="control-label">{{ trans_choice('loan::general.daily_expense', 1) }}</label>
                                            <input type="numeric" v-model="daily_expesnse" name="daily_expesnse" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                id="daily_expesnse"
                                                class="form-control @error('daily_expesnse') is-invalid @enderror numeric"
                                                disabled>
                                            @error('daily_expesnse')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="raw_material_total"
                                                class="control-label">{{ trans_choice('loan::general.raw_material_total', 2) }}</label>
                                            <input type="numeric" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('raw_material_total') is-invalid @enderror number"
                                                v-model="raw_material_total" name="raw_material_total"
                                                id="raw_material_total" />
                                            @error('raw_material_total')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="recuring_costs_total"
                                                class="control-label">{{ trans_choice('loan::general.recuring_costs_total', 2) }}</label>
                                            <input type="numeric" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('recuring_costs_total') is-invalid @enderror number"
                                                v-model="recuring_costs_total" name="recuring_costs_total"
                                                id="recuring_costs_total" />
                                            @error('recuring_costs_total')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sales_reveanu_total"
                                                class="control-label">{{ trans_choice('loan::general.sales_reveanu_total', 2) }}</label>
                                            <input type="numeric" pattern="[0-9]+([\.,][0-9]+)?" step="0.01"
                                                class="form-control @error('sales_reveanu_total') is-invalid @enderror number"
                                                v-model="sales_reveanu_total" name="sales_reveanu_total"
                                                id="sales_reveanu_total" />
                                            @error('sales_reveanu_total')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Others --}}
                        <h5> {{ trans_choice('loan::general.additional_Info', 2) }} </h5>
                        <div class="card card-bordered card-preview">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="book_of_records"
                                                class="control-label">{{ trans_choice('loan::general.book_of_records', 1) }}</label>
                                            <select class="form-control @error('book_of_records') is-invalid @enderror"
                                                v-model="book_of_records" name="book_of_records" id="book_of_records"
                                                required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('book_of_records')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edir_status"
                                                class="control-label">{{ trans_choice('loan::general.edir_status', 1) }}</label>
                                            <select class="form-control @error('edir_status') is-invalid @enderror"
                                                v-model="edir_status" name="edir_status" id="edir_status"
                                                onChange="edir_paymentChange(this.value)" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('edir_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset id="f3">
                                            <div class="form-group">
                                                <label for="edir_payment_status"
                                                    class="control-label">{{ trans_choice('loan::general.edir_paymend_status', 1) }}</label>
                                                <select
                                                    class="form-control @error('edir_payment_status') is-invalid @enderror"
                                                    v-model="edir_payment_status" name="edir_payment_status"
                                                    id="edir_payment_status">
                                                    <option value=""></option>
                                                    <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                    </option>
                                                    <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                    </option>
                                                </select>
                                                @error('edir_payment_status')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="equib_status"
                                                class="control-label">{{ trans_choice('loan::general.equb_status', 1) }}</label>
                                            <select class="form-control @error('equib_status') is-invalid @enderror"
                                                v-model="equib_status" name="equib_status" id="equib_status"
                                                onChange="equib_paymentChange(this.value)" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('equib_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset id="f4">
                                            <div class="form-group">
                                                <label for="equib_payment_status"
                                                    class="control-label">{{ trans_choice('loan::general.equb_paymend_status', 1) }}</label>
                                                <select
                                                    class="form-control @error('equib_payment_status') is-invalid @enderror"
                                                    v-model="equib_payment_status" name="equib_payment_status"
                                                    id="equib_payment_status">
                                                    <option value=""></option>
                                                    <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                    </option>
                                                    <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                    </option>
                                                </select>
                                                @error('equib_payment_status')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="community_role_status"
                                                class="control-label">{{ trans_choice('loan::general.community_role_status', 1) }}</label>
                                            <select
                                                class="form-control @error('community_role_status') is-invalid @enderror"
                                                v-model="community_role_status" name="community_role_status"
                                                id="community_role_status" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('community_role_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="utillites_payment_status"
                                                class="control-label">{{ trans_choice('loan::general.utillity_payment_status', 1) }}</label>
                                            <select
                                                class="form-control @error('utillites_payment_status') is-invalid @enderror"
                                                v-model="utillites_payment_status" name="utillites_payment_status"
                                                id="utillites_payment_status" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('utillites_payment_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fines_penalities_status"
                                                class="control-label">{{ trans_choice('loan::general.fines_penalities_status', 1) }}</label>
                                            <select
                                                class="form-control @error('fines_penalities_status') is-invalid @enderror"
                                                v-model="fines_penalities_status" name="fines_penalities_status" clear
                                                id="fines_penalities_status" required>
                                                <option value=""></option>
                                                <option value="1">{{ trans_choice('loan::general.yes', 1) }}
                                                </option>
                                                <option value="0">{{ trans_choice('loan::general.no', 1) }}
                                                </option>
                                            </select>
                                            @error('fines_penalities_status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End others --}}
                        <div class="card-footer border-top ">
                            <button type="submit"
                                class="btn btn-primary  float-right">{{ trans_choice('core::general.save', 1) }}</button>
                        </div>
        </form>
    </section>
@endsection
@section('scripts')
    <script type="text/javascript">
        function takeInfo(amount) {
            document.getElementById("tila_support_loan").value = amount;
            $('#tila_support_loan').attr('placeholder', amount);
        }

        function spouse_tinNumber(value) {
            if (value == 1) {
                $('fieldset').hide().filter('#tin_number2').show();
            } else if (value == 0) {
                $('fieldset').hide().filter('#tin_number2').hide();
            } else {
                $('fieldset').hide().filter('#tin_number2').hide();
            }
        }


        function Loan_beforeChange(val) {
            if (val == 1) {
                $('fieldset').hide().filter('#f2').show();
            } else if (val == 0) {
                $('fieldset').hide().filter('#f2').hide();
            } else {
                $('fieldset').hide().filter('#f2').hide();
            }

        }

        function edir_paymentChange(val1) {
            if (val1 == 1) {
                $('fieldset').hide().filter('#f3').show();
            } else if (val1 == 0) {
                $('fieldset').hide().filter('#f3').hide();
            } else {

                $('fieldset').hide().filter('#f3').hide();

            }
        }
        $('fieldset').hide().filter('#f3').hide();


        function equib_paymentChange(val2) {

            if (val2 == 1) {
                $('fieldset').hide().filter('#f4').show();
            } else if (va2 == 0) {
                $('fieldset').hide().filter('#f4').hide();
            } else {
                $('fieldset').hide().filter('#f4').hide();

            }
        }
        $('fieldset').hide().filter('#f4').hide();
    </script>
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                stage: 1,
                loan_product: {!! $loan_product !!},
                applied_amount: "{{ old('applied_amount', $loan->applied_amount) }}",
                loan_term: "{{ old('loan_term', $loan->loan_term) }}",
                repayment_frequency: "{{ old('repayment_frequency', $loan->repayment_frequency) }}",
                repayment_frequency_type: "{{ old('repayment_frequency_type', $loan->repayment_frequency_type) }}",
                fund_id: parseInt("{{ old('fund_id', $loan->fund_id) }}"),
                interest_rate: "{{ old('interest_rate', $loan->interest_rate) }}",
                expected_disbursement_date: "{{ old('expected_disbursement_date', $loan->expected_disbursement_date) }}",
                loan_officer_id: "{{ old('loan_officer_id', $loan->loan_officer_id) }}",
                expected_first_payment_date: "{{ old('expected_first_payment_date', $loan->expected_first_payment_date) }}",
                //kyc update
                bussiness_sector_id: "{{ old('bussiness_sector_id', $kyc->bussiness_sector_id) }}",
                knowledge_id: "{{ old('knowledge_id', $kyc->knowledge_id) }}",
                reason_bussiness: "{{ old('reason_bussiness', $kyc->reason_bussiness) }}",
                owners_of_bussiness: "{{ old('owners_of_bussiness', $kyc->owners_of_bussiness) }}",
                bank_name: "{{ old('bank_name', $kyc->bank_name) }}",
                bank_branch_name: "{{ old('bank_branch_name', $kyc->bank_branch_name) }}",
                bank_saving_account: "{{ old('bank_saving_account', $kyc->bank_saving_account) }}",
                martial_status: "{{ old('martial_status', $kyc->martial_status) }}",
                tin_number: "{{ old('tin_number', $kyc->tin_number) }}",
                tin_number2: "{{ old('tin_number2', $kyc->tin_number2) }}",
                physical_bussiness: "{{ old('physical_bussiness', $kyc->physical_bussiness) }}",
                loan_before: "{{ old('loan_before', $kyc->loan_before) }}",
                number_rounds_loan: "{{ old('number_rounds_loan', $kyc->number_rounds_loan) }}",
                reason_histroy_loan: "{{ old('reason_histroy_loan', $kyc->reason_histroy_loan) }}",
                //update bussiness_plan
                estimated_market_size: "{{ old('estimated_market_size', $bussiness_plan->estimated_market_size) }}",
                market_potential: "{{ old('market_potential', $bussiness_plan->market_potential) }}",
                customer_buying_power: "{{ old('customer_buying_power', $bussiness_plan->customer_buying_power) }}",
                pricing_strategy: "{{ old('pricing_strategy', $bussiness_plan->pricing_strategy) }}",
                sales_approach: "{{ old('sales_approach', $bussiness_plan->sales_approach) }}",
                expected_risk_of_bussiness: "{{ old('expected_risk_of_bussiness', $bussiness_plan->expected_risk_of_bussiness) }}",
                direct_employee: "{{ old('direct_employee', $bussiness_plan->direct_employee) }}",
                indirect_employee: "{{ old('indirect_employee', $bussiness_plan->indirect_employee) }}",
                supplier_amount: "{{ old('supplier_amount', $bussiness_plan->supplier_amount) }}",
                //updae financial plan              
                current_asset_amount: "{{ old('current_asset_amount', $financial_plan->current_asset_amount) }}",
                fixed_asset_amount: "{{ old('fixed_asset_amount', $financial_plan->fixed_asset_amount) }}",
                current_liability_amount: "{{ old('current_liability_amount', $financial_plan->current_liability_amount) }}",
                long_term_liabilit_amount: "{{ old('long_term_liabilit_amount', $financial_plan->long_term_liabilit_amount) }}",
                annual_income_bussiness: "{{ old('annual_income_bussiness', $financial_plan->annual_income_bussiness) }}",
                monthly_income_bussiness: "{{ old('monthly_income_bussiness', $financial_plan->monthly_income_bussiness) }}",
                daily_income_bussiness: "{{ old('daily_income_bussiness', $financial_plan->daily_income_bussiness) }}",
                annual_expense: "{{ old('annual_expense', $financial_plan->annual_expense) }}",
                monthly_expense: "{{ old('monthly_expense', $financial_plan->monthly_expense) }}",
                daily_expesnse: "{{ old('daily_expesnse', $financial_plan->daily_expesnse) }}",
                raw_material_total: "{{ old('raw_material_total', $financial_plan->raw_material_total) }}",
                recuring_costs_total: "{{ old('recuring_costs_total', $financial_plan->recuring_costs_total) }}",
                sales_reveanu_total: "{{ old('sales_reveanu_total', $financial_plan->sales_reveanu_total) }}",
                recuring_costs_total: "{{ old('recuring_costs_total', $financial_plan->recuring_costs_total) }}",
                recuring_costs_total: "{{ old('recuring_costs_total', $financial_plan->recuring_costs_total) }}",
                recuring_costs_total: "{{ old('recuring_costs_total', $financial_plan->recuring_costs_total) }}",
                //update source of captial
                own_capital: "{{ old('own_capital', $source_captial->own_capital) }}",
                other_source_of_income: "{{ old('other_source_of_income', $source_captial->other_source_of_income) }}",
                total_captial: "{{ old('total_captial', $source_captial->total_captial) }}",
                //update others               
                book_of_records: "{{ old('book_of_records', $others->book_of_records) }}",
                edir_status: "{{ old('edir_status', $others->edir_status) }}",
                edir_payment_status: "{{ old('edir_payment_status*', $others->edir_payment_status) }}",
                equib_status: "{{ old('equib_status', $others->equib_status) }}",
                equib_payment_status: "{{ old('equib_payment_status', $others->equib_payment_status) }}",
                community_role_status: "{{ old('community_role_status', $others->community_role_status) }}",
                utillites_payment_status: "{{ old('utillites_payment_status', $others->utillites_payment_status) }}",
                fines_penalities_status: "{{ old('fines_penalities_status', $others->fines_penalities_status) }}",
                loan_purpose_id: parseInt("{{ old('loan_purpose_id', $loan->loan_purpose_id) }}"),
                loan_charges: charges,
                funds: funds,
                loan_purposes: loan_purposes,
                selected_charge: "",
                selected_charges: charges_list

            },
            created: function() {
                //this.loan_charges=charges;

            },
            methods: {
                add_charge(event) {
                    if (this.selected_charge != '') {
                        this.selected_charges.push(original_charges[this.selected_charge]);
                        delete charges[this.selected_charge];
                        this.selected_charge = '';
                    } else {
                        alert('Please select a charge')
                    }
                },
                remove_charge(event) {
                    var id = event.currentTarget.getAttribute('data-id');
                    this.selected_charges.splice(id, 1);
                    //charges.push(original_charges[id]);
                },
                onSubmit() {

                }
            }
        });
    </script>
@endsection
