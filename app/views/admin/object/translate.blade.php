@extends('admin.layouts.default')

@section('content')



<div class="container-fluid">
    <div class="page-header">
        <h3>
            Add
            <div class="pull-right">
                {{ HTML::link('/admin/'.$path,'List', array('class'=>'btn btn-primary')) }}
            </div>
        </h3>
    </div>
    @include('admin/object/createedit')


</div>
@stop