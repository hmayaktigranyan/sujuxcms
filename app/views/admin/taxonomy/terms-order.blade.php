@extends('admin.layouts.default')

@section('content')
{{ HTML::script('assets/js/jquery.nestable.js') }}
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
                <li><a href="{{{ url('admin/'.$path.'/terms/'.$object->id) }}}">Terms</a></li>
                <li  class="active"><a href="#">Terms Order</a</li>
            </ul><br/>

            {{ Form::open( array( 'action' => array( 'AdminTaxonomyController@termsOrderUpdate', $object->id), 'method' => 'PATCH')) }}

            <center>

                <button type="submit" name="update" class='btn  btn-primary'  ><i class="icon-ok icon-white"></i> <?php echo trans('Save') ?></button>


            </center>

            <div class="dd" id="nestable" style="width:100%">
                <ol class="dd-list">
                    <?php
                    $count = count($terms);
                    $levelsarray = array(-1 => 0);

                    for ($i = 0; $i < $count;) {

                        $element1 = $terms[$i];
                        $element2 = $terms[++$i];
                        $levelsarray[$level] = $element1['_id'];
                        $level = $element1['level'];
                        ?>
                        <li class="dd-item" data-id="<?php echo $element1['_id']; ?>">

                            <div class="dd-handle nestableorderbg"><?php echo $element1['title_'.$locale]; ?></div>
                            <?php
                            if ($element2['parent_id'] == $element1['_id']) {
                                $level++;
                                ?>
                                <ol class="dd-list">
                                    <?php
                                } elseif ($element2['parent_id'] == $element1['parent_id']) {
                                    ?>
                            </li>
                            <?php
                        } else {
                            $level2 = $element2['level'];
                            //$key = array_search($element2['parent_id'], $levelsarray);
                            echo str_repeat("</li></ol>", $level - $level2);

                            /* if($key !== false){
                              echo str_repeat("</li></ol>", $level-$key-1);
                              $level = $key+1;
                              } */
                        }
                        ?>

                        <?php
                    }
                    echo str_repeat("</li></ol>", $level);
                    echo "</li>";
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