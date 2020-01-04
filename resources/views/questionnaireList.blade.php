@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            <span style="font-size:1.5rem;margin-right:2rem">{{__('c4pe.quest.list.title')}}</span>
                        </div>
                        <div class="col-md-3" style="text-align:right">
                            <a href="{{route('questionnaire.edit', ['id' => $newQuest->id])}}" style="margin-top:8px" class="btn btn-sm btn-primary">{{__('c4pe.quest.list.newButton')}}</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>{{__('c4pe.quest.list.table.title')}}</th>
                            <th title="{{__('c4pe.quest.list.table.active.info')}}" style="text-align:center">{{__('c4pe.quest.list.table.active')}}</th>
                            <th>{{__('c4pe.quest.list.table.user')}}</th>
                            <th>{{__('c4pe.quest.list.table.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($quests as $quest)
                            <tr>
                                <td>
                                    {{$quest->title}}
                                </td>
                                <td style="text-align:center">
                                    @if(in_array($quest->id , $actQuestionnaireArr))
                                        <img style="vertical-align:top;" title="@lang('c4pe.quest.list.table.active.info')" width="16" height="16" src="{{asset('images/checkbox-checked.png')}}">
                                    @endif
                                    @if(!in_array($quest->id , $actQuestionnaireArr))
                                        <img style="vertical-align:top;" title="@lang('c4pe.quest.list.table.active.info')" width="16" height="16" src="{{asset('images/checkbox-unchecked.png')}}">
                                    @endif
                                </td>
                                <td>
                                    @if($quest->creator)
                                        {{$quest->creator->lastname}}
                                    @endif
                                </td>
                                <td>
                                    <img style="margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.quest.list.action.edit')" id="imgEdit_{{$quest->id}}" width="16" height="16" src="{{asset('images/pencil2.png')}}" onclick="location.href = '{{route('questionnaire.edit', ['id' => $quest->id])}}';">
                                    <img style="margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.quest.list.action.period')" id="imgPeriod_{{$quest->id}}" width="16" height="16" src="{{asset('images/meter.png')}}" onclick="location.href = '{{route('questionnaire.periodList', ['id' => $quest->id])}}';">
                                    <img style="margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.quest.list.action.copy')" id="imgCopy_{{$quest->id}}" width="16" height="16" src="{{asset('images/copy.png')}}" onclick="location.href = '{{route('questionnaire.edit', ['id' => $quest->id])}}?mode=copy';">
                                    @if(!in_array($quest->id, $questionnaireWithAssessments))
                                        <img style="margin-left:10px;margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.quest.list.action.remove')" id="imgRemove_{{$quest->id}}" width="16" height="16" src="{{asset('images/remove2.png')}}" onclick="remove({{$quest->id}});">
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>
    <form id="removeForm" method="post" action="{{route('questionnaire.remove', -1)}}">
        @csrf
    </form>
@endsection

<script>
    function remove(id) {
        var defaultRoute = "{{route('questionnaire.remove', -1)}}";
        var check = confirm("@lang('c4pe.quest.list.action.remove.confirm')");
        if(!check)
            return;
        var route = defaultRoute.replace('-1',id);
        $('#removeForm').attr('action', route);
        $('#removeForm').submit();

    }


</script>