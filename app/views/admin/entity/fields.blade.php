@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Details</h3>
        </div>
        <div class="panel-body">
             <ul class="nav nav-tabs" role="tablist">
                 <li  class="active"><a href="#">Fields</a</li>
                <li><a href="{{{ url('admin/'.$path.'/fieldsorder/'.$object->id) }}}">Fields Order</a></li>
                <li><a href="{{{ url('admin/'.$path.'/fieldsdetails/'.$object->id) }}}">Fields Details</a></li>
                
            </ul><br/>
           
            {{ Form::open( array( 'action' => array( 'AdminEntityController@fieldsUpdate', $object->id), 'method' => 'PATCH','class'=>'form')) }}

            <center>

                <button type="submit" name="update" class='btn  btn-primary' value="update" ><i class="icon-ok icon-white"></i> <?php echo trans('Save') ?></button>

                <button name='' class="btn" onclick="add_new_field();
                        return false;" ><i class="icon-plus"></i> <?php echo trans('Add new field') ?></button>

            </center>
            <div class="pull-left" >

                <select name="bulkaction" class="select">

                    <option value=""></option>
                    <option value="deleteselected"><?php echo trans('Delete') ?></option>
                    <option value="updateselected"><?php echo trans('Update') ?></option>
                    <option value="enabled"><?php echo trans('Enable') ?></option>
                    <option value="disable"><?php echo trans('Disable') ?></option>
                </select>
                &nbsp;<button type="submit" name="apply" class='btn btn-success'  ><i class="icon-ok"></i> 
                    <?php echo trans('Apply') ?></button>
            </div>
            <br/><br/>
            <table class='table table-bordered table-striped table-hover' id="mtlist">
                <thead>
                    <tr>
                        <th ><input type="checkbox" class="checkall"/></th>
                        <th ><?php echo(trans('Name')); ?></th>
                        <th ><?php echo(trans('Type')); ?></th>
                        <th ><?php echo(trans('Enabled')); ?></th>
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
                </tr>
                </thead>
                <tbody>
                    <?php
                    if ($fieldsOrdered) {
                        foreach ($fieldsOrdered as $record) {
                            ?>
                            <tr>
                                <td> <input  type="checkbox" class="chk" name="fields_list[<?php echo $record['name'] ?>]"  value="1"
                                    <?php
                                    if ($_POST['bulkaction'] == "deleteselected" && is_array($_POST['term_list']) && isset($_POST['term_list'][$record['term']])) {
                                        echo " checked='checked' ";
                                    }
                                    ?>
                                             ></input>
                                </td>
                                <td><?php echo $record['name']; ?></td>
                                <td><?php
                                    echo $record['type'];
                                    if ($record['type'] == 'tree' || $record['type'] == 'multitree' || $record['type'] == 'select' || $record['type'] == 'multiselect') {
                                        echo " - ";
                                        ?>
                                        <a class="btn btn-link btn-xs" href="{{{ URL::to('admin/taxonomy/' . $record['taxonomy_id']) }}}"><?php echo $taxonomies[$record['taxonomy_id']]; ?></a>
                                        <?php
                                    }
                                    ?></td>
                                <td><?php echo $record['enabled']; ?></td>
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
                                            <input  type="text" name="title[<?php echo $record['name']; ?>][<?php echo $language->code ?>]"  value="<?php if ($record[$fieldName]) echo htmlentities($record[$fieldName], ENT_QUOTES, "UTF-8"); ?>" class="form-control"></input>

                                        </div>
                                        <?php
                                    }
                                    ?>
                                </td>

                            </tr>
                            <?php
                        }
                    }
                    ?>

                </tbody>
            </table>

            {{ Form::close() }}
        </div>
    </div>

</div>
<script type="text/javascript">


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
    function add_new_field()
    {
        var template = "";
        var selectId = randomString()
        template += "<tr><td></td><td><input  type='text' class='new_field_name form-control' name='new_field_name[]'  id='name_" + selectId + "' /></td><td><select id='type_" + selectId + "' name='new_field_type[]' class='select input-large'  >";
<?php foreach ($fieldTypes as $k => $v) {
    ?>
            template += "<option value='<?php echo $k ?>'><?php echo $v ?></option>";
    <?php
}
?>
        template += "</select>";
        template += "<div id='taxonomy_div_" + selectId + "' ><br/><select class='select' name='new_field_taxonomy[]' id='taxonomy_" + selectId + "' >";
<?php foreach ($taxonomies as $k => $v) {
    ?>
            template += "<option value='<?php echo $k ?>'><?php echo $v ?></option>";
    <?php
}
?>
        template += "</select></div>";
        template += "</td><td></td><td>";
        template += "<?php
foreach ($languages as $language) {

    echo "<div class='titleinputdiv titleinputdiv_" . $language->code . "' ";
    if ($locale != $language->code) {
        echo "style='display:none'";
    }
    echo ">";
    echo "<input class='form-control'  type='text' name='new_field_title[" . $language->code . "][]'  /></div>";
}
?>";
        template += "</td></tr>";
        $('#mtlist  tr:last').after(template);

        $('#type_' + selectId).each(function() {
            var dropdownCss = new Object;
            if ($(this).attr('data-width')) {
                dropdownCss.width = $(this).attr('data-width');
            }
            var containerCss = new Object();
            containerCss.width = parseInt($(this).css('width'));
            containerCss.width = containerCss.width + 14

            $(this).select2({
                allowClear: true,
                placeholder: "Select",
                dropdownCss: dropdownCss,
                containerCss: containerCss
            });


        });
        $('#taxonomy_' + selectId).each(function() {
            var dropdownCss = new Object;
            if ($(this).attr('data-width')) {
                dropdownCss.width = $(this).attr('data-width');
            }
            var containerCss = new Object();
            containerCss.width = parseInt($(this).css('width'));
            containerCss.width = containerCss.width + 14

            $(this).select2({
                allowClear: true,
                placeholder: "Select",
                dropdownCss: dropdownCss,
                containerCss: containerCss
            });


        });
        $("#taxonomy_div_" + selectId).hide();
        $('#type_' + selectId).on("change", function() {
            var type = $(this).select2("val");
            if (type == 'tree' || type == 'multitree' || type == 'select' || type == 'multiselect') {
                $("#taxonomy_div_" + selectId).show();

            } else {
                $("#taxonomy_div_" + selectId).hide();
            }

        });
        console.log($('input[name="new_field_name[]"'))
       $('.form').bootstrapValidator('addField', $('input[name="new_field_name[]"'));//$('#name_' + selectId));
        /*$('.form').bootstrapValidator('addField', 'name_' + selectId, {selector: '#name_' + selectId, validators: {
                notEmpty: {
                },
                regexp: {
                    regexp: /^[a-zA-Z0-9_]+$/,
                    message: 'This can only consist of alphabetical, number and underscore'
                }
            }});*/
    }
</script>
<script>
    $(document).ready(function() {
        $('.form').bootstrapValidator({
            //live: 'enabled',
            container: 'tooltip',
            group: 'td',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                "new_field_name[]": {
                    selector: '.new_field_name',
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