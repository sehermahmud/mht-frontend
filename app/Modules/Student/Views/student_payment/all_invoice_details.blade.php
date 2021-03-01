@extends('master')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('scripts')
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
    $(document).ready(function () {        
        var table = $('#all_user_list').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/student/get_all_invoice_details_for_a_student_payment')}}",
                    'data': {
                       student_id: {{ $studentDetails->id }},
                    },
                },
            "columns": [
                    {"data": "invoice_master.serial_number"},
                    {"data": "batch.name"},
                    {"data": "price"},
                    {"data": "payment_from"},
                    {"data": "payment_to"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ]
        });

       
    });
</script>

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Invoice Details History
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Students</a></li>
        <li class="active">Invoice Details History</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">            

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Student name: <b>{{ $studentDetails->name }}</b></h3>
                </div>
                <div class="box-header">
                	<h3 class="box-title">Phone number: <b>{{ $studentDetails->phone_home }}</b></h3>
                </div>
                <div class="box-header">
                	<h3 class="box-title">Father's name: <b>{{ $studentDetails->fathers_name }}</b></h3>
                </div>    
                
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="all_user_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice Master Id</th>
                                <th>Batch Name</th>
                                <th>Price</th>
                                <th>payment_from</th>
                                <th>payment_to</th>
                                <th>Refund</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <!-- user list -->
                        </tbody>                        
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@endsection

