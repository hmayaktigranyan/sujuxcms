@extends('admin.layouts.default')

@section('content')



<div class="container-fluid">
    <div class="page-header">
        <h3>Create</h3>
    </div>
    <div class="pull-left">
        <div class="btn-toolbar">
            <a href="{{{ url('admin/'.$path.'/'.$entity->id) }}}"  class="btn btn-primary">
                <span class="glyphicon glyphicon-th-list"></span>&nbsp;List
            </a>
        </div>
    </div>
    <br/><br/>
    @include('admin/object/createedit')
    @yield('subcontent')

</div>
@stop