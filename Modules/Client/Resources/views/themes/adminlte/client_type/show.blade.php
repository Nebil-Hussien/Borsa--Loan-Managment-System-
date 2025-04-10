@extends('core::layouts.master')
@section('title')
    {{ $client_type->name }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ $client_type->name }}</h6>
            <h5> This is me </h5>

            <div class="heading-elements">

            </div>
        </div>

        <div class="box-body">


        </div>

    </div>
@endsection
@section('scripts')
@endsection
