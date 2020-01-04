@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="card" style="width: 100%">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-9">
                        <span style="font-size:1.5rem;margin-right:2rem">@lang('c4pe.userList.ListofUsers')</span>
                    </div>
                    <div class="col-md-3">
                        <a href="{{route('user.edit', $userNew)}}" style="margin-top:8px" class="btn btn-sm btn-primary">@lang('c4pe.userList.newUser')</a>
                    </div>
                </div>

            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-hover table-sm">
                            <thead>
                            <tr>
                                <td>@lang('c4pe.userList.table.lastname')</td>
                                <td>@lang('c4pe.userList.table.firstname')</td>
                                <td>@lang('c4pe.userList.table.email')</td>
                                <td>@lang('c4pe.userList.table.lastLogin')</td>
                                <td>@lang('c4pe.userList.table.role')</td>
                                <td>@lang('c4pe.userList.table.organisation')</td>
                                <td></td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td><i>{{$user->lastname}}</i></td>
                                    <td><i>{{$user->firstname}}</i></td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->last_login}}</td>
                                    <td>@lang('c4pe.userList.role_'.$user->role)</td>
                                    <td>{{$user->myOrganisation->title}}</td>
                                    <td>
                                        <a href="{{route('user.edit', $user)}}" class="btn btn-sm btn-primary">@lang('c4pe.userList.editButton')</a>
                                        @if($user->id != Auth::user()->id)
                                            <a href="#DestroyUserModal"  class="btn btn-sm btn-primary" onclick="destroy('{{route('user.destroy', $user)}}'); return false;" >@lang('c4pe.userList.removeButton')</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade " id="DestroyUserModal" tabindex="-1" role="dialog" aria-labelledby="DestroyUserModal" aria-hidden="true">
        <form id="destroyForm" method="POST" action="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NewCourseModalTitle">@lang('c4pe.userList.destroyUser.title')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="col-md-offset-1 col-md-10">
                        <span>@lang('c4pe.userList.destroyUser.destroyUserQuestion')</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('c4pe.userList.destroyUser.cancelButton')</button>
                    <button type="submit" class="btn btn-primary btn-danger">@lang('c4pe.userList.destroyUser.deleteButton')</button>
                </div>
            </div>
        </div>
        </form>
    </div>


</div>

@if ($errors->any())
<script type="text/javascript">
    $(document).ready(function(){
        $("#NewCourseModal").modal('show');
    });
</script>
@endif
<script>
   function destroy(url) {
       $("#destroyForm").attr('action',  url );

       $('#DestroyUserModal').modal('show');
    };

</script>
@endsection
