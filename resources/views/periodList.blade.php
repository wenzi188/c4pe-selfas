@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            <span style="font-size:1rem;margin-right:2rem">{{__('c4pe.period.list.title')}}</span><br>
                            <span style="font-size:1.5rem;margin-right:2rem">{{$quest->title}}</span>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-sm btn-primary" onclick="$('#newPeriod').css('display', 'block');$(this).remove();">{{__('c4pe.period.list.newButton')}}</a>
                        </div>
                        <div class="col-md-1">
                            @if(count($pIds) > 0)
                                <a href="{{route('questionnaire.statistics', ['qid' => $quest->id, 'pid' => -1])}}">
                                    <img style="margin-top:6px;margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.period.list.statistics')" id="imgStats_-1}}" width="16" height="16" src="{{asset('images/stats.png')}}">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @foreach($periods as $period)
                    <form method="post" action="{{route('period.store', ['qid' => $quest->id, 'pid' => $period->id])}}">
                        @csrf
                    <div class="card" {{($period->id == -1) ? 'id=newPeriod' : ''}} style="margin-bottom:3px;display:{{($period->id == -1) ? 'none' : 'block'}}">
                        <div class="card-body">
                            @if($period->id == -1)
                                <div class="row">
                                    <div class="col-md-8 form-group">
                                        <b>{{__('c4pe.period.list.newPeriod')}}</b>
                                    </div>
                                </div>
                            @endif
                                @if($period->errorCode != 0)
                                    <div class="row">
                                        <div class="col-md-12 alert-danger" style="margin-bottom:5px;padding-top:10px;text-align:center">
                                            <label >{{__('c4pe.period.list.error1')}}</label>
                                        </div>
                                    </div>
                                @endif


                                <div class="row">
                                <div class="col-md-8 form-group">
                                    <input class="form-control" value="{{$period->title}}" type="text" name="title" value="" maxlength="64" placeholder="{{__('c4pe.period.list.placeholder.title')}}" required>
                                </div>

                                <div class="offset-md-3 col-md-1">
                                    @if(in_array($period->id, $pIds) )
                                        <a href="{{route('questionnaire.statistics', ['qid' => $quest->id, 'pid' => $period->id])}}">
                                            <img style="margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.period.list.statistics')" id="imgStats_{{$period->id}}" width="16" height="16" src="{{asset('images/stats.png')}}">
                                        </a>
                                    @endif
                                    @if(!in_array($period->id, $pIds) )
                                        <img style="margin-left:10px;margin-right:10px;vertical-align:top;cursor:pointer" title="@lang('c4pe.period.list.remove')" id="imgRemove_{{$period->id}}" width="16" height="16" src="{{asset('images/remove2.png')}}" onclick="deletePeriod('{{route('period.delete', ['qid' => $quest->id, 'pid' => $period->id])}}');">
                                    @endif

                                </div>


                            </div>
                            <div class="row" id="hugo">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group date" id="datetimepicker_{{$period->id}}" data-target-input="nearest">
                                            <input required id="dtp_{{$period->id}}" name="start" value="{{$period->startTimeStamp}}" placeholder="{{__('c4pe.period.list.placeholder.startDate')}}" type="text" class="form-control datetimepicker-input" data-target="#datetimepicker_{{$period->id}}"/>
                                            <div class="input-group-append" data-target="#datetimepicker_{{$period->id}}" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input id="dtl_{{$period->id}}" title="{{__('c4pe.period.list.hoursTitle')}}" style="{{($period->length == 0) ? 'color:red' :''}}" placeholder="Dauer" name="length" value="{{$period->length}}" type="number" min="0" max="720" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-md-1" >
                                    <span style="padding-top:10px; float:left">{{__('c4pe.period.list.hours')}}</span>
                                </div>
                                <div class="offset-md-3 col-md-1" >
                                    <button type="submit" class="btn btn-primary" onclick="return checkOverLap(this);">{{__('c4pe.period.list.saveButton')}}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    </form>
                    @endforeach


                </div>

                <div class="card-footer text-muted">
                    <a href="{{route('questionnaire.list')}}" class="btn btn-sm btn-secondary">{{__('c4pe.period.list.backButton')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script type="text/javascript" src="{{asset('js/moment-with-locales.js')}}"></script>
<script type="text/javascript" src="{{asset('js/moment-range.js')}}"></script>
<script type="text/javascript" src="{{asset('js/tempusdominus-bootstrap-4.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/fontawesome/all.min.js')}}"></script>
@endpush

@section('stylesheets')
    <link rel="stylesheet" href="{{asset('css/tempusdominus-bootstrap-4.min.css')}}" />
    <link rel="stylesheet" href="{{asset('css/fontawesome/all.min.css')}}" />
@endsection

<script type="text/javascript">
    window['moment-range'].extendMoment(moment);

    $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'far fa-calendar-check-o',
            clear: 'far fa-trash',
            close: 'far fa-times'
        }

    });

    $(function () {
        @foreach ($periods as $period)
        $('#datetimepicker_{{$period->id}}').datetimepicker({
            locale: "{{ App::getLocale() }}",
            format: "DD.MM.YYYY HH:mm",
            tooltips: {
                today: '{{__('c4pe.datetimepicker.tooltips.today')}}',
                clear: '{{__('c4pe.datetimepicker.tooltips.clear')}}',
                close: '{{__('c4pe.datetimepicker.tooltips.close')}}',
                selectMonth: '{{__('c4pe.datetimepicker.tooltips.selectMonth')}}',
                selectTime: '{{__('c4pe.datetimepicker.tooltips.selectTime')}}',
                prevMonth: '{{__('c4pe.datetimepicker.tooltips.prevMonth')}}',
                nextMonth: '{{__('c4pe.datetimepicker.tooltips.nextMonth')}}',
                selectYear: '{{__('c4pe.datetimepicker.tooltips.selectYear')}}',
                prevYear: '{{__('c4pe.datetimepicker.tooltips.prevYear')}}',
                nextYear: '{{__('c4pe.datetimepicker.tooltips.nextYear')}}',
                selectDecade: '{{__('c4pe.datetimepicker.tooltips.selectDecade')}}',
                prevDecade: '{{__('c4pe.datetimepicker.tooltips.prevDecade')}}',
                nextDecade: '{{__('c4pe.datetimepicker.tooltips.nextDecade')}}',
                prevCentury: '{{__('c4pe.datetimepicker.tooltips.prevCentury')}}',
                nextCentury: '{{__('c4pe.datetimepicker.tooltips.nextCentury')}}',
                incrementHour: '{{__('c4pe.datetimepicker.tooltips.incrementHour')}}',
                pickHour: '{{__('c4pe.datetimepicker.tooltips.pickHour')}}',
                decrementHour:'{{__('c4pe.datetimepicker.tooltips.decrementHour')}}',
                incrementMinute: '{{__('c4pe.datetimepicker.tooltips.incrementMinute')}}',
                pickMinute: '{{__('c4pe.datetimepicker.tooltips.pickMinute')}}',
                decrementMinute:'{{__('c4pe.datetimepicker.tooltips.decrementMinute')}}',
                incrementSecond: '{{__('c4pe.datetimepicker.tooltips.incrementSecond')}}',
                pickSecond: '{{__('c4pe.datetimepicker.tooltips.pickSecond')}}',
                decrementSecond: '{{__('c4pe.datetimepicker.tooltips.decrementSecond')}}'
            },
        });
        @endforeach
    });

    function deletePeriod(route) {
        var res = confirm("{{__('c4pe.period.list.remove.confirm')}}");
        if(res)
            location.href = route;
    }

    function checkOverLap(ele) {
        var root = $(ele).parent().parent();
        var startNode = root.find("input[id^='dtp_']");
        var periodId =  startNode.prop('id').split("_")[1];

        var start = root.find("input[id^='dtp_']").val();
        var duration = root.find("input[id^='dtl_']").val();
        var st = moment(start, "DD.MM.YYYY HH:mm", true);
        var en = moment(st).add(duration, 'hours');
        var range = moment.range(st, en);
        var error = false;
        $(":input[id^='dtp_']").each(function (el) {
            var chkPeriodId =  $(this).prop('id').split("_")[1];
            if(chkPeriodId != periodId && $(this).val() != "") {
                var chkStart = $(this).val();
                var chkDuration = parseInt( $(":input[id='dtl_"+chkPeriodId+"']").val() , 10);
                if(Number.isInteger(chkDuration) && chkDuration >=0) {
                    console.log("OO:" +start+"/"+duration);
                    console.log("PP:" +chkStart+"/"+chkDuration);
                    var chkSt = moment(chkStart, "DD.MM.YYYY HH:mm", true);
                    var chkEn = moment(chkSt).add(chkDuration, 'hours');
                    var chkRange = moment.range(chkSt, chkEn);
                    console.log("R1: "+range.toString());
                    console.log("R2: "+chkRange.toString());
                    var res = range.overlaps(chkRange, { adjacent: false });
                    if(res) {
                        alert("{{__('c4pe.period.list.period.overlap')}}");
                        error = true;
                        return false;
                    }
                }
            }
        });
        return !error;
    }

</script>

@endsection
