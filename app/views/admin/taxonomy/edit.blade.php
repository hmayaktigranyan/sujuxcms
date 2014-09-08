@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="page-header">
        <h3>Update</h3>
    </div>
    <div class="pull-left">
        <div class="btn-toolbar">
            <a href="{{{ url('admin/'.$path) }}}"  class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
            </a>
             <a href="{{{ url('admin/'.$path.'/terms/'.$object->id) }}}"  class="btn btn-warning">
                <span class="glyphicon glyphicon-th"></span>&nbsp;Terms
            </a>
             <a href="{{{ url('admin/'.$path.'/fields/'.$object->id) }}}"  class="btn btn-info">
                        <span class="glyphicon glyphicon-list"></span>&nbsp;Fields
                    </a>
            <a href="{{{ url('admin/'.$path.'/'.$object->id) }}}"  class="btn btn-success">
                <span class="glyphicon  glyphicon-eye-open"></span>&nbsp;Show
            </a>
            <a href="{{{ URL::to('admin/'.$path.'/' . $object->id.'/delete') }}}" class="btn btn-danger">
                <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
            </a>
        </div>
    </div>
    <br/><br/>
    {{ Form::open( array( 'action' => array( 'AdminTaxonomyController@update', $object->id), 'method' => 'PATCH','class'=>'form form-horizontal')) }}
    <!-- Title -->
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

    <?php
    foreach ($languages as $language) {
        $fieldName = "title_" . $language->code;
        $fieldTitle = $language->title;
        ?>
        <div class="form-group {{{ $errors->has($fieldName) ? 'has-error' : '' }}}">
            <label class="col-sm-2 control-label" for="<?php echo $fieldName ?>"><?php echo $fieldTitle ?> Title</label>

            <div class="col-sm-10">
                {{ Form::text($fieldName, $object->$fieldName, array('class'=>'form-control', 'id' => $fieldName,  'value'=>Input::old($fieldName))) }}
                @if ($errors->first($fieldName))
                <span class="help-block">{{{ $errors->first($fieldName) }}}</span>
                @endif
            </div>
        </div>
        <br>
        <?php
    }
    ?>


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
<?php
foreach ($languages as $language) {
    $fieldName = "title_" . $language->code;
    ?>
    <?php echo $fieldName ?>: {
                validators: {
                notEmpty: {
                }
                }
                },
    <?php
}
?>

            name: {
            validators: {
            notEmpty: {
            },
                    regexp: {
                    regexp: /^[a-zA-Z0-9_]+$/,
                            message: 'This can only consist of alphabetical, number and underscore'
                    }
            }
            }

            }
    });
    });
</script>
@stop