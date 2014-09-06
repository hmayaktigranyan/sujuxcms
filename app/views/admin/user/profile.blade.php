@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('currentpassword')
{{{ Lang::get('user/user.profile') }}} ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="container">
<div class="page-header">
	<h1>User Profile</h1>
</div>
{{ Form::open( array( 'action' => array( 'AdminController@profileUpdate', $object->id), 'method' => 'PATCH','class'=>'form form-horizontal')) }}
    <div class="form-group {{{ $errors->has('currentpassword') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="currentpassword">Current password</label>

        <div class="col-sm-10">
            {{ Form::password('currentpassword', array('class'=>'form-control', 'id' => 'currentpassword', 'placeholder'=>'', 'value'=>'')) }}
            @if ($errors->first('currentpassword'))
            <span class="help-block">{{{ $errors->first('currentpassword') }}}</span>
            @endif
        </div>
    </div>
    <br>
     <div class="form-group {{{ $errors->has('newpassword') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="newpassword">New password</label>

        <div class="col-sm-10">
            {{ Form::password('newpassword',array('class'=>'form-control', 'id' => 'newpassword', 'placeholder'=>'', 'value'=>'')) }}
            @if ($errors->first('newpassword'))
            <span class="help-block">{{{ $errors->first('newpassword') }}}</span>
            @endif
        </div>
    </div>
    <br>
     <div class="form-group {{{ $errors->has('newpassword_confirmation') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="newpassword_confirmation">Confirm password</label>

        <div class="col-sm-10">
            {{ Form::password('newpassword_confirmation', array('class'=>'form-control', 'id' => 'newpassword_confirmation', 'placeholder'=>'', 'value'=>'')) }}
            @if ($errors->first('newpassword_confirmation'))
            <span class="help-block">{{{ $errors->first('newpassword_confirmation') }}}</span>
            @endif
        </div>
    </div>
    <br>
     <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">

    {{ Form::submit('Update', array('class' => 'btn btn-success')) }}
        </div>
     </div>
    {{ Form::close() }}
</div>

<script>
    $(document).ready(function() {
        $('.form').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                currentpassword: {
                    validators: {
                        notEmpty: {
                        }
                    }
                },
                newpassword: {
                    validators: {
                        notEmpty: {
                        },identical: {
                        field: 'newpassword_confirmation',
                        message: 'The password and its confirm are not the same'
                    }
                    }
                }, newpassword_confirmation: {
                    validators: {
                        notEmpty: {
                        },identical: {
                        field: 'newpassword',
                        message: 'The password and its confirm are not the same'
                    }
                    }
                }

            }
        });
    });
</script>
@stop
