@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan_approved_report', 2) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('loan::general.loan_approved_report', 1) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ url('dashboard') }}">{{ trans_choice('dashboard::general.dashboard', 1) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ url('report') }}">{{ trans_choice('report::general.report', 2) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ url('report/loan') }}">{{ trans_choice('loan::general.loan', 1) }}
                                {{ trans_choice('report::general.report', 2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan_approved_report', 1) }}
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <div class="card-tools hidden-print">
                    <div class="dropdown">
                        <a href="#" class="btn btn-info btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                            {{ trans_choice('core::general.action', 2) }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                            <a href="{{ url('report/loan/loan_approved_report?download=1&type=csv&end_date=' . $end_date . '&branch_id=' . $branch_id . '&city_id=' . $city_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.csv_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_approved_report?download=1&type=excel&end_date=' . $end_date . '&branch_id=' . $branch_id . '&city_id=' . $city_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_approved_report?download=1&type=excel_2007&end_date=' . $end_date . '&branch_id=' . $branch_id . '&city_id=' . $city_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_2007_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_approved_report?download=1&type=pdf&end_date=' . $end_date . '&branch_id=' . $branch_id . '&city_id=' . $city_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.pdf_format', 1) }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="{{ Request::url() }}" class="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                    for="branch_id">{{ trans_choice('core::general.branch', 1) }}</label>
                                <select class="form-control select2" name="branch_id" id="branch_id">
                                    <option value="" disabled selected>
                                        {{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($branches as $key)
                                        <option value="{{ $key->id }}"
                                            @if ($branch_id == $key->id) selected @endif>
                                            {{ $key->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                    for="start_date">{{ trans_choice('core::general.start_date', 1) }}</label>
                                <flat-pickr value="{{ $start_date }}"
                                    class="form-control  @error('start_date') is-invalid @enderror" name="start_date"
                                    id="start_date" required>
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                    for="end_date">{{ trans_choice('core::general.end_date', 1) }}</label>
                                <flat-pickr value="{{ $end_date }}"
                                    class="form-control  @error('end_date') is-invalid @enderror" name="end_date"
                                    id="end_date" required>
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"
                                    for="loan_product_id">{{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.product', 1) }}</label>
                                <select class="form-control select2" name="loan_product_id" id="loan_product_id">
                                    <option value="" disabled selected>
                                        {{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($loan_products as $key)
                                        <option value="{{ $key->id }}"
                                            @if ($loan_product_id == $key->id) selected @endif>
                                            {{ $key->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group has-feedback @error('city_id') has-error @enderror">
                                <div class="form-label-group">
                                    <label class="form-label"
                                        for="city_id">{{ trans_choice('user::general.city', 1) }}</label>
                                </div>
                                <select class="form-control select2" name="city_id" id="city_id" required>
                                    <option value="" disabled selected>
                                        {{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($cities as $key)
                                        <option value="{{ $key->shortName }}"
                                            @if ('city_id' == $key->id) selected @endif> {{ $key->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <span class="input-group-btn">
                                <button type="submit"
                                    class="btn bg-olive btn-flat">{{ trans_choice('core::general.filter', 1) }}
                                </button>
                            </span>
                            <span class="input-group-btn">
                                <a href="{{ Request::url() }}"
                                    class="btn bg-purple  btn-flat pull-right">{{ trans_choice('general.reset', 1) }}!</a>
                            </span>
                        </div>
                    </div>
                </form>

            </div>
            <!-- /.box-body -->

        </div>
        <!-- /.box -->
        @if (!empty($status))
            <div class="card box-white">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    @if (!empty($data->first()) && !empty($branch_id))
                                        {{ trans_choice('core::general.branch', 1) }}:

                                        {{ $data->first()->branches }}
                                    @endif
                                </th>
                                <th colspan="12">

                                </th>
                            </tr>
                            <tr style="background-color: #D1F9FF">
                                <th> {{ trans_choice('core::general.number', 1) }}</th>
                                <th>{{ trans_choice('loan::general.client_id', 1) }}</th>
                                <th>{{ trans_choice('loan::general.client_name', 1) }}</th>
                                <th>{{ trans_choice('loan::general.product', 1) }}</th>
                                <th>{{ trans_choice('core::general.branch', 1) }}</th>
                                <th>{{ trans_choice('loan::general.fund_name', 1) }}</th>
                                <th>{{ trans_choice('loan::general.status', 1) }}</th>
                                <th>{{ trans_choice('loan::general.approved_on_date', 1) }}</th>
                                <th>{{ trans_choice('loan::general.approved_notes', 1) }}</th>
                                <th>{{ trans_choice('loan::general.applied_amount', 1) }}</th>
                                <th>{{ trans_choice('loan::general.approved_amount', 1) }}</th>
                                <th>{{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.purpose', 1) }}</th>
                                <th>{{ trans_choice('loan::general.interest', 1) }}</th>
                                <th>{{ trans_choice('loan::general.loan', 1) }}
                                    {{ trans_choice('loan::general.term', 1) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($data as $key)
                                <tr>
                                    <td> {{ $i++ }}</td>
                                    <td>{{ $key->id }}</td>
                                    <td> {{ $key->client }} </td>
                                    <td>{{ $key->loan_products }}</td>
                                    <td>{{ $key->branches }}</td>
                                    <td>{{ $key->funds }}</td>
                                    <td>{{ $key->status }}</td>
                                    <td>{{ $key->approved_on_date }}</td>
                                    <td>{{ $key->approved_notes }}</td>
                                    <td>{{ $key->applied_amount }}</td>
                                    <td>{{ $key->approved_amount }}</td>
                                    <td>{{ $key->loan_purposes }}</td>
                                    <td>{{ $key->interest_rate }}</td>
                                    <td>{{ $key->loan_term }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {},
            methods: {},
        })
    </script>
@endsection
