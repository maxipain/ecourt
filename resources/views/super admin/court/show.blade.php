@extends('super admin.layouts.app')
@section('headSection')
    <link rel="stylesheet" href="{{ asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection
@section('main-content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                courts list
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">courts</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="box">
                <div class="box-header">
                    <a href="{{ route('courts.create') }}" class=" btn btn-success">Add new</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    @include('includes.messages')
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>S.no</th>
                            <th>court name</th>
                            <th>Location</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($courts as $court)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $court->name }}</td>
                                <td>{{ $court->location }}</td>
                                <td><a href="{{ route('courts.edit',$court->id) }}"><span class="glyphicon glyphicon-edit"></span></a></td>
                                <td>
                                    <form id="delete-form-{{ $court->id }}" action="{{ route('courts.destroy',$court->id) }}" style="display: none;" method="post">
                                        {{@csrf_field()}}
                                        {{@method_field('DELETE')}}
                                    </form>
                                    <a href="" onclick="
                                            if(confirm('Are you sure you want to delete this court?'))
                                            {event.preventDefault();
                                            document.getElementById('delete-form-{{ $court->id }}').submit();
                                            }
                                            else{
                                            event.preventDefault();
                                            }
                                            "><span class="glyphicon glyphicon-trash"></span></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>S.no</th>
                            <th>court name</th>
                            <th>Location</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>

@section('footerSection')
    <script src="{{ asset('admin/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(function () {
            $('#example1').DataTable()
            $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
            })
        })
    </script>
@endsection
@endsection
