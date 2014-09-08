
@section('subcontent')

<?php
$locale = App::getLocale();
?>


<?php
if ($object->id) {
    ?>{{ Form::model($object,array('action' => array('AdminTaxonomyController@termUpdate',$object->id), 'method' => 'PATCH','class'=>'form-horizontal form')) }}
    <?php
} else {
    ?>{{ Form::model($object,array('action' => array('AdminTaxonomyController@termStore',$entity->id),'class'=>'form-horizontal form')) }}
    <?php
}
?>
<?php
if ($originalObject->id) {
    ?>
    {{ Form::hidden('originalObjectId', $originalObject->id) }}
    <h1>Add object translation</h1>
    <?php
}
?>
<?php
foreach ($fields as $field) {
    if (!$field['enabled'] || !$field['visible_form']) {
        continue;
    }
    $fieldName = $field['name'];
    $fieldType = $field['type'];
    $fieldTitle = $field['title_' . $locale];
    ?>
    <div class="form-group {{{ $errors->has($fieldName) ? 'has-error' : '' }}}">
        <label class="col-sm-2 control-label" for="<?php echo $fieldName ?>"><?php echo $fieldTitle ?></label>

        <div class="col-sm-10">
            <?php
            if ($fieldType == "text") {
                ?>
                {{ Form::text($fieldName, null, array('class'=>'form-control', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "textarea") {
                ?>
                {{ Form::textarea($fieldName, null, array('class'=>'form-control', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "richtext") {
                ?>
                {{ Form::textarea($fieldName, null, array('class'=>'form-control richtext', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "radio") {
                ?>
                {{ Form::radio($fieldName, 1, null,array('class'=>'', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "checkbox") {
                ?>
                {{ Form::checkbox($fieldName, 1,null, array('class'=>'', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "date") {
                $val = null;
                if ($object->$fieldName) {
                    $val = $object->$fieldName->format('Y-m-d');
                }
                ?>
                {{ Form::text($fieldName, $val, array('class'=>'form-control datepicker', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "select") {
                $values = array();
                if ($taxonomyTerms[$field['taxonomy_id']]) {
                    $values = $taxonomyTerms[$field['taxonomy_id']];
                }
                $values = array_merge(array('' => 'Select'), $values);
                ?>
                {{ Form::select($fieldName, $values, null,array('class'=>'select form-control', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "multiselect") {
                $values = array();
                if ($taxonomyTerms[$field['taxonomy_id']]) {
                    $values = $taxonomyTerms[$field['taxonomy_id']];
                }
                //$values = array_merge(array('' => 'Select'), $values);
                ?>
                {{ Form::select($fieldName."[]", $values, null,array('multiple'=>true,'class'=>'select form-control', 'id' => $fieldName)) }}
                <?php
            } elseif ($fieldType == "tree") {
                $values = array();
                if ($taxonomyTermsFull[$field['taxonomy_id']]) {
                    $values = $taxonomyTermsFull[$field['taxonomy_id']];
                }
                $count = count($values);
                ?>
                <select class="tree form-control" <?php if ($width) echo 'data-width="' . $width . '"'; ?> 

                        name="<?php echo $fieldName ?>" id="<?php echo $fieldName ?>">
                    <option value="">Select</option>
                    <?php
                    for ($i = 0; $i < $count;) {
                        $element1 = $values[$i];
                        if (is_array($object->$fieldName)) {
                            $sel = ( in_array($element1['_id'], $object->$fieldName) ) ? 'selected="selected"' : null;
                        } else {
                            $sel = ( $element1['_id'] == $object->$fieldName ) ? 'selected="selected"' : null;
                        }
                        ?>
                        <option value="<?php echo $element1['_id'] ?>" <?php echo $sel ?> data-level="<?php echo $element1['level'] ?>">
                            <?php echo $element1['title_' . $locale] ?></option>

                        <?php
                        $i++;
                    }
                    ?>
                </select>

                <?php
            } elseif ($fieldType == "multitree") {
                $values = array();
                if ($taxonomyTermsFull[$field['taxonomy_id']]) {
                    $values = $taxonomyTermsFull[$field['taxonomy_id']];
                }
                $count = count($values);
                ?>
                <select class="tree form-control" <?php if ($width) echo 'data-width="' . $width . '"'; ?> 

                        name="<?php echo $fieldName ?>[]" id="<?php echo $fieldName ?>"  multiple="multiple" >
                    <?php
                    for ($i = 0; $i < $count;) {
                        $element1 = $values[$i];
                        if (is_array($object->$fieldName)) {
                            $sel = ( in_array($element1['_id'], $object->$fieldName) ) ? 'selected="selected"' : null;
                        } else {
                            $sel = ( $element1['_id'] == $object->$fieldName ) ? 'selected="selected"' : null;
                        }
                        ?>
                        <option value="<?php echo $element1['_id'] ?>" <?php echo $sel ?> data-level="<?php echo $element1['level'] ?>">
                            <?php echo $element1['title_' . $locale] ?></option>

                        <?php
                        $i++;
                    }
                    ?>
                </select>

                <?php
            } elseif ($fieldType == "file") {
                ?>
                <input  name="{{$fieldName}}" id="{{$fieldName}}" type="text" value="{{$object->$fieldName}}" >
                <a href="{{{ URL::to('/') }}}/filemanager/dialog.php?akey=<?php echo md5("jdlfa900" . Config::get('app.key')); ?>&type=0&field_id={{$fieldName}}" class="btn btn-default btn-filemanager" type="button">Select</a>
                <?php
            } elseif ($fieldType == "image") {
                ?>
                <input  name="{{$fieldName}}" id="{{$fieldName}}" type="text" value="{{$object->$fieldName}}" >
                <a href="{{{ URL::to('/') }}}/filemanager/dialog.php?akey=<?php echo md5("jdlfa900" . Config::get('app.key')); ?>&type=1&field_id={{$fieldName}}" class="btn btn-default btn-filemanager" type="button">Select</a>
                <?php
            }
            ?>
            @if ($errors->first($fieldName))
            <span class = "help-block">{{{ $errors->first($fieldName) }}}</span>
            @endif
        </div>
    </div>
    <br>
    <?php
}
?>
   
    
<br>
 <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
  
{{ Form::submit('Update', array('class' => 'btn btn-success')) }}
</div>
</div>
{{ Form::close() }}
<script>
<?php
$rules = array();
foreach ($fields as $field) {
    if (!$field['enabled'] || !$field['visible_form'] || !$field['validation']) {
        continue;
    }
    $fieldName = $field['name'];
    $fieldType = $field['type'];   
    if($fieldType == "multitree" || $fieldType == "multiselect"){
        $fieldName = $fieldName."[]";
    }
    $validation = $field['validation'];
    if (!is_array($validation)) {
        $validation = array($validation);
    }
    $rules[$fieldName] = array('validators' => array());
    foreach ($validation as $rule) {
        if ($rule == "required") {
            $rules[$fieldName]['validators']['notEmpty'] = array();
        }
    }
}
?>
    $(document).ready(function() {

        $('.form').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: <?php echo json_encode($rules, JSON_FORCE_OBJECT) ?>
        });
    });
</script>
@stop