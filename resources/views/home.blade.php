@extends('layouts.frontendApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        @foreach($organisations as $orga)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header"><h4>{{$orga->info}}</h4></div>

                    <div class="card-body">
                        <div style="display: table-cell; width:10px"></div>
                        <div id="tablecell">
                            <a href="{{route('home.formList', $orga->id)}}">
                                <img src="{{asset("images/organisations/".$orga->title.".png")}}" class="img-fluid" style="margin:auto">
                            </a>
                        </div>
                        <div style="display: table-cell; width:10px"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
