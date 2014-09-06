@extends('admin.layouts.default')

@section('content')

<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Language</h3>
        </div>
        <div class="panel-body">

            @if($objects->count())
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Code</th>
                            <th>Site default language</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $objects as $value )
                        <tr>
                            <td> <a class="btn btn-link btn-xs" href="{{{ URL::to('admin/'.$path.'/' . $value->id) }}}">{{{ $value->title }}}</a></td>
                            <td>{{{ $value->code }}}</td>
                            <td>{{{ $value->site_default }}}</td>
                            <td>{{{ $value->created_at }}}</td>
                            <td>{{{ $value->updated_at }}}</td>
                            <td>
                               
                                            <a href="{{{ URL::to('admin/'.$path.'/' . $value->id) }}}" class="btn btn-success">
                                                <span class="glyphicon glyphicon-eye-open"></span>&nbsp;Show
                                            </a>
                                       
                                            <a href="{{{ URL::to('admin/'.$path.'/' . $value->id.'/edit') }}}" class="btn btn-info">
                                                <span class="glyphicon glyphicon-edit"></span>&nbsp;Edit
                                            </a>
                                      
                                            <a href="{{{ URL::to('admin/'.$path.'/' . $value->id.'/delete') }}}" class="btn btn-danger">
                                                <span class="glyphicon glyphicon-remove-circle"></span>&nbsp;Delete
                                            </a>
                                       
                                    </ul>
                                </div>
                            </td>
                           
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-danger">No results found</div>
            @endif
        </div>
    </div>

    <div class="pull-left">
        <ul class="pagination">
            {{{ $objects->links() }}}
        </ul>
    </div>
</div>
@stop