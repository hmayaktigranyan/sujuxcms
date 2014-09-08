@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $entity->title; ?></h3>
        </div>
        <div class="panel-body">
            <div class="container-fluid">
                <div class="row">
                    <?php if ($filterFields) {
                        ?>
                        <div class="col-xs-9">
                            <?php
                        } else {
                            ?><div class="col-xs-12"><?php
                        }
                        ?>
                            @if(count($objects))
                            <?php echo $objects->appends(Input::all())->links(); ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <?php
                                            foreach ($fields as $field) {
                                                if (!$field['enabled'] || !$field['visible_browse']) {
                                                    continue;
                                                }
                                                $fieldName = $field['name'];
                                                $fieldType = $field['type'];
                                                $fieldTitle = $field['title_' . $locale];
                                                ?>
                                                <th><?php echo $fieldTitle ?></th>
                                            <?php } ?>
                                            <?php
                                            foreach ($entity->languages as $lang) {
                                                echo "<th>" . $languagesByKey[$lang] . "</th>";
                                            }
                                            ?>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach( $objects as $value )
                                        <tr>

                                            <?php
                                            foreach ($fields as $field) {
                                                if (!$field['enabled'] || !$field['visible_browse']) {
                                                    continue;
                                                }
                                                $fieldName = $field['name'];
                                                $fieldType = $field['type'];
                                                $fieldTitle = $field['title_' . $locale];
                                                ?>
                                                <td><?php
                                                    if ($fieldType == 'tree' || $fieldType == 'select') {
                                                        $fieldVal = $value[$fieldName];
                                                        if (is_array($fieldVal)) {
                                                            $fieldVal = array_shift($fieldVal);
                                                        }
                                                        echo $taxonomyTerms[$field['taxonomy_id']][$fieldVal];
                                                    } elseif ($fieldType == 'multitree' || $fieldType == 'multiselect') {
                                                        $fieldVals = $value[$fieldName];

                                                        if (!is_array($fieldVals)) {
                                                            $fieldVals = array($fieldVals);
                                                        }
                                                        $fieldVals2 = array();
                                                        foreach ($fieldVals as $fieldVal) {
                                                            $fieldVals2[] = $taxonomyTerms[$field['taxonomy_id']][$fieldVal];
                                                        }
                                                        echo implode(", ", $fieldVals2);
                                                    } elseif ($fieldType == 'date') {

                                                        if ($value[$fieldName]) {
                                                            if ($value[$fieldName] instanceof MongoDate) {
                                                                $value[$fieldName] = $value[$fieldName]->sec;
                                                            }
                                                            echo Carbon::createFromTimestamp($value[$fieldName])->format('Y-m-d');
                                                        }
                                                    } elseif ($fieldType == 'file' || $fieldType == 'image') {
                                                        if ($value[$fieldName]) {
                                                            $pathinfo = pathinfo($value[$fieldName]);
                                                            echo "<a href='" . $value[$fieldName] . "' target='_blank'>" . $pathinfo['basename'] . "</a>";
                                                        }
                                                    } elseif ($fieldType == 'checkbox') {

                                                        if ($value[$fieldName]) {
                                                            echo trans('Yes');
                                                        } else {
                                                            echo trans('No');
                                                        }
                                                    } else {
                                                        echo $value[$fieldName];
                                                    }
                                                    ?></td>
                                            <?php } ?>


                                            <?php
                                            $possibleLanguages = array();
                                            foreach ($entity->languages as $lang) {
                                                if ($value['language'] == $lang) {
                                                    ?>
                                                    <td><a href="{{{ URL::to('admin/'.$path.'/edit/' .$value['_id']) }}}" class="btn btn-info">
                                                            <span class="glyphicon glyphicon-edit"></span>&nbsp;Edit
                                                        </a></td>
                                                    <?php
                                                } elseif ($value[$lang . "_id"]) {
                                                    ?>
                                                    <td><a href="{{{ URL::to('admin/'.$path.'/edit/' .$value[$lang."_id"]) }}}" class="btn">
                                                            <span class="glyphicon glyphicon-edit"></span>&nbsp;Edit
                                                        </a></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td><a href="{{{ URL::to('admin/object/translate/' . $value['_id']."/".$lang) }}}" >
                                                            <span class="glyphicon glyphicon-plus"></span>&nbsp;Add 
                                                        </a></td>
                                                    <?php
                                                }
                                            }
                                            /* if (count($possibleLanguages) == 1) {
                                              reset($possibleLanguages);
                                              $lang = key($possibleLanguages);
                                              ?>
                                              <a href="{{{ URL::to('admin/object/translate/' . $entity->id."/".$lang) }}}" class="btn btn-primary">
                                              <span class="glyphicon glyphicon-plus"></span>&nbsp;Add <?php echo $possibleLanguages[$lang] ?>
                                              </a>

                                              <?php
                                              } elseif (count($possibleLanguages) > 1) {
                                              ?>
                                              <div class="btn-group">
                                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                              <span class="glyphicon glyphicon-plus"></span>&nbsp;Add  <span class="caret"></span>
                                              </button>
                                              <ul class="dropdown-menu" role="menu">

                                              <?php foreach ($possibleLanguages as $lang => $possibleLanguage) {
                                              ?>
                                              <li><a href="{{{ URL::to('admin/object/translate/' . $entity->id."/".$lang) }}}" >
                                              <?php echo $possibleLanguage ?>
                                              </a></li>
                                              <?php } ?>

                                              </ul>
                                              </div>

                                              <?php
                                              } */
                                            ?>
                                           
                                                        <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                        Action  <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">


                                                        <li><a href="{{{ URL::to('admin/'.$path.'/show/' . $value['_id']) }}}" class="btn">
                                                                <span class="glyphicon glyphicon-eye-open"></span>&nbsp;Show
                                                            </a></li>
                                                        <li> <a href="{{{ URL::to('admin/'.$path.'/edit/' .$value['_id']) }}}" class="btn">
                                                                <span class="glyphicon glyphicon-edit"></span>&nbsp;Edit
                                                            </a></li>
                                                        <li> <a href="{{{ URL::to('admin/'.$path.'/delete/' . $value['_id']) }}}" class="btn">
                                                                <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
                                                            </a></li>

                                                    </ul>
                                                </div>

                                            </td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <?php echo $objects->appends(Input::all())->links(); ?>
                            @else
                            <div class="alert alert-danger">No results found</div>
                            @endif
                        </div>


                        <?php if ($filterFields) {
                            ?>
                            <div class="col-xs-3">
                                {{ Form::model($object,array('action' => array('AdminObjectController@index',$entity->id), 'method' => 'GET','class'=>'form')) }}
                                {{ Form::submit('Search', array('class' => 'btn btn-success')) }}
                                {{ HTML::link('/admin/'.$path.'/'.$entity->id,'Reset', array('class'=>'btn btn-danger')) }}
                                <?php
                                if ($queryFilterExists) {
                                    $fieldName = "query";
                                    ?>
                                    <div class="form-group" >
                                        <label class="control-label" for="<?php echo $fieldName ?>"><?php echo trans('Text') ?></label>

                                        <div class="controls">
                                            {{ Form::text($fieldName, Input::get('query'), array('class'=>'form-control', 'id' => $fieldName)) }}
                                        </div></div>

                                    <?php
                                }
                                foreach ($filterFields as $fieldName => $field) {
                                    $fieldName = $field['name'];
                                    $fieldType = $field['type'];
                                    if ($fieldType == "text" || $fieldType == "textarea") {
                                        continue;
                                    }
                                    $fieldTitle = $field['title_' . $locale];
                                    if ($fieldType == "language") {
                                        $fieldTitle = trans('Language');
                                    }
                                    $queryParam = $queryParams[$fieldName];
                                    ?>
                                    <div class="form-group" >
                                        <label class="control-label" for="<?php echo $fieldName ?>"><?php echo $fieldTitle ?></label>

                                        <div class="controls">

                                            <?php
                                            if ($fieldType == "select" || $fieldType == 'multiselect') {
                                                $values = array();
                                                if ($taxonomyTerms[$field['taxonomy_id']]) {
                                                    $values = $taxonomyTerms[$field['taxonomy_id']];
                                                }
                                                ?>

                                                {{ Form::select($fieldName."[]", $values, $queryParam,array('multiple'=>true,'class'=>'select form-control', 'id' => $fieldName)) }}
                                                <?php
                                            } elseif ($fieldType == 'tree' || $fieldType == 'multitree') {
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
                                                                if (is_array($queryParam)) {
                                                                    $sel = ( in_array($element1['_id'], $queryParam) ) ? 'selected="selected"' : null;
                                                                } else {
                                                                    $sel = ( $element1['_id'] == $queryParam) ? 'selected="selected"' : null;
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
                                        } elseif ($fieldType == "language") {
                                            $possibleLanguages = array();
                                            foreach ($entity->languages as $lang) {
                                                $possibleLanguages[$lang] = $languagesByKey[$lang];
                                            }
                                            ?>

                                            {{ Form::select("language[]",$possibleLanguages, $queryParam,array('multiple'=>true,'class'=>'select form-control', 'id' => $fieldName)) }}
                                            <?php
                                        } elseif ($fieldType == 'date') {
                                            ?>
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    {{ Form::label($fieldName."[start]", 'Start') }}
                                                    {{ Form::text($fieldName."[start]", $queryParams[$fieldName]['start'], array('class'=>'form-control datepicker', 'id' => $fieldName."_start")) }}

                                                </div>
                                                <div class="col-xs-6">
                                                    {{ Form::label($fieldName."[end]", 'End') }}
                                                    {{ Form::text($fieldName."[end]", $queryParams[$fieldName]["end"], array('class'=>'form-control datepicker', 'id' => $fieldName."_end")) }}
                                                </div>
                                            </div>
                                            <?php
                                        } elseif ($fieldType == 'checkbox') {
                                            $checked = false;
                                            if ($queryParam && in_array(1, $queryParam)) {
                                                $checked = true;
                                            }
                                            ?>
                                            <label class="checkbox-inline">
                                                {{ Form::checkbox($fieldName."[]",1,$checked, array('class'=>'', 'id' => $fieldName)) }} Yes
                                            </label>
                                            <label class="checkbox-inline">
                                                <?php
                                                $checked = false;
                                                if ($queryParam && in_array(0, $queryParam)) {
                                                    $checked = true;
                                                }
                                                ?>
                                                {{ Form::checkbox($fieldName."[]", 0,$checked, array('class'=>'', 'id' => $fieldName)) }} No
                                            </label>
                                            <?php
                                        } elseif ($fieldType == 'file' || $fieldType == 'image') {
                                            $checked = false;
                                            if ($queryParam && in_array(1, $queryParam)) {
                                                $checked = true;
                                            }
                                            ?>
                                            <label class="checkbox-inline">
                                                {{ Form::checkbox($fieldName."[]",1,$checked, array('class'=>'', 'id' => $fieldName)) }} Yes
                                            </label>
                                            <label class="checkbox-inline">
                                                <?php
                                                $checked = false;
                                                if ($queryParam && in_array(0, $queryParam)) {
                                                    $checked = true;
                                                }
                                                ?>
                                                {{ Form::checkbox($fieldName."[]", 0,$checked, array('class'=>'', 'id' => $fieldName)) }} No
                                            </label>
                                            <?php
                                        }
                                        ?>

                                    </div></div>

                                <?php
                            }
                            ?>

                            {{ Form::close() }}
                        </div>
                        <?php }
                        ?></div>
                </div>
            </div>
        </div>

    </div>
    @stop