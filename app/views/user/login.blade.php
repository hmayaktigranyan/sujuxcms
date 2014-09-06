@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ Lang::get('Login') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="container">
<div class="page-header">
	<h1>Login</h1>
</div>
<form class="form-horizontal" method="POST" action="{{ URL::to('user/login') }}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <fieldset>
        <div class="form-group">
            <label class="col-md-2 control-label" for="email">Username</label>
            <div class="col-md-10">
                <input class="form-control" tabindex="1" type="text" name="username" id="email" value="{{ Input::old('username') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label" for="password">
               Password
            </label>
            <div class="col-md-10">
                <input class="form-control" tabindex="2"  type="password" name="password" id="password">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <div class="checkbox">
                    <label for="remember">Remember me
                        <input type="hidden" name="remember" value="0">
                        <input tabindex="4" type="checkbox" name="remember" id="remember" value="1">
                    </label>
                </div>
            </div>
        </div>

      

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <button tabindex="3" type="submit" class="btn btn-primary">Login</button>
               <!-- <a class="btn btn-default" href="forgot">{{ Lang::get('confide::confide.login.forgot_password') }}</a>-->
            </div>
        </div>
    </fieldset>
</form>
</div>
@stop
