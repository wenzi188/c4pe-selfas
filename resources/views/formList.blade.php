@extends('layouts.frontendApp')

@section('content')

    <div class="container">
    <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3>{{__('c4pe.form.list.title')}}</h3></div>
                    <div class="card-body">
                        @if($openQuests > 0)
                        <ul style="list-style-type: none;padding-inline-start: 20px;">
                        @foreach ($quests as $quest)
                            @if(count($quest->activePeriods) > 0)
                            <li>
                                <a onclick="makeRef(this)" href="{{route('form.index', [$organisation->id, $quest->id, $quest->activePeriods->first()->toArray()['id']])}}"><span style="font-size:20px">{{$quest->title}}</span></a><br>
                                <p><i>{{$quest->description}}</i></p>
                            </li>
                            @endif
                        @endforeach
                        </ul>
                        @endif
                        @if($openQuests == 0)
                            <span style="font-size:20px;color:red">{{__('c4pe.form.list.info')}}</span><br>
                        @endif
                    </div>
                    <div class="card-footer" style="text-align:center">{{$organisation->info}}</div>
                </div>
            </div>
    </div>
</div>
    <script>
        function makeRef(ele) {
            var v = $(ele).prop('href')+"?viewSize="+viewSize();
            $(ele).prop('href', v);
        }

    </script>
@endsection

