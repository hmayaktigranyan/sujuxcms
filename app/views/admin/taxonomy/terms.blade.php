@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">

            <h3 class="panel-title">Terms</h3>
        </div>

        <div class="panel-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#">Terms</a></li>
                <li><a href="{{{ url('admin/'.$path.'/termsorder/'.$object->id) }}}">Terms Order</a></li>
            </ul><br/>

            {{ Form::open( array( 'action' => array( 'AdminTaxonomyController@termsUpdate', $object->id), 'method' => 'PATCH','class'=>'form')) }}

            <center>

                <button type="submit" name="update" class='btn  btn-primary'  ><i class="icon-ok icon-white"></i> <?php echo trans('Save') ?></button>

                <button name='' class="btn" onclick="add_new_mt();
                        return false;" ><i class="icon-plus"></i> <?php echo trans('Add new term') ?></button>

            </center>
            <div class="pull-left" >

                <select name="bulkaction" class="select">

                    <option value=""></option>
                    <option value="deleteselected"><?php echo trans('Delete') ?></option>
                    <option value="updateselected"><?php echo trans('Update') ?></option>
                    <option value="visible"><?php echo trans('Set visible ') ?></option>
                    <option value="disable"><?php echo trans('Disable') ?></option>
                </select>
                &nbsp;
                <button type="submit" name="apply" class='btn  btn-success'  ><i class="icon-ok"></i> 
                    <?php echo trans('Apply') ?></button>
            </div>
            <br/><br/>
            <table class='table table-bordered table-striped table-hover' id="mtlist">
                <thead>
                    <tr>
                        <th ><input type="checkbox" class="checkall"/></th>
                        <th ><?php echo(trans('Visible')); ?></th>
                        <th>
                <ul class="nav nav-tabs" id="langTabs">   
                    <?php
                    foreach ($languages as $language) {
                        ?><li <?php
                        if ($locale == $language->code) {
                            echo 'class="active"';
                        }
                        ?> ><a href="#<?php echo $language->code ?>" data-toggle="tab" data-locale="<?php echo $language->code ?>"><?php echo $language->title ?></a></li>
                        <?php } ?>
                </ul>
                </th>
                <?php
                $fieldsExists = false;
                foreach ($fields as $field) {
                    if (!$field['enabled'] || !$field['visible_browse']) {
                        continue;
                    }
                    $fieldsExists = true;
                    $fieldName = $field['name'];
                    $fieldType = $field['type'];
                    $fieldTitle = $field['title_' . $locale];
                    ?>
                    <th><?php echo $fieldTitle ?></th>
                    <?php
                }
                if ($fieldsExists) {
                    ?>
                    <th>Action</th>
                    <?php
                }
                ?>

                </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($terms as $value) {
                        ?>
                        <tr>
                            <td> <input  type="checkbox" class="chk" name="term_list[<?php echo $value['_id'] ?>]"  value="1"
                                <?php
                                if ($_POST['bulkaction'] == "deleteselected" && is_array($_POST['term_list']) && isset($_POST['term_list'][$value['term']])) {
                                    echo " checked='checked' ";
                                }
                                ?>
                                         ></input>
                            </td>
                            <td><?php echo $value['visible'];
                                ?></td>

                            <td>
                                <?php
                                foreach ($languages as $language) {
                                    ?>
                                    <div class="titleinputdiv titleinputdiv_<?php echo $language->code ?>" <?php
                                    $fieldName = "title_" . $language->code;


                                    if ($locale != $language->code) {
                                        echo 'style="display:none"';
                                    }
                                    ?>>
                                        <input  type="text" name="title[<?php echo $value['_id']; ?>][<?php echo $language->code ?>]"  value="<?php if ($value[$fieldName]) echo htmlentities($value[$fieldName], ENT_QUOTES, "UTF-8"); ?>" 
                                                class="form-control"></input>

                                    </div>
                                    <?php
                                }
                                ?>
                            </td>
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
                                <?php
                            }
                            if ($fieldsExists) {
                                ?>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                            Action  <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">


                                            <li><a href="{{{ URL::to('admin/'.$path.'/termshow/' . $value['_id']) }}}" class="btn">
                                                    <span class="glyphicon glyphicon-eye-open"></span>&nbsp;Show
                                                </a></li>
                                            <li> <a href="{{{ URL::to('admin/'.$path.'/termedit/' .$value['_id']) }}}" class="btn">
                                                    <span class="glyphicon glyphicon-edit"></span>&nbsp;Edit
                                                </a></li>
                                            <li> <a href="{{{ URL::to('admin/'.$path.'/termdelete/' . $value['_id']) }}}" class="btn">
                                                    <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
                                                </a></li>

                                        </ul>
                                    </div>

                                </td>
                            <?php }
                            ?>


                        </tr>
                    <?php } ?>

                </tbody>
            </table>

            {{ Form::close() }}
        </div>
    </div>

</div>

<script>

    $(document).ready(function() {
        $('#langTabs a').click(function(e) {
            e.preventDefault();
            var locale = $(this).data('locale');
            $('.titleinputdiv').hide();
            $('.titleinputdiv_' + locale).show();
        });

        $('.checkall').click(function() {
            $(".chk").prop("checked", $(".checkall").prop("checked"))
        });
    });
    function add_new_mt()
    {
        var template = "";
        template += "<tr><td></td><td></td><td>";
        template += "<?php
                    foreach ($languages as $language) {

                        echo "<div class='titleinputdiv titleinputdiv_" . $language->code . "' ";
                        if ($locale != $language->code) {
                            echo "style='display:none'";
                        }
                        echo ">";
                        echo "<input  type='text' name='new_term_title[" . $language->code . "][]' class='form-control'  /></div>";
                    }
                    ?>";
        template += "</td></tr>";
        $('#mtlist  tr:last').after(template);
        /*if($('#mtlist > tbody > tr:first').lenght){
         $('#mtlist > tbody > tr:first').before(template);
         }else{
         $('#mtlist > tbody:last').append(template);
         }*/
    }
</script>

@stop