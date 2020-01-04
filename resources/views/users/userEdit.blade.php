@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">
        <div class="card" style="width: 100%">
            <form id="userForm" method="POST" action="{{ route('user.update', $user) }}">
                @csrf
            <div class="card-header">
                <span style="font-size:1.5rem;margin-right:2rem">@lang('c4pe.user.title')</span>
                <button type="button" class="btn btn-primary float-right" onclick="makeSubmit();">@lang('c4pe.user.saveButton')</button>
            </div>
            <div class="card-body">

                    <div class="form-group row">
                        <label for="lastname" class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.lastname')</label>
                        <div class="col-md-3">
                            <input id="lastname" type="text" maxlength="32" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{ old('lastname') != "" ? old('lastname') : $user->lastname }}" required autofocus>
                            @if ($errors->has('lastname'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('lastname') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="firstname" class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.firstname')</label>

                        <div class="col-md-3">
                            <input id="firstname" type="text" maxlength="32" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{ old('firstname') != "" ? old('firstname') : $user->firstname }}" required>
                            @if ($errors->has('firstname'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('firstname') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.email')</label>

                        <div class="col-md-3">
                            <input id="email" name="email" type="text" maxlength="64" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') != "" ? old('email') : $user->email }}" required>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.role')</label>
                        <div class="col-md-2">
                            @can('changeUserRole', \App\User::class)
                            <select class="custom-select" id="role" name="role">
                                @foreach (\App\User::getRoles() as $role)
                                    <option value="{{$role}}" {{ $role == $user->role ? ' selected' : '' }}>@lang('c4pe.userList.role_'.$role)</option>
                                @endforeach
                            </select>
                            @endcan
                            @cannot('changeUserRole', \App\User::class)
                                <label class="col-form-label"><b>@lang('c4pe.userList.role_'.$user->role)</b></label>
                            @endcannot
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.organisation')</label>
                        <div class="col-md-2">
                            @can('changeUserOrganisation', \App\User::class)
                                <select class="custom-select" id="organisation" name="organisation">
                                    @foreach ($orgas as $orga)
                                        <option value="{{$orga->id}}" {{ $orga->id == $user->organisation_id? ' selected' : '' }}>{{$orga->title}}</option>
                                    @endforeach
                                </select>
                            @endcan
                            @cannot('changeUserOrganisation', \App\User::class)
                                @foreach ($orgas as $orga)
                                    @if($orga->id == $user->organisation_id)
                                        <label class="col-form-label"><b>{{$orga->title}}</b></label>
                                    @endif
                                @endforeach
                            @endcannot
                        </div>
                    </div>

                    @if($user->id == -1)
                        <div class="form-group row">
                            <label for="passwordNew" class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.password')</label>

                            <div class="col-md-3">
                                <input id="passwordNew" name="passwordNew" type="password" maxlength="32" class="form-control{{ $errors->has('passwordNew') ? ' is-invalid' : '' }}" name="passwordNew" value="{{ old('passwordNew') != "" ? old('passwordNew') : $user->password }}" required>
                                @if ($errors->has('passwordNew'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('passwordNew') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($user->id != -1)
                        <div class="form-group row">
                            <label for="pwd" class="col-md-2 col-form-label text-md-right">@lang('c4pe.user.password')</label>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm" style="margin-bottom:0.8rem" data-toggle="modal" data-target="#ChangePasswortModal">@lang('c4pe.user.pwdChangeButton')</button>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade " id="ChangePasswortModal" tabindex="-1" role="dialog" aria-labelledby="ChangePasswortModal" aria-hidden="true">
        <form method="POST" action="{{ route('user.updatePassword', $user) }}">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ChangePasswortModalTitle">@lang('c4pe.user.pwdChange.title')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf

                        <div class="form-group row">
                            <label for="password" class="col-sm-2 col-form-label text-md-right">@lang('c4pe.user.password')</label>

                            <div class="col-md-6">
                                <input id="password" type="password" maxlength="32" class="form-control{{ $errors->has('course') ? ' is-invalid' : '' }}" name="password" value="" required autofocus>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password2" class="col-sm-2 col-form-label text-md-right">@lang('c4pe.user.password2')</label>

                            <div class="col-md-6">
                                <input id="password2" type="password" maxlength="32" class="form-control{{ $errors->has('course') ? ' is-invalid' : '' }}" name="password2" value="" required>
                                @if ($errors->has('password2'))
                                    <span class="invalid-feedback" style="display:block" role="alert">
                                        <strong>{{ $errors->first('password2') }}</strong>
                                    </span>
                                @endif

                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('c4pe.user.pwdCloseButton')</button>
                        <button type="submit" class="btn btn-primary">@lang('c4pe.user.pwdSaveButton')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>




</div>


<script>

@if ($errors->has('password') || $errors->has('password2'))
        $(document).ready(function(){
            $("#ChangePasswortModal").modal('show');
        });
@endif

    function makeSubmit() {
        $('#userForm').submit();
        return true;
    }
</script>



@endsection

