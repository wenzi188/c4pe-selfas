@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="row col-md-8">
            <h2>{{__('c4pe.user.dsgvo.title')}}</h2>
            <div>{{__('c4pe.user.dsgvo.explanation')}}</div>
        </div>
    </div>
    <div class="row justify-content-center col-md-8 offset-md-2" style="margin-top:20px">
        <form method="POST" action="{{ route('home.acceptedDSGVO') }}">
            <input type="hidden" name="solspec" value="{{$user->id}}">
                @csrf
            <div class="form-group row">
                <div class="col-md-10 offset-md-2">
                    <div class="form-check">
                        <input onchange="toggleAcceptButton()" class="form-check-input" type="checkbox" name="accDSGVO" id="accDSGVO" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="accDSGVO">
                            <nobr>{{__('c4pe.user.dsgvo.checkbox')}}</nobr>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group  row">
                <div class="col-md-10 offset-md-1">{{__('c4pe.user.dsgvo.explanationLong')}}</div>
            </div>

            <div class="form-group row mb-0">
                <div class="col-md-10 offset-md-3">
                    <button id="acceptButton" disabled type="submit" class="btn btn-primary">
                        {{__('c4pe.user.dsgvo.button')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
<hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('impressum_'.App::getLocale())
        </div>
    </div>


</div>
@endsection

<script>
function toggleAcceptButton() {
    if ($("#acceptButton").prop('disabled'))
        $("#acceptButton").prop('disabled', false);
    else
        $("#acceptButton").prop('disabled', true);
}
</script>
