@extends('layouts.app')

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        </div>
    @endif
    <form lang="cs" action="{{route('questionnaire.store', ["id"=>($modeCopy)?-1:$quest->id])}}" method="post" onsubmit="return assembleInputs()">
        @csrf
        <input type="hidden" id="criteriaJson" name="criteriaJson" value="[]">
        <input type="hidden" name="selection_0_id" value="{{count($selectionList) && !$modeCopy == 2 ? $selectionList[0]->id : '-1'}}">
        <input type="hidden" name="selection_1_id" value="{{count($selectionList) && !$modeCopy == 2 ? $selectionList[1]->id : '-1'}}">
    <div class="row">
        <div class="col-md-8">
           <h1>{{__('c4pe.quest.edit.title')}}</h1>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">{{__('c4pe.quest.edit.saveButton')}}</button>
        </div>
        <div class="col-md-1" style="text-align:right">
            <a href="{{route('questionnaire.list')}}" class="btn btn-secondary">{{__('c4pe.quest.edit.backButton')}}</a>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-8">
            @if($allowChanges)
                <input type="text" class="form-control" maxlength="128" value="{{(!$modeCopy)?$quest->title:''}}" id="qtitle" placeholder="{{__('c4pe.quest.edit.placeholder.newQuest')}}" name="qtitle" required >
            @endif
            @if(!$allowChanges)
                <input name="qtitle" type="hidden" value="{{$quest->title}}">
                    <span style="font-size:20px"><u>{{$quest->title}}</u></span>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-8">
            <input type="text" class="form-control form-control-sm" maxlength="128" value="{{$quest->question}}" id="question" placeholder="{{__('c4pe.quest.edit.placeholder.question')}}" name="question">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <textarea class="form-control form-control-sm" maxlength="512" rows="3" id="description" name="description" placeholder="{{__('c4pe.quest.edit.placeholder.info')}}">{{$quest->description}}</textarea>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-11">
            <div class="form-inline">
                <div class="col-md-3">
                    <span style="font-size:20px;"><sub><b>{{__('c4pe.quest.edit.criteria')}}</b></sub></span>
                </div>
                <div class="col-md-2" style="text-align:right">
                    <span style="font-size:20px"><sup><b>{{__('c4pe.quest.edit.products')}}</b></sup></span>
                </div>
                <div class=" col-md-2">
                    <input type="text" maxlength="64" class="form-control" value="{{count($selectionList)==2 ? $selectionList[0]->title: ''}}" id="selection0" placeholder="{{__('c4pe.quest.edit.product.title1')}}" name="selectionList_0" required>
                </div>
                <div class="offset-md-1 col-md-2">
                    <input type="text" maxlength="64" class="form-control" value="{{count($selectionList)==2 ? $selectionList[1]->title: ''}}" id="selection1" placeholder="{{__('c4pe.quest.edit.product.title2')}}" name="selectionList_1" required>
                </div>
                <div class="offset-md-1 col-md-1">
                    <div class="form-group">
                        <a href="" class="btn-secondary"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>
    <hr>
    <div id="criteriaList" style="padding-left:15px">

    </div>
    </div>
    @if($allowChanges)
    <div class="row">
        <div class="col-md-3">
            <button style="margin-left:80px" class="btn btn-primary" onclick="addEmptyCriteria()">{{__('c4pe.quest.edit.addCriteriaButton')}}</button>
        </div>
    </div>
    @endif


    <div id="emptyCriteria" style="display:none;padding-bottom:3px;" class="row" style="margin-left:0px">
        <div class="card col-md-11">
            <div class="card-body" style="padding-bottom:0.25px">
                <div class="form-inline">
                    <div class="form-group col-md-5" style="padding-left:0px">
                        <input style="width:inherit" type="text" maxlength="64" class="form-control" value="" id="title" placeholder="{{__('c4pe.quest.edit.placeholder.newCriteria')}}" name="">
                    </div>
                    <div class="offset-md-1 col-md-2">
                        <select onfocus="rememberValue(this)" class="form-control" id="weight1" onchange="return checkAllowed(this);">
                            @foreach(config('app.allowedWeights') as $weight)
                                <option value="{{$weight}}">{{$weight}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="offset-md-1 col-md-2">
                        <select onfocus="rememberValue(this)" class="form-control" id="weight2" onchange="return checkAllowed(this);">
                            @foreach(config('app.allowedWeights') as $weight)
                                <option value="{{$weight}}">{{$weight}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <div class="form-group">
                            @if($allowChanges)
                                <button id="delCriteria" class="btn btn-sm btn-secondary" onclick="deleteCriteria(this)">{{__('c4pe.quest.edit.placeholder.criteria.delButton')}}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group col-md-6" maxlength="512" style="padding-left:0px;padding-top:0.8rem;">
                        <textarea class="form-control form-control-sm" rows="3" id="info" placeholder="{{__('c4pe.quest.edit.placeholder.criteria.additionalInfo')}}"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@push('scripts')
    <script type="text/javascript" src="{{asset('js/jquery-ui.min.js')}}"></script>
@endpush

<script>
    var criteriaList = JSON.parse('{!!$criteriaList!!}');
    var newEleIndex = -1;
    var oldSelection = -1;

    $(document).ready(function(){
/*        criteriaList.forEach(el => {
            console.log(el.id+" "+el.title+" - "+el.weights[0].weight+"--"+el.weights[1].weight);
        });*/
        criteriaList.forEach(el => {
            $(document).find($('#emptyCriteria')).clone(true).appendTo($(document).find('#criteriaList'));
            $entry = $(document).find('#criteriaList #emptyCriteria').last();
            $entry.prop('id', 'index_'+el.id);
            $entry.css('display', 'block');
            $entry.css('margin-bottom', '3px');
            $entry.find('#title').prop('value', el.title).prop('id', 'title_'+el.id);
            $entry.find('#info').val(el.info).prop('id', 'info_'+el.id);
            $entry.find('#weight1').val(el.weights[0].weight).prop('id', 'weight1_'+el.id);
            $entry.find('#weight2').val(el.weights[1].weight).prop('id', 'weight2_'+el.id);
            $entry.find('#delCriteria').prop('id', 'delCriteria_'+el.id);
            //index++;
        });
        @if($allowChanges)
        addEmptyCriteria();
        @endif
        $("#criteriaList").sortable();

/*        $('input[required]').on('invalid', function() {
            this.setCustomValidity($(this).data("required-message"));
        });*/
    });

    function deleteCriteria(ele) {
        var index = $(ele).prop("id").split("_")[1];
        var check = confirm('{{__("c4pe.quest.edit.criteria.delButton.confirmation")}}');
        if (check)
            $("#criteriaList").find("div[id='index_"+index+"']").remove();
    }


    function addEmptyCriteria() {
        index = newEleIndex;
        $(document).find($('#emptyCriteria')).clone(true).appendTo($(document).find('#criteriaList'));
        $entry = $(document).find('#criteriaList #emptyCriteria').last();
        $entry.prop('id', 'index_'+index);
        $entry.css('display', '');
        $entry.find('#title').prop('id', 'title_'+index);
        $entry.find('#info').prop('id', 'info_'+index);
        $entry.find('#weight1').prop('id', 'weight1_'+index);
        $entry.find('#weight2').prop('id', 'weight2_'+index);
        $entry.find('#delCriteria').prop('id', 'delCriteria_'+index);
        newEleIndex--;
    }

    function rememberValue(ele) {
        oldSelection = $(ele).val();
    }

    function checkAllowed(ele) {
        @if(!$allowChanges)
        $(ele).val(oldSelection);
        alert("{{__('c4pe.quest.edit.change.notAllowed')}}");
        return false;
        @endif
    }

    function assembleInputs() {

        var dataComplete = [];
        var s0id = $("#selection_0_id").val();
        var s1id = $("#selection_1_id").val();

        $("#criteriaList").find("div[id^='index_']").each(function( i ) {
            var data = {};
            var hasWeights = 0;
            var allweights = [];
            data["id"] =  $(this).prop('id').split("_")[1]
            $(this).find(":input").each(function (i) {
                var weights = {};
                var v = $(this).prop('id').split("_");
                if(v[0] == "weight1" || v[0] == "weight2") {
                    hasWeights++;
                    if(v[0] == "weight1") weights["id"] = s0id;
                    if(v[0] == "weight2") weights["id"] = s1id;
                    weights["weight"] = $(this).prop('value');
                    allweights.push(weights);
                }
                else
                    data[v[0]] = $(this).prop('value');
                if(hasWeights == 2) {
                    data["weights"] = allweights;
                    hasWeights = 0;
                }
            });
            if(data["title"].length != 0) // save only if the title is not empty!
                dataComplete.push(data);

        });
        $("#criteriaJson").val(JSON.stringify(dataComplete));
        //console.log(JSON.stringify(dataComplete));

    }


</script>
@endsection
