@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add', 1) }} {{ trans_choice('loan::general.loan', 1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add', 1) }} {{ trans_choice('loan::general.loan', 1) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add', 1) }}
                            {{ trans_choice('loan::general.loan', 1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/store') }}">
            {{ csrf_field() }}
            <h5>{{ trans_choice('loan::general.loan', 1) }}</h5>
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_type"
                                    class="control-label">{{ trans_choice('client::general.client', 1) }}
                                    {{ trans_choice('core::general.type', 1) }}</label>
                                <select class="form-control @error('client_type') is-invalid @enderror" name="client_type"
                                    id="client_type" v-model="client_type" required>
                                    <option value=""></option>
                                    <option value="client">{{ trans_choice('client::general.client', 1) }}</option>
                                </select>
                                @error('client_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" v-if="client_type=='client'">
                                <label for="client_id"
                                    class="control-label">{{ trans_choice('client::general.client', 1) }}</label>
                                <v-select label="name_id" :options="clients" :reduce="client => client.id"
                                    v-on:input="change_client" v-model="client_id">
                                    <template #search="{attributes, events}">
                                        <input autocomplete="off"
                                            class="vs__search @error('client_id') is-invalid @enderror" v-bind="attributes"
                                            v-bind:required="!client_id" v-on="events" />
                                    </template>
                                </v-select>
                                <input type="hidden" name="client_id" v-model="client_id">
                                @error('client_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="loan_product_id"
                                    class="control-label">{{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.product', 1) }}</label>
                                <v-select label="name" :options="loan_products" :reduce="loan_product => loan_product.id"
                                    v-on:input="change_loan_product" v-model="loan_product_id">
                                    <template #search="{attributes, events}">
                                        <input autocomplete="off"
                                            class="vs__search @error('loan_product_id') is-invalid @enderror"
                                            v-bind="attributes" v-bind:required="!loan_product_id" v-on="events" />
                                    </template>
                                </v-select>
                                <input type="hidden" name="loan_product_id" v-model="loan_product_id">
                                @error('loan_product_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div v-show="loan_product">
                        <h3>{{ trans_choice('loan::general.term', 2) }}</h3>
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="applied_amount"
                                        class="control-label">{{ trans_choice('loan::general.principal', 1) }}</label>
                                    <input type="number" name="applied_amount" id="applied_amount"
                                        class="form-control @error('applied_amount') is-invalid @enderror numeric"
                                        onChange="takeInfo(this->value)" v-model="applied_amount" required>
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
                                <div class="form-group" v-if="loan_product">
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
                        @foreach ($custom_fields as $custom_field)
                            <?php
                            $field = custom_field_build_form_field($custom_field);
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
                                    <td>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==1">{{ trans_choice('loan::general.flat', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==2">{{ trans_choice('loan::general.principal_due_on_installment', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==3">{{ trans_choice('loan::general.principal_interest_due_on_installment', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==4">{{ trans_choice('loan::general.interest_due_on_installment', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==5">{{ trans_choice('loan::general.total_outstanding_loan_principal', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==6">{{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_option_id==7">{{ trans_choice('loan::general.original_loan_principal', 1) }}</span>
                                    </td>
                                    <td>
                                        <span v-if="charge.charge.allow_override=='0'">
                                            <input v-bind:name="'charges['+charge.charge.id+']'" type="hidden"
                                                v-bind:value="charge.charge.amount">
                                            @{{ charge.charge.amount }}
                                        </span>
                                        <span v-if="charge.charge.allow_override=='1'">
                                            <input v-bind:name="'charges['+charge.charge.id+']'" type="number"
                                                class="form-control numeric" v-bind:value="charge.charge.amount" required>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==1">{{ trans_choice('loan::general.disbursement', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==2">{{ trans_choice('loan::general.specified_due_date', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==3">{{ trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==4">{{ trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==5">{{ trans_choice('loan::general.disbursement_paid_with_repayment', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==6">{{ trans_choice('loan::general.loan_rescheduling_fee', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==7">{{ trans_choice('loan::general.overdue_on_loan_maturity', 1) }}</span>
                                        <span
                                            v-if="charge.charge.loan_charge_type_id==8">{{ trans_choice('loan::general.last_installment_fee', 1) }}</span>

                                    </td>
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
                                    name="bussiness_sector_id" id="bussiness_sector_id" required>
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
                                        {{ trans_choice('loan::general.bussiness_sector_merchandise', 1) }}</option>
                                    <option value="5">
                                        {{ trans_choice('loan::general.bussiness_sector_woodwork_metal', 1) }}
                                    </option>
                                    <option value="6">{{ trans_choice('loan::general.bussiness_sector_garment', 1) }}
                                    </option>
                                    <option value="7">
                                        {{ trans_choice('loan::general.bussiness_sector_petty_trade', 1) }}</option>
                                    <option value="8">{{ trans_choice('loan::general.bussiness_sector_other', 1) }}
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
                                <input type="text" name="reason_bussiness" id="reason_bussiness"
                                    class="form-control @error('reason_bussiness') is-invalid @enderror text" required>
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
                                    name="knowledge_id" id="knowledge_id" required>
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
                                <input type="text" name="owners_of_bussiness" id="owners_of_bussiness"
                                    class="form-control @error('owners_of_bussiness') is-invalid @enderror text" required>
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
                                <input type="text" name="bank_name" id="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror text" required>
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
                                <input type="text" name="bank_branch_name" id="bank_branch_name"
                                    class="form-control @error('bank_branch_name') is-invalid @enderror text" required>
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
                                <input type="text" name="bank_saving_account" id="bank_saving_account"
                                    class="form-control @error('bank_saving_account') is-invalid @enderror text" required>
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
                                    name="martial_status" id="martial_status" onChange="spouse_tinNumber(this.value)"
                                    required>
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
                                <input type="text" name="tin_number" id="tin_number"
                                    class="form-control @error('tin_number') is-invalid @enderror text" required>
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
                                    <input type="text" name="tin_number2" id="tin_number2"
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
                                    name="physical_bussiness" id="physical_bussiness" required>
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
                                <select class="form-control @error('loan_before') is-invalid @enderror" name="loan_before"
                                    id="loan_before" onChange="Loan_beforeChange(this.value)" required>
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
                                        <input type="numeric" name="number_rounds_loan" id="number_rounds_loan"
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
                                            name="repayment_history_status" id="repayment_history_status">
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
                                        <input type="text" name="reason_histroy_loan" id="reason_histroy_loan"
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
                                <input type="numeric" name="estimated_market_size" id="estimated_market_size"
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
                                <input type="numeric" name="pricing_strategy" id="pricing_strategy"
                                    class="form-control @error('pricing_strategy') is-invalid @enderror text" required>
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
                                <input type="numeric" name="customer_buying_power" id="customer_buying_power"
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
                                <input type="numeric" name="market_potential" id="market_potential"
                                    class="form-control @error('market_potential') is-invalid @enderror numeric" required>
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
                                <input type="text" name="sales_approach" id="sales_approach"
                                    class="form-control @error('sales_approach') is-invalid @enderror text" required>
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
                                <input type="text" name="expected_risk_of_bussiness" id="expected_risk_of_bussiness"
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
                                <input type="numeric" name="direct_employee" id="direct_employee"
                                    class="form-control @error('direct_employee') is-invalid @enderror numeric" required>
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
                                <input type="numeric" name="indirect_employee" id="indirect_employee"
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
                                    name="supplier_amount" id="supplier_amount" required>
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
                                <input type="numeric" name="own_capital" id="own_capital"
                                    class="form-control @error('own_capital') is-invalid @enderror numeric" required>
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
                                <input type="numeric" name="tila_support_loan" id="tila_support_loan" disabled
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
                                <input type="numeric" name="other_source_of_income" id="other_source_of_income"
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
                                <input type="numeric" name="total_captial" id="total_captial"
                                    class="form-control @error('total_captial') is-invalid @enderror numeric" required>
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
                                <input type="numeric" name="current_asset_amount" id="current_asset_amount"
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
                                    name="fixed_asset_amount" id="fixed_asset_amount" />
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
                                <input type="numeric" name="current_liability_amount" id="current_liability_amount"
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
                                <input type="numeric" name="long_term_liabilit_amount" id="long_term_liabilit_amount"
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
                                <input type="numeric" name="annual_income_bussiness" id="annual_income_bussiness"
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
                                <input type="numeric" name="monthly_income_bussiness" id="monthly_income_bussiness"
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
                                <input type="numeric" name="daily_income_bussiness" id="daily_income_bussiness"
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
                                <input type="numeric" name="annual_expense" id="annual_expense"
                                    class="form-control @error('annual_expense') is-invalid @enderror numeric" required>
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
                                <input type="numeric" name="monthly_expense" id="monthly_expense"
                                    class="form-control @error('monthly_expense') is-invalid @enderror numeric" disabled>
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
                                <input type="numeric" name="daily_expesnse" id="daily_expesnse"
                                    class="form-control @error('daily_expesnse') is-invalid @enderror numeric" disabled>
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
                                <input type="numeric"
                                    class="form-control @error('raw_material_total') is-invalid @enderror number"
                                    name="raw_material_total" id="raw_material_total" />
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
                                <input type="numeric"
                                    class="form-control @error('recuring_costs_total') is-invalid @enderror number"
                                    name="recuring_costs_total" id="recuring_costs_total" />
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
                                <input type="numeric"
                                    class="form-control @error('sales_reveanu_total') is-invalid @enderror number"
                                    name="sales_reveanu_total" id="sales_reveanu_total" />
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
                                    name="book_of_records" id="book_of_records" required>
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
                                <select class="form-control @error('edir_status') is-invalid @enderror" name="edir_status"
                                    id="edir_status" onChange="edir_paymentChange(this.value)" required>
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
                                    <select class="form-control @error('edir_payment_status') is-invalid @enderror"
                                        name="edir_payment_status" id="edir_payment_status">
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
                                    name="equib_status" id="equib_status" onChange="equib_paymentChange(this.value)"
                                    required>
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
                                    <select class="form-control @error('equib_payment_status') is-invalid @enderror"
                                        name="equib_payment_status" id="equib_payment_status">
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
                                <select class="form-control @error('community_role_status') is-invalid @enderror"
                                    name="community_role_status" id="community_role_status" required>
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
                                <select class="form-control @error('utillites_payment_status') is-invalid @enderror"
                                    name="utillites_payment_status" id="utillites_payment_status" required>
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
                                <select class="form-control @error('fines_penalities_status') is-invalid @enderror"
                                    name="fines_penalities_status" id="fines_penalities_status" required>
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
    {{-- <script>
        $(document).on('click', '#add', function() {
            var description = $('#description').text();
            var unit_measure = $('#unit_measure').text();
            var qty = $('#qty').text();
            var amount = $('#amount').text();
            var totalAmount += amount;
            if (description != '' && unit_measure != '' && qty != '' && amount != '') {
                $.ajax({
                    url: "{{ route('loan/store.add_data') }}",
                    method: "POST",
                    data: {
                        description: description,
                        unit_measure: unit_measure,
                        qty: qty,
                        amount: amount,
                    },
                    success: function(data) {
                        $('#message').html(data);
                    }
                });
            } else {
                $('#message').html("<div class='alert alert-danger'>Both Fields are required</div>");
            }
        });
    </script> --}}




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
                client_type: "{{ old('client_type') }}",
                loan_product: "{{ old('loan_product') }}",
                loan_product_id: parseInt("{{ old('loan_product_id') }}"),
                client_id: parseInt("{{ old('client_id') }}"),
                group_id: parseInt("{{ old('group_id') }}"),
                applied_amount: "{{ old('applied_amount') }}",
                loan_term: "{{ old('loan_term') }}",
                repayment_frequency: "{{ old('repayment_frequency') }}",
                repayment_frequency_type: "{{ old('repayment_frequency_type') }}",
                fund_id: parseInt("{{ old('fund_id') }}"),
                interest_rate: "{{ old('interest_rate') }}",
                expected_disbursement_date: "{{ old('expected_disbursement_date', date('Y-m-d')) }}",
                loan_officer_id: parseInt("{{ old('loan_officer_id') }}"),
                expected_first_payment_date: "{{ old('expected_first_payment_date',\Illuminate\Support\Carbon::today()->addMonths(1)->format('Y-m-d')) }}",
                loan_purpose_id: parseInt("{{ old('loan_purpose_id') }}"),
                loan_charges: loan_charges,
                loan_product_charges: [],
                loan_products: loan_products,
                clients: clients,
                funds: funds,
                loan_purposes: loan_purposes,
                users: users,
                selected_charge: "",
                selected_charges: []
            },
            created: function() {},
            methods: {
                add_charge(event) {

                    this.selected_charges.push(this.loan_product_charges[this.selected_charge]);
                    //delete charges[this.selected_charge];
                    this.selected_charge = "";

                },
                remove_charge(event) {
                    var id = event.currentTarget.getAttribute('data-id');
                    this.selected_charges.splice(id, 1);
                    //charges.push(original_charges[id]);
                },
                change_loan_product() {
                    if (this.loan_product_id != "") {
                        this.loan_products.forEach(item => {
                            if (item.id == this.loan_product_id) {
                                this.loan_product = item;
                                this.applied_amount = this.loan_product.default_principal;
                                this.loan_term = this.loan_product.default_loan_term;
                                this.repayment_frequency = this.loan_product.repayment_frequency;
                                this.repayment_frequency_type = this.loan_product.repayment_frequency_type;
                                this.fund_id = this.loan_product.fund_id;
                                this.interest_rate = this.loan_product.default_interest_rate;
                                this.loan_product_charges = this.loan_product.charges;
                            }
                        })
                    }
                },
                change_client() {
                    this.loan_officer_id = "";
                    if (this.client_id != "") {
                        this.clients.forEach(item => {
                            if (item.id == this.client_id) {
                                this.loan_officer_id = item.loan_officer_id;
                            }
                        })
                    }
                }
            }
        });
    </script>
@endsection
