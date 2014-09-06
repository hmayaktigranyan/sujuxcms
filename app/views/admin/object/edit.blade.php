@extends('admin.layouts.default')

@section('content')



<div class="container-fluid">
    <div class="page-header">
        <h3>Update</h3>
    </div>
    <div class="pull-left">
        <div class="btn-toolbar">
            <a href="{{{ url('admin/'.$path.'/'.$entity->id) }}}"  class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
            </a>
            <a href="{{{ url('admin/'.$path.'/show/'.$object->id) }}}"  class="btn btn-success">
                <span class="glyphicon  glyphicon-eye-open"></span>&nbsp;Show
            </a>
            <a href="{{{ URL::to('admin/'.$path.'/delete/' . $object->id) }}}" class="btn btn-danger">
                <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
            </a>
        </div>
    </div><br/><br/>
    @include('admin/object/createedit')
    @yield('subcontent')
</div>
@stop