@extends('layouts.app')

@section('content')
    <style>
        .bar {
            fill: steelblue;
        }

        .bar:hover {
            fill: brown;
        }

        .axis--x path {
            display: none;
        }
    </style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-11">
                            {{__('c4pe.statistics.title')}} <span style="font-size:20px;font-weight:bold">{{$questionnaire->title}}</span>
                        </div>
                        <div class="col-md-1">
                            <a href="{{route('questionnaire.downloadCSV', ['qid' => $questionnaire->id, 'pid' => $periodId])}}"><img title="{{__('c4pe.statistics.download.img.title')}}" style="margin-top:6px;margin-right:10px;vertical-align:top;cursor:pointer"  id="imgStats_-1}}" width="16" height="16" src="{{asset('images/file-excel.png')}}"></a>
                        </div>
                    </div>
                    <hr>
                    @foreach ($periods as $period)
                    <div class="row">
                        <div class="col-md-6">
                            {{$period->title}}
                        </div>
                        <div class="col-md-3" style="text-align:right">
                            {{\Carbon\Carbon::create($period->startTimeStamp)->format('d.m.Y H:i')}}&nbsp;&nbsp;&nbsp;&nbsp; ---
                        </div>
                        <div class="col-md-3">
                            {{\Carbon\Carbon::create($period->endTimeStamp)->format('d.m.Y H:i')}}
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h4>{{__('c4pe.statistics.participants')}} {{count($assessments)}}</h4>
                            {{__('c4pe.statistics.participantsNeutral')}} {{$selNeutral}}
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th style="text-align:right">{{__('c4pe.form.result.strong')}}</th>
                                    <th style="text-align:right">{{__('c4pe.form.result.weak')}}</th>
                                    <th style="text-align:right">{{__('c4pe.statistics.sum')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$questionnaire->selections[0]->title}}</td>
                                    <td style="text-align:right">{{$sel1Strong}}</td>
                                    <td style="text-align:right">{{$sel1Weak}}</td>
                                    <td style="text-align:right">{{$sel1Weak+$sel1Strong}}</td>
                                </tr>
                                <tr>
                                    <td>{{$questionnaire->selections[1]->title}}</td>
                                    <td style="text-align:right">{{$sel2Strong}}</td>
                                    <td style="text-align:right">{{$sel2Weak}}</td>
                                    <td style="text-align:right">{{$sel2Weak+$sel2Strong}}</td>
                                </tr>
                                <tr class="table-dark">
                                    <td>{{__('c4pe.statistics.combined')}}</td>
                                    <td style="text-align:right">{{$sel1Strong+$sel2Strong}}</td>
                                    <td style="text-align:right">{{$sel1Weak+$sel2Weak}}</td>
                                    <td style="text-align:right">{{$sel1Strong+$sel2Strong+$sel1Weak+$sel2Weak}}</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        <div class="card">
            <div class="card-header">
                <span style="font-size:20px;font-weight:bold">{{__('c4pe.statistics.graphics.title')}}</span>
            </div>
            <div class="card-body">

                <div id="graphic"></div>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <span style="font-size:20px;font-weight:bold">{{__('c4pe.statistics.savedQuests')}}</span>
            </div>
            <div class="card-body">

            <table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th style="text-align:right" scope="col">#</th>
                    <th scope="col">{{__('c4pe.statistics.table.begin')}}</th>
                    <th scope="col">{{__('c4pe.statistics.table.end')}}</th>
                    <th style="text-align:left" colspan="2" scope="col">{{$questionnaire->selections[0]->title}}</th>
                    <th style="text-align:left" colspan="2" scope="col">{{$questionnaire->selections[1]->title}}</th>
                    <th style="text-align:right" scope="col">Id#/P#</th>
                </tr>
                </thead>
                <tbody>
                @foreach($assessments as $ass)
                    @php
                        $arr = json_decode($ass->results)
                    @endphp
                    <tr>
                        <td style="text-align:right">{{$loop->index+1}}.</td>
                        <td>{{\Carbon\Carbon::create($ass->started)->format('d.m.Y H:i:s')}}</td>
                        <td>
                            @php
                            $d = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ass->updated_at);
                            @endphp
                            {{$d->format('d.m.Y H:i:s')}}
                        </td>
                        <td>{{$arr[0]}}</td>
                        <td>
                            @if($arr[2] == -2)
                                Stark
                            @endif
                            @if($arr[2] == -1)
                                Schwach
                            @endif
                        </td>
                        <td>{{$arr[1]}}</td>
                        <td>
                            @if($arr[2] == 2)
                                Stark
                            @endif
                            @if($arr[2] == 1)
                                Schwach
                            @endif
                        </td>
                        <td style="text-align:right">{{$ass->id}}/{{$ass->period_id}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
    </div>

</div>


@push('scripts')
    <script type="text/javascript" src="{{asset('js/d3.min.js')}}"></script>
@endpush

    <script>
        var data = {!! $graphicData !!} ;

        //sort bars based on value
        data = data.sort(function (a, b) {
            return d3.ascending(a.value, b.value);
        })

        //set up svg using margin conventions - we'll need plenty of room on the left for labels
        var margin = {
            top: 15,
            right: 125,
            bottom: 15,
            left: 160
        };

        var width = 960 - margin.left - margin.right,
            height = 300 - margin.top - margin.bottom;

        var svg = d3.select("#graphic").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var x = d3.scale.linear()
            .range([0, width])
            .domain([0, d3.max(data, function (d) {
                return d.value;
            })]);

        var y = d3.scale.ordinal()
            .rangeRoundBands([height, 0], .2)
            .domain(data.map(function (d) {
                return d.name;
            }));

        //make y axis to show bar names
        var yAxis = d3.svg.axis()
            .scale(y)
            //no tick marks
            .tickSize(0)
            .orient("left");

        var gy = svg.append("g")
            .attr("class", "y axis")
            .call(yAxis)

        var bars = svg.selectAll(".bar")
            .data(data)
            .enter()
            .append("g")

        //append rects
        bars.append("rect")
            .attr("class", "bar")
            .attr("y", function (d) {
                return y(d.name);
            })
            .attr("height", y.rangeBand())
            .attr("x", 0)
            .attr("width", function (d) {
                return x(d.value);
            });

        //add a value label to the right of each bar
        bars.append("text")
            .attr("class", "label")
            //y position of the label is halfway down the bar
            .attr("y", function (d) {
                return y(d.name) + y.rangeBand() / 2 + 4;
            })
            //x position is 3 pixels to the right of the bar
            .attr("x", function (d) {
                return x(d.value) + 3;
            })
            .text(function (d) {
                return d.value;
            });

    </script>
@endsection
