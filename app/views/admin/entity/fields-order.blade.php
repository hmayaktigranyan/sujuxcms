@extends('admin.layouts.default')

@section('content')
{{ HTML::script('assets/js/jquery.nestable.js') }}
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
                <li ><a href="{{{ url('admin/'.$path.'/fields/'.$object->id) }}}">Fields</a</li>
                <li class="active"><a href="#">Fields Order</a></li>
                <li><a href="{{{ url('admin/'.$path.'/fieldsdetails/'.$object->id) }}}">Fields Details</a></li>

            </ul><br/>
            {{ Form::open( array( 'action' => array( 'AdminEntityController@fieldsOrderUpdate', $object->id), 'method' => 'PATCH')) }}

            <center>

                <button type="submit" name="update" class='btn  btn-primary'  ><i class="icon-ok icon-white"></i> <?php echo trans('Save') ?></button>


            </center>

            <div class="dd" id="nestable" style="width:100%">
                <ol class="dd-list">
                    <?php
                    foreach ($fieldsOrdered as $field) {
                        ?>
                        <li class="dd-item" data-id="<?php echo $field['name']; ?>">

                            <div class="dd-handle nestableorderbg"><?php echo $field['title_' . $locale]; ?></div>

                        </li>


                        <?php
                    }
                    ?>
                </ol>
            </div>
            <div style="clear:both;"></div> 

            <input type="hidden" name="itemsorder" id="itemsorder" value=""/>
            <br/>
            <script>

                $(document).ready(function()
                {
                    var updateHidden = function(e)
                    {

                        if (window.JSON) {
                            $('#itemsorder').val(window.JSON.stringify($('#nestable').nestable('serialize')));//, null, 2));
                        }
                    };
                    $('#nestable').nestable({
                        maxDepth: 100,
                        group: 1
                    }).on('change', updateHidden);

                    updateHidden($('#nestable').data('output', $('#itemsorder')));

                });
            </script>

            {{ Form::close() }}
        </div>
    </div>

</div>

@stop