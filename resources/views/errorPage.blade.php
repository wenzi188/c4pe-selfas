@extends('layouts.frontendApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3>{{__('c4pe.error.title')}}</h3></div>
                    <div class="card-body alert-danger">
                        <h1>{{$msg}}</h1>
                    </div>

                    <div class="card-footer">
                        @guest
                        <a href="/" class="btn btn-danger">{{__('c4pe.error.backButton')}}</a>
                        @endguest
                        @auth
                        <a href="{{route('questionnaire.list')}}" class="btn btn-danger">{{__('c4pe.error.backButton')}}</a>
                        @endauth
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
