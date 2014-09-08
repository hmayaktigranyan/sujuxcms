@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Fields</h3>
        </div>
        <div class="panel-body">
            <ul class="nav nav-tabs" role="tablist">
                 <li ><a href="{{{ url('admin/'.$path.'/fields/'.$object->id) }}}">Fields</a</li>
                <li ><a href="{{{ url('admin/'.$path.'/fieldsorder/'.$object->id) }}}">Fields Order</a></li>
                <li class="active"><a href="#">Fields Details</a></li>
                
            </ul><br/>
            
            {{ Form::open( array( 'action' => array( 'AdminTaxonomyController@fieldsDetailsUpdate', $object->id), 'method' => 'PATCH','class'=>'form')) }}

            <center>

                <button type="submit" name="update" class='btn  btn-primary'  ><i class="icon-ok icon-white"></i> <?php echo trans('Save') ?></button>

            </center>

            <br/>
            <table class='table table-bordered table-striped table-hover' id="mtlist">
                <thead>
                    <tr>
                        <th ><?php echo(trans('Name')); ?></th>
                        <th ><?php echo(trans('Type')); ?></th>
                        <th ><?php echo(trans('Enabled')); ?></th>
                        <th><?php echo(trans('Visible in form')); ?></th>
                        <th><?php echo(trans('Visible in list')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($fieldsOrdered) {
                        foreach ($fieldsOrdered as $value) {
                            $postfix = "_".$value['name'];
                            ?>
                            <tr>

                                <td><?php echo $value['name']; ?></td>
                                 <td><?php
                                    echo $value['type'];
                                    if ($value['type'] == 'tree' || $value['type'] == 'multitree' || $value['type'] == 'select' || $value['type'] == 'multiselect') {
                                        echo " - ";
                                        ?>
                                        <a class="btn btn-link btn-xs" href="{{{ URL::to('admin/taxonomy/' . $value['taxonomy_id']) }}}"><?php echo $taxonomies[$value['taxonomy_id']]; ?></a>
                                        <?php
                                    }
                                    ?></td>
                                <td><?php echo $value['enabled']; ?></td>
                                <td>{{ Form::checkbox('visible_form'.$postfix,1,$value['visible_form'] ) }}</td>
                                <td>{{ Form::checkbox('visible_browse'.$postfix,1,$value['visible_browse'] ) }}</td>
                                
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

        $('.checkall').click(function() {
            $(".chk").prop("checked", $(".checkall").prop("checked"))
        });
    });
</script>
@stop