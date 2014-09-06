@extends('admin.layouts.default')

@section('content')


<script type="text/javascript">
    $(document).ready(function() {
        // $("#title").slugable({slugfield: '#name'});
    });
</script>
<div class="container-fluid">
    <div class="page-header">
        <h3>Update</h3>
    </div>
    <div class="pull-left">
        <div class="btn-toolbar">
            <a href="{{{ url('admin/'.$path) }}}"  class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
            </a>
             <a href="{{{ url('admin/'.$path.'/fields/'.$object->id) }}}"  class="btn btn-warning">
                <span class="glyphicon glyphicon-th"></span>&nbsp;Fields
            </a>
            <a href="{{{ url('admin/'.$path.'/'.$object->id) }}}"  class="btn btn-success">
                <span class="glyphicon  glyphicon-eye-open"></span>&nbsp;Show
            </a>
            <a href="{{{ URL::to('admin/'.$path.'/' . $object->id.'/delete') }}}" class="btn btn-danger">
                <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
            </a>
        </div>
    </div>
    <br/><br/><br/>
    {{ Form::open( array( 'action' => array( 'AdminEntityController@update', $object->id), 'method' => 'PATCH','class'=>'form form-horizontal')) }}
    <!-- Title -->
    <div class="form-group {{{ $errors->has('title') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="title">Title</label>

        <div class="col-sm-10">
            {{ Form::text('title', $object->title, array('class'=>'form-control', 'id' => 'title', 'placeholder'=>'Title', 'value'=>Input::old('title'))) }}
            @if ($errors->first('title'))
            <span class="help-block">{{{ $errors->first('title') }}}</span>
            @endif
        </div>
    </div>
    <br>

    <!-- Name -->
    <div class="form-group {{{ $errors->has('name') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="name">Name</label>

        <div class="col-sm-10">
            {{ Form::text('name', $object->name, array('class'=>'form-control', 'id' => 'name', 'placeholder'=>'Name', 'value'=>Input::old('name'))) }}
            @if ($errors->first('name'))
            <span class="help-block">{{{ $errors->first('name') }}}</span>
            @endif
        </div>
    </div>
    <br>
    <div class="form-group {{{ $errors->has('languages') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="languages">Languages</label>

        <div class="col-sm-10">

            {{ Form::select("languages[]", $languagesByKey, $object->languages,array('multiple'=>true,'class'=>'select form-control', 'id' => 'languages')) }}
            @if ($errors->first('languages'))
            <span class="help-block">{{{ $errors->first('languages') }}}</span>
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