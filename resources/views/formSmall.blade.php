@extends('layouts.frontendAppSmall')

@section('content')
<div class="container">
    <form action="{{route('form.store', [$questionnaire->id, $periodId])}}" method="post" onsubmit="return checkAnswers();">
        @csrf
        <input type="hidden" name="qCount" value="{{count($pairs)}}">
        <input type="hidden" name="token" value="{{$token}}">

        @foreach($pairs as $entry)
            <div class="p-0" id="quest_{{$loop->index}}" style="height:100%; display:{{$loop->index == 0 ? 'block' : 'none'}}">
            <div class="row p-2 justify-content-center" style="background-color:white;margin-left:-15px;border-bottom:1px solid grey">
                <img src="{{asset("images/InterregLogoC4PE.png")}}" style="height:20px">
               <span style="padding-left:10px; font-size:12px; font-weight:bold">Career Guidance</span>
            </div>
            <div class="row p-2 justify-content-center" style="background-color:#626d71; color:white">
                <h3 style="text-align:center">{{$questionnaire->title}}</h3>
            </div>

            <div class="row justify-content-center" style="padding:10px; border-top: 1px solid #2a3132;border-bottom: 0.5px dashed #2a3132;background-color:#ddbc95">
                <i>{{$entry[0][2]}}</i>
            </div>
            <div class="row justify-content-center" style="background-color:#ddbc95">
                <label for="radio_{{$loop->index}}_1"><h4 style="margin:0;padding-top:10px">{{$entry[0][1]}}</h4></label>
            </div>
            <div class="row pb-2 justify-content-center" style="background-color:#ddbc95">
                <input type="radio" id="radio_{{$loop->index}}_1" name="radio_{{$loop->index}}" value="{{$entry[0][0]}}" onclick="toggleColor({{$loop->index}})">
            </div>
            <div class="row p-2 justify-content-start" style="background-color:#cdcdc0;border-top:1px dotted black; border-bottom:1px dotted black"">
                <span style="font-size:10px"><i>{{$questionnaire->question}}</i></span>
            </div>
            <div class="row pt-2 justify-content-center" style="background-color:#ddbc95">
                <input type="radio" id="radio_{{$loop->index}}_2" name="radio_{{$loop->index}}" value="{{$entry[1][0]}}" onclick="toggleColor({{$loop->index}})">
            </div>
            <div class="row justify-content-center" style="background-color:#ddbc95">
                <label for="radio_{{$loop->index}}_2"><h4 style="margin:0;padding-top:10px">{{$entry[1][1]}}</h4></label>
            </div>
            <div class="row  justify-content-center" style="padding:10px; border-top: 0.5px dashed #2a3132;border-bottom: 1px solid #2a3132; background-color:#ddbc95">
                <i>{{$entry[1][2]}}</i>
            </div>
            <div class="row p-2 justify-content-center" >
                @if($loop->index != 0)
                    <button class="btn btn-secondary btn-xs" style="margin-top:6px" onclick="return back({{$loop->index}})">Zurück</button>
                @endif
                <span style="padding:10px">Frage {{$loop->index+1}} von {{$loop->count}}</span>
                @if($loop->index == $loop->count-1)
                        <button class="btn btn-primary btn-xs" type="submit">Absenden</button>
                @endif
                @if($loop->index != $loop->count-1)
                        <a href="{{route('home')}}" class="btn btn-secondary btn-xs align-middle" style="margin-top:6px" >Abbruch</a>
                @endif
            </div>
            </div>
        @endforeach


    </form>
</div>

<script>

    function back(ind) {
        if(ind > 0 ) {
            $("#quest_" + ind).css("display", "none");
            $("#quest_" + (ind - 1)).css("display", "block");
        }
        return false;
    }

    function toggleColor(ind) {
        if(ind+1 < {{count($pairs)}}) {
            $("#quest_" + ind).css("display", "none");
            $("#quest_" + (ind + 1)).css("display", "block");
        }
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

