@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan_application_report', 1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('loan::general.loan_application_report', 1) }} </h1>
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
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.loan_application_report', 1) }}
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card">
            <div class="card-header with-border">
                <h6 class="card-title">
                    {{ trans_choice('loan::general.search_start_date', 2) }}
                    @if (!empty($start_date))
                        at: <b> {{ $start_date }}</b>
                    @endif
                </h6>

                <div class="card-tools hidden-print">
                    <div class="dropdown">
                        <a href="#" class="btn btn-info btn-trigger btn-icon dropdown-toggle" data-toggle="dropdown">
                            {{ trans_choice('core::general.action', 2) }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-xs dropdown-menu-right">
                            <a href="{{ url('report/loan/loan_application_report?download=1&type=csv&start_date=' . $start_date . '&end_date=' . $end_date . '&branch_id=' . $branch_id . '&loan_product_id=' . $loan_product_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.csv_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_application_report?download=1&type=excel&start_date=' . $start_date . '&end_date=' . $end_date . '&branch_id=' . $branch_id . '&loan_product_id=' . $loan_product_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_application_report?download=1&type=excel_2007&start_date=' . $start_date . '&end_date=' . $end_date . '&branch_id=' . $branch_id . '&loan_product_id=' . $loan_product_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_2007_format', 1) }}</a>
                            <a href="{{ url('report/loan/loan_application_report?download=1&type=pdf&start_date=' . $start_date . '&end_date=' . $end_date . '&branch_id=' . $branch_id . '&loan_product_id=' . $loan_product_id) }}"
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
                                    <option value="" disabled selected>{{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($branches as $key)
                                        <option value="{{ $key->id }}"
                                            @if ($branch_id == $key->id) selected @endif>{{ $key->name }}</option>
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
                                    <option value="" disabled selected>{{ trans_choice('core::general.select', 1) }}
                                    </option>
                                    @foreach ($loan_products as $key)
                                        <option value="{{ $key->id }}"
                                            @if ($loan_product_id == $key->id) selected @endif>{{ $key->name }} </option>
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
        @if (!empty($start_date))
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
                                <th colspan="2"></th>
                                <th colspan="3">{{ trans_choice('core::general.end_date', 1) }}: {{ $end_date }}
                                </th>
                            </tr>
                            <tr style="background-color: #D1F9FF">

                                <th>{{ trans_choice('loan::general.client_id', 1) }}</th>
                                <th>{{ trans_choice('loan::general.application_id', 1) }}</th>
                                <th>{{ trans_choice('core::general.branch', 1) }}</th>
                                <th>{{ trans_choice('loan::general.product', 1) }}</th>
                                <th>{{ trans_choice('loan::general.fund_name', 1) }}</th>
                                <th>{{ trans_choice('loan::general.client_name', 1) }}</th>
                                <th>{{ trans_choice('loan::general.applied_amount', 1) }}</th>
                                <th>{{ trans_choice('loan::general.created_at', 1) }}</th>
                                <th>{{ trans_choice('loan::general.status', 1) }}</th>
                                <th>{{ trans_choice('loan::general.approved_on_date', 1) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key)
                                <tr>

                                    <td>
                                        {{ $key->client_ID }}
                                    </td>
                                    <td>
                                        {{ $key->id }}
                                    </td>
                                    <td>{{ $key->branches }}</td>
                                    <td>{{ $key->loan_products }}</td>
                                    <td>{{ $key->funds }}</td>
                                    <td>{{ $key->client }} </td>
                                    <td>{{ $key->amount }}</td>
                                    <td>{{ $key->created_at }}</td>
                                    <td>{{ $key->status }}</td>
                                    <td>{{ $key->approved_on_date }}</td>
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
