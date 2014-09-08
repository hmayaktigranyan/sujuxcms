@extends('admin.layouts.default')

@section('content')
<?php
$locale = App::getLocale();
?>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body">
            <div class="pull-left">
                <div class="btn-toolbar">
                    <a href="{{{ url('admin/'.$path.'/terms/'.$taxonomy->id) }}}"  class="btn btn-primary">
                        <span class="glyphicon glyphicon-th-list"></span>&nbsp;Terms
                    </a>
                    <a href="{{{ url('admin/'.$path.'/termedit/'.$object->id) }}}"  class="btn btn-success">
                        <span class="glyphicon  glyphicon-edit"></span>&nbsp;Edit
                    </a>
                    <a href="{{{ URL::to('admin/'.$path.'/termdelete/' . $object->id) }}}" class="btn btn-danger">
                        <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
                    </a>
                </div>
            </div><br/><br/>
            <table class="table table-striped">
                <tbody>
                    <?php
                    foreach ($fields as $field) {
                        $fieldName = $field['name'];
                        $fieldType = $field['type'];
                        $fieldTitle = $field['title_' . $locale];
                        ?>
                        <tr>
                            <td><strong><?php echo $fieldTitle ?></strong></td>
                            <td><?php
                                if ($fieldType == 'tree' || $fieldType == 'select') {
                                    $fieldVal = $object[$fieldName];
                                    if (is_array($fieldVal)) {
                                        $fieldVal = array_shift($fieldVal);
                                    }
                                    echo $taxonomyTerms[$field['taxonomy_id']][$fieldVal];
                                } elseif ($fieldType == 'multitree' || $fieldType == 'multiselect') {
                                    $fieldVals = $object[$fieldName];

                                    if (!is_array($fieldVals)) {
                                        $fieldVals = array($fieldVals);
                                    }
                                    $fieldVals2 = array();
                                    foreach ($fieldVals as $fieldVal) {
                                        $fieldVals2[] = $taxonomyTerms[$field['taxonomy_id']][$fieldVal];
                                    }
                                    echo implode(", ", $fieldVals2);
                                } elseif ($fieldType == 'date') {
                                    if ($object[$fieldName]) {
                                        echo $object[$fieldName]->format('Y-m-d');
                                    }
                                } elseif ($fieldType == 'checkbox') {

                                    if ($object[$fieldName]) {
                                        echo trans('Yes');
                                    } else {
                                        echo trans('No');
                                    }
                                } elseif ($fieldType == 'file' || $fieldType == 'image') {
                                    if ($object[$fieldName]) {
                                         $pathinfo = pathinfo($object[$fieldName]);
                                        echo "<a href='" . $object[$fieldName] . "' target='_blank'>" . $pathinfo['basename']. "</a>";
                                    }
                                } else {
                                    echo $object[$fieldName];
                                }
                                ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
