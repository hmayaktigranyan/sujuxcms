@extends('admin.layouts.default')

@section('content')


<div class="container-fluid">
    <div class="page-header">
        <h3>Create</h3>
    </div>
    <div class="pull-left">
        <div class="btn-toolbar">
            <a href="{{{ url('admin/'.$path) }}}"  class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
            </a>
        </div>
    </div>
    <br/><br/>
    {{ Form::open(array('action' => 'AdminLanguageController@store','class'=>'form form-horizontal')) }}

    <!-- Title -->
    <div class="form-group {{{ $errors->has('title') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="title">Title</label>

        <div class="col-sm-10">
            {{ Form::text('title', null, array('class'=>'form-control', 'id' => 'title', 'placeholder'=>'Title', 'value'=>Input::old('title'))) }}
            @if ($errors->first('title'))
            <span class="help-block">{{{ $errors->first('title') }}}</span>
            @endif
        </div>
    </div>
    <br>

    <!-- Code -->
    <div class="form-group {{{ $errors->has('code') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="title">Code</label>

        <div class="col-sm-10">
            {{ Form::text('code', null, array('class'=>'form-control', 'id' => 'code', 'placeholder'=>'Code', 'value'=>Input::old('code'))) }}
            @if ($errors->first('code'))
            <span class="help-block">{{{ $errors->first('code') }}}</span>
            @endif
        </div>
    </div>
    <br>

<div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">

    {{ Form::submit('Create', array('class' => 'btn btn-success')) }}
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
                title: {
                    validators: {
                        notEmpty: {
                        }
                    }
                },
                code: {
                    validators: {
                        notEmpty: {
                        },
                        regexp: {
                            regexp: /^[a-zA-Z_]+$/,
                            message: 'This can only consist of alphabetical and underscore'
                        }
                    }
                }

            }
        });
    });
</script>
@stop