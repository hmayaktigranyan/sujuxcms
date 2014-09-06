@extends('admin.layouts.default')

@section('content')

<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Entity</h3>
        </div>
        <div class="panel-body">

            @if($objects->count())
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Name</th>
                            <th>Fields</th>
                            <th>Languages</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $objects as $value )
                        <tr>
                            <td> <a class="btn btn-link btn-xs" href="{{{ URL::to('admin/'.$path.'/' . $value->id) }}}">{{{ $value->title }}}</a></td>
                            <td>{{{ $value->name }}}</td>
                            <td> <a class="btn btn-link btn-xs" href="{{{ URL::to('admin/'.$path.'/fields/' . $value->id) }}}">Fields</a></td>
                            <td><?php
                                $fieldVals = $value->languages;

                                if (!is_array($fieldVals)) {
                                    $fieldVals = array($fieldVals);
                                }
                                $fieldVals2 = array();
                                foreach ($fieldVals as $fieldVal) {
                                    $fieldVals2[] = $languagesByKey[$fieldVal];
                                }
                                echo implode(", ", $fieldVals2);
                                ?></td>
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

</div>
@stop