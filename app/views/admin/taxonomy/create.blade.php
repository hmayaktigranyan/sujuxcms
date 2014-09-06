@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<script type="text/javascript">
    $(document).ready(function() {
    $("#title_<?php echo $locale ?>").slugable({slugfield: '#name'});
    });</script>
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
    {{ Form::open(array('action' => 'AdminTaxonomyController@store','class'=>'form form-horizontal')) }}


    <?php
    foreach ($languages as $language) {
        $fieldName = "title_" . $language->code;
        $fieldTitle = $language->title;
        ?>
        <div class="form-group {{{ $errors->has($fieldName) ? 'has-error' : '' }}}">
            <label class="col-sm-2 control-label" for="<?php echo $fieldName ?>"><?php echo $fieldTitle ?> Title</label>

            <div class="col-sm-10">
                {{ Form::text($fieldName, null, array('class'=>'form-control', 'id' => $fieldName,  'value'=>Input::old($fieldName))) }}
                @if ($errors->first($fieldName))
                <span class="help-block">{{{ $errors->first($fieldName) }}}</span>
                @endif
            </div>
        </div>
        <br>
        <?php
    }
    ?>
    <div class="form-group {{{ $errors->has('name') ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="title">Name</label>

        <div class="col-sm-10">
            {{ Form::text('name', null, array('class'=>'form-control', 'id' => 'name', 'value'=>Input::old('name'))) }}
            @if ($errors->first('name'))
            <span class="help-block">{{{ $errors->first('name') }}}</span>
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