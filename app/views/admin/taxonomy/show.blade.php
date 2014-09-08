@extends('admin.layouts.default')

@section('content')
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{{ $object->title }}}</h3>
        </div>
        <div class="panel-body">
          <div class="pull-left">
                <div class="btn-toolbar">
                    <a href="{{{ url('admin/'.$path) }}}"  class="btn btn-primary">
                        <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
                    </a>
                    <a href="{{{ url('admin/'.$path.'/terms/'.$object->id) }}}"  class="btn btn-warning">
                        <span class="glyphicon glyphicon-th"></span>&nbsp;Terms
                    </a>
                     <a href="{{{ url('admin/'.$path.'/fields/'.$object->id) }}}"  class="btn btn-success">
                        <span class="glyphicon glyphicon-list"></span>&nbsp;Fields
                    </a>
                    <a href="{{{ url('admin/'.$path.'/'.$object->id.'/edit') }}}"  class="btn btn-info">
                        <span class="glyphicon  glyphicon-edit"></span>&nbsp;Edit
                    </a>
                    <a href="{{{ URL::to('admin/'.$path.'/' . $object->id.'/delete') }}}" class="btn btn-danger">
                        <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
                    </a>
                </div>
            </div>
            <br/><br/>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td><strong>Name</strong></td>
                        <td>{{{ $object->name }}}</td>
                    </tr>
                    <?php
                    foreach ($languages as $language) {
                        $fieldName = "title_" . $language->code;
                        ?><tr><td><?php echo $language->title; ?></td>
                            <td><?php echo $object->$fieldName; ?></td></tr>
                    <?php }
                    ?>
                    

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
