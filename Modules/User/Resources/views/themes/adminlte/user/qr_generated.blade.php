@extends('core::layouts.master')
@section('title')
    {{ $user->first_name }} {{$user->middle_name}} {{ $user->last_name }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
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
                                href="{{ url('user') }}">{{ trans_choice('user::general.user', 2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $user->first_name }} {{ $user->middle_name }}
                            {{ $user->middle_name }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-responsive table-hover">
                            <tr>
                                <td>{{trans('user::general.system_id')}}</td>
                                <td>{{$user->id}}</td>
                            </tr>
                            <tr>
                                <td> {{trans('user::general.username')}}</td>
                                <td> {{$user->username}}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('general.gender') }}</td>
                                <td>
                                    @if ($user->gender == 'male')
                                        {{ trans('core::general.male') }}
                                    @endif
                                    @if ($user->gender == 'female')
                                        {{ trans('core::general.female') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('general.email', 1) }}</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('general.phone') }}</td>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            <td>{{ trans_choice('user::general.city', 1) }}</td>
                            <td>{{ determine_the_city($user->city_id) }}</td>
                            <tr>
                                <td>{{ trans('general.address') }}</td>
                                <td>{!! $user->address !!}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('general.created_at') }}</td>
                                <td>{{ $user->created_at }}</td>
                            </tr>
                            <tr>
                                <td>{{ trans('general.updated_at') }}</td>
                                <td>{{ $user->updated_at }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title">{{ trans_choice('general.note', 2) }}</h3>
                    </div>
                    <div class="card-body">
                        {!! $user->notes !!}
                    </div>
                </div>
            </div>
              <div class="">
                <div class="card">
                    <div class="card-header with-border">
                        <h3 class="card-title" style="font: bold">{{ trans_choice('user::general.qr', 1)}}</h3>
                    </div>
                    <div class="card-body">
                    <img src="data:image/png;base64, {!! 
                base64_encode(
                QrCode::format('png')
                ->merge('/public/uploads/Elebat_Logo.png',.3,false)
                ->size(400)
                ->color(37, 42, 197)
                ->gradient(37, 42, 197, 1 , 2, 5, 'horizontal')
                ->errorCorrection('H')
                ->eyeColor(1,226,167,67,22,33,88)
                ->generate($user->QRpassword) 
                ) !!}">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        $('.data-table').DataTable({
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }]
            },
            responsive: true,
        });
    </script>
@endsection
