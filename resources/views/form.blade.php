@extends('layouts.frontendApp')

@section('content')
<div class="container">
    <form action="{{route('form.store', [$questionnaire->id, $periodId])}}" method="post" onsubmit="return checkAnswers();">
        @csrf
        <input type="hidden" name="qCount" value="{{count($pairs)}}">
        <input type="hidden" name="token" value="{{$token}}">

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header" style="text-align:center">
                        <h3>{{$questionnaire->title}}</h3>
                        <h6>{{$questionnaire->description}}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="offset-md-1 col-md-9" style="text-align:center"><h4 style="color:grey">{{$questionnaire->question}}</h4></div>
                            <div class="col-md-2"><button id="showInfos" class="btn btn-info btn-xs" style="margin-bottom:3px" onclick="return showDetails();return false;">{{__('c4pe.form.deactivateInfos')}}</button></div>
                        </div>

                        @foreach($pairs as $entry)
                        <div class="card" style="margin-bottom:5px">
                            <div class="card-header" style="background-color:#f4aa75" id="head_{{$loop->index}}">
                                <div class="row">
                                    <div class="col-md-5" style="text-align:right">
                                        {{$entry[0][1]}}
                                    </div>
                                    <div class="col-md-1" style="padding-left:0px">
                                        <input type="radio" style="margin-top:5px;" class="" name="radio_{{$loop->index}}" value="{{$entry[0][0]}}" onclick="toggleColor({{$loop->index}})">
                                    </div>
                                    <div class="col-md-1" style="text-align:right; padding-right:0px;padding-left:0px ">
                                        <input type="radio" style="margin-top:5px; margin-left:0px;padding-right:0px" class="" name="radio_{{$loop->index}}" value="{{$entry[1][0]}}" onclick="toggleColor({{$loop->index}})">
                                    </div>
                                    <div style="" class="col-md-5" style="text-align:left">
                                        {{$entry[1][1]}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body questionInfos">
                                <div class="row">
                                    <div class="col-md-5" style="text-align:right">
                                        <i>{{$entry[0][2]}}</i>
                                    </div>
                                    <div class="offset-md-2 col-md-5" style="text-align:left">
                                        <i>{{$entry[1][2]}}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                    <div class="card-footer" style="text-align:center"><button class="btn btn-primary" >{{__('c4pe.form.resultButton')}}</button></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function showDetails() {
        if($(".questionInfos").css('display') == 'block') {
            $(".questionInfos").css('display', 'none');
            $("#showInfos").text("{{__('c4pe.form.activateInfos')}}");
        }
        else {
            $(".questionInfos").css('display', 'block');
            $("#showInfos").text("{{__('c4pe.form.deactivateInfos')}}");
        }
        return false;
    }

    function toggleColor(ind) {
        $("#head_"+ind).css("background-color","#20c901");
    }

    function checkAnswers() {
        var rbs = new Array();
        for(var i = 0; i < {{count($pairs)}}; i++)
            rbs.push(0);
        $("input[type='radio']:checked").each(function( el ) {
            var name = this.name;
            var number = this.name.split('_')[1];
            rbs[number] = 1;
        });
        var error = 0;
        for(var i = 0; i < {{count($pairs)}}; i++)
            if(rbs[i] == 0)
                error++;
        if(error > 0) {
            alert('{{__("c4pe.form.questionsOpen")}}');
            return false;
        }
        return true;
    }

</script>
@endsection
