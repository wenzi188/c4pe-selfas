@extends(Auth::user() ? 'layouts.app' : 'layouts.frontendApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
        @include('impressum_'.App::getLocale())
        </div>
    </div>
    <hr>
    <div class="row justify-content-center">

    @guest
        <a href="{{route('home')}}" class="btn btn-sm btn-primary">{{__('c4pe.impress.backButton')}}</a>
    @endguest
    @auth
        <a href="{{route('questionnaire.list')}}" class="btn btn-sm btn-primary">{{__('c4pe.impress.backButton')}}</a>
    @endauth

    </div>
    </div>
</div>
@endsection
