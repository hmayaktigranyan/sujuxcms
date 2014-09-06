@extends('admin.layouts.default')

@section('content')
<div class="container">
    {{ Form::open( array( 'action' => array( 'AdminObjectController@destroy', $object->id ) ) ) }}
    {{ Form::hidden( '_method', 'DELETE' ) }}
    <div class="alert alert-danger">
        <div class="pull-left"><b> Be Careful!</b> Are you sure you want to delete  ?
        </div>
        <div class="pull-right">
            {{ Form::submit( 'Yes', array( 'class' => 'btn btn-danger' ) ) }}
            {{ link_to( URL::previous(), 'No', array( 'class' => 'btn btn-primary' ) ) }}
        </div>
        <div class="clearfix"></div>
    </div>
    {{ Form::close() }}
</div>
@stop