@extends('admin.layouts.default')

@section('content')


<script type="text/javascript">
    $(document).ready(function() {
        $("#title").slugable({slugfield: '#name'});
    });
</script>
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
    {{ Form::open(array('action' => 'AdminEntityController@store','class'=>'form form-horizontal')) }}

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

    <!-- Slug -->
    <div class="form-group {{{ $errors->has('name') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="title">Name</label>

        <div class="col-sm-10">
            {{ Form::text('name', null, array('class'=>'form-control', 'id' => 'name', 'placeholder'=>'Name', 'value'=>Input::old('name'))) }}

            @if ($errors->first('name'))
            <span class="help-block">{{{ $errors->first('name') }}}</span>
            @endif
        </div>
    </div>
    <br>
    <div class="form-group {{{ $errors->has('languages') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="languages">Languages</label>

        <div class="col-sm-10">

            {{ Form::select("languages[]", $languagesByKey, null,array('multiple'=>true,'class'=>'select form-control', 'id' => 'languages')) }}
            @if ($errors->first('languages'))
            <span class="help-block">{{{ $errors->first('languages') }}}</span>
            @endif
        </div>
    </div>
    <br>

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
                name: {
                    validators: {
                        notEmpty: {
                        },
                        regexp: {
                            regexp: /^[a-zA-Z_]+$/,
                            message: 'This can only consist of alphabetical and underscore'
                        }
                    }
                }, "languages[]": {
                    validators: {
                        notEmpty: {
                        }
                    }
                }

            }
        });
    });
</script>
@stop