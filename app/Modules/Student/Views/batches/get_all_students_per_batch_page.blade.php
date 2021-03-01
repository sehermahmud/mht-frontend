@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('../../plugins/datepicker/datepicker3.css')}}">
<!-- DataTables Printing Operation -->
<link rel="stylesheet" href="{{asset('plugins/DataTablePrint/jquery.dataTables.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/DataTablePrint/buttons.dataTables.min.css')}}">
@endsection

@section('scripts')
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/jQuery/jquery.form.min.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
<!-- bootstrap datepicker -->
<script src="{{asset('../../plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/momentjs/moment.min.js')}}"></script>
<script type="text/JavaScript" src="{{asset('plugins/JQueryPrintJS/jQuery.print.js')}}" ></script>

<!-- DataTables Printing Operation -->
<script src="{{asset('plugins/DataTablePrint/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.flash.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/jszip.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/pdfmake.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/vfs_fonts.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.print.min.js')}}"></script>
<script>
    // add the rule here
    $.validator.addMethod("valueNotEquals", function (value, element, arg) {
        return arg != value;
    }, "Value must not equal arg.");

    $(document).ready(function () {
    var month = ["January","February","March", "April",
                "May", "June","July", "August",
                "September","October","November","December"];    
    let total_student = 0;
    
    var active_students = $('#all_active_students_datatable').DataTable({
        "paging": false,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "destroy": true,
        "info": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
                'url': "{{URL::to('/students_get_all_active_students_per_batch')}}",
                'data': {
                   batch_id: "{{ $batch_id }}",
                },
            },
        "columns": [
                {"data": "student_permanent_id"},
                {"data": "student_name", "name": "students.name"},
                {"data": "school_name", "name": "schools.name"},
                {"data": "batch_type_name", "name": "batch_types.name"},
                {"data": "student_phone_number",},
                {"data": "guardian_phone_number", },
                {"data": "joining_date"},
                {"data": "last_paid_date"},
                {"data": "Link", searchable: false}

            ],
        "fnCreatedRow": function ( row, data, index ) {
            // if (data.last_paid_date !== null) {
                let human_readable_joining_date = moment(data.joining_date);
                human_readable_joining_date = month[human_readable_joining_date.month()] + " - " + human_readable_joining_date.year();
                $(row).children()[6].innerHTML = human_readable_joining_date;
                
                let human_readable_last_paid_date = moment(data.last_paid_date);
                human_readable_last_paid_date = month[human_readable_last_paid_date.month()] + " - " + human_readable_last_paid_date.year();
                $(row).children()[7].innerHTML = human_readable_last_paid_date;
                
                if (!data.payment_status) {
                    $(row).css("color", "red");
                }
            // }
        },
        dom: 'Bfrtip',
        buttons: [
                'copy',
                {
                    extend: 'csvHtml5',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Total Student : '+  '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'print',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
            ]
        
    });

    var inactive_students = $('#all_inactive_students_datatable').DataTable({
        "paging": false,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "destroy": true,
        "info": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
                'url': "{{URL::to('/students_get_all_inactive_students_per_batch')}}",
                'data': {
                   batch_id: "{{ $batch_id }}",
                },
            },
        "columns": [
                {"data": "student_permanent_id"},
                {"data": "student_name"},
                {"data": "school_name"},
                {"data": "batch_type_name"},
                {"data": "student_phone_number"},
                {"data": "guardian_phone_number"},
                {"data": "joining_date"},
                {"data": "last_paid_date"},
                {"data": "Link"}

            ],
        "fnCreatedRow": function ( row, data, index ) {
                let human_readable_joining_date = moment(data.joining_date);
                human_readable_joining_date = month[human_readable_joining_date.month()] + " - " + human_readable_joining_date.year();
                $(row).children()[6].innerHTML = human_readable_joining_date;

                let human_readable_last_paid_date = moment(data.last_paid_date);
                human_readable_last_paid_date = month[human_readable_last_paid_date.month()] + " - " + human_readable_last_paid_date.year();
                $(row).children()[7].innerHTML = human_readable_last_paid_date;
                
                if (!data.payment_status) {
                    $(row).css("color", "red");
                }
            // }
        },
        dom: 'Bfrtip',
        buttons: [
                'copy',
                {
                    extend: 'csvHtml5',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Total Student : '+  '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
                {
                    extend: 'print',
                    title: 'Total Student : '+ '{{ $total_student }}',
                    "lengthChange": true,
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5, 6 ]
                    }
                },
            ]
        
    });  
	
});
</script>


@endsection

@section('side_menu')

@endsection

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Batch Details
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"><i class="fa fa-dashboard"></i> Students</a></li>
        <li><a href="#">Batch Wise Students</a></li>
        <li class="active">Batch:{{ $batch_name }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <div class="box box-warning">
        <div class="box-header">
            <div class="row ">
                <div class="col-md-6 animated fadeIn">
                    <h3>Batch Name : <strong> {{ $batch_name }}</strong></h3>
                    <h3>Schedule : <strong> {{ $schedule }}</strong></h3>
                    <h3>Total Number of Students : <strong> {{ $total_student }}</strong></h3>                       
                </div>
                <div class="col-md-6 animated fadeIn">
                    <h3>Total Number of Paid Students(will start class in future): <strong> {{ $number_of_inactive_paid_students }}</strong></h3>
                    <h3>Total Number of Paid Students(Currently Active): <strong> {{ $number_of_active_paid_students }}</strong></h3>
                    <h3>Total Number of Unpaid Students : <strong> {{ $number_of_unpaid_students }}</strong></h3>
                </div>
            </div>        
        </div>
    </div>

    <!-- All active students Datatable -->
    <div class="box box-primary">
        <div class="box-header">
            <h3><strong>Student List (will start class in future)</strong></h3>      
        </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="all_inactive_students_datatable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Student Permanent ID</th>
                            <th>Student Name</th>
                            <th>School Name</th>
                            <th>Education Board</th>
                            <th>Student's Phone Number</th>
                            <th>Guardian's Phone Number</th>
                            <th>Joining Date</th>
                            <th>Last Paid</th>
                            <th>Action</th>
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


    <!-- All active students Datatable -->
    <div class="box box-primary">
        <div class="box-header">
            <h3><strong>Student List (currently active)</strong></h3>      
        </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="all_active_students_datatable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Student Permanent ID</th>
                            <th>Student Name</th>
                            <th>School Name</th>
                            <th>Education Board</th>
                            <th>Student's Phone Number</th>
                            <th>Guardian's Phone Number</th>
                            <th>Joining Date</th>
                            <th>Last Paid</th>
                            <th>Action</th>
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

</section>
<!-- /.content -->

@endsection

