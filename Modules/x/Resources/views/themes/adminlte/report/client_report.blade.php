@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.client_report', 1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('loan::general.client_report', 1) }} </h1>
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
                        <li class="breadcrumb-item active">{{ trans_choice('loan::general.client_report', 1) }}
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
                            <a href="{{ url('report/loan/client_report?download=1&type=csv&branch_id=' . $branch_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.csv_format', 1) }}</a>
                            <a href="{{ url('report/loan/client_report?download=1&type=excel&branch_id=' . $branch_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_format', 1) }}</a>
                            <a href="{{ url('report/loan/client_report?download=1&type=excel_2007&branch_id=' . $branch_id) }}"
                                class="dropdown-item">{{ trans_choice('core::general.download', 1) }}
                                {{ trans_choice('core::general.excel_2007_format', 1) }}</a>
                            <a href="{{ url('report/loan/client_report?download=1&type=pdf&branch_id=' . $branch_id) }}"
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
        @if (!empty($branches))
            <div class="card box-white">
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr style="background-color: #D1F9FF">

                                <th>{{ trans_choice('loan::general.client_id', 1) }}</th>
                                <th>{{ trans_choice('loan::general.client_name', 1) }}</th>
                                <th>{{ trans_choice('core::general.gender', 1) }}</th>
                                <th>{{ trans_choice('core::general.age', 1) }}</th>
                                <th>{{ trans_choice('core::general.mobile', 1) }}</th>
                                <th>{{ trans_choice('core::general.email', 1) }}</th>
                                <th>{{ trans_choice('core::general.status', 1) }}</th>
                                <th>{{ trans_choice('core::general.created_at', 1) }}</th>
                                <th>{{ trans_choice('core::general.branch', 1) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key)
                                <tr>

                                    <td>
                                        {{ $key->id }}
                                    </td>
                                    <td>
                                        {{ $key->client }}
                                    </td>
                                    <td>{{ $key->gender }}</td>
                                    <td>{{ $key->age }}</td>
                                    <td>{{ $key->mobile }}</td>
                                    <td>{{ $key->email }}</td>
                                    <td>{{ $key->status }} </td>
                                    <td>{{ $key->created_at }}</td>
                                    <td>{{ $key->branches }}</td>
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
