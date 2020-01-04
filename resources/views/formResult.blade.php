@extends('layouts.frontendApp')

@section('content')
<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header" style="text-align:center">
                        <h1><b>{{__('c4pe.form.result.title')}}</b></h1>
                        <h3>"{{$questionnaire->title}}"</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="offset-md-2 col-md-4">
                                <h4 style="margin-top:20px">{{$selections[0]->title}}:</h4>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size:48px">{{$sel1Sum}}</span>{{__('c4pe.form.result.points')}}
                            </div>
                            <div class="col-md-3">
                                @if ($tendency < 0)
                                    {{__('c4pe.form.result.tendency')}}:
                                    <br><span style="font-size:32px">
                                        @if($tendency == -2)
                                            {{__('c4pe.form.result.strong')}}
                                        @endif
                                        @if($tendency == -1)
                                            {{__('c4pe.form.result.weak')}}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="offset-md-2 col-md-4">
                                <h4 style="margin-top:20px">{{$selections[1]->title}}:</h4>
                            </div>
                            <div class="col-md-2">
                                <span style="font-size:48px">{{$sel2Sum}}</span>{{__('c4pe.form.result.points')}}
                            </div>
                            <div class="col-md-3">
                                @if ($tendency > 0)
                                    {{__('c4pe.form.result.tendency')}}:
                                    <br><span style="font-size:32px">
                                        @if($tendency == 2)
                                            {{__('c4pe.form.result.strong')}}
                                        @endif
                                        @if($tendency == 1)
                                            {{__('c4pe.form.result.weak')}}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="card-footer" style="text-align:center">
                        <a href={{route('home.formList', $orgaId)}} class="btn btn-primary">{{__('c4pe.form.result.newButton')}}</a>
                        <a href="/" class="btn btn-primary">{{__('c4pe.form.result.finishButton')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
