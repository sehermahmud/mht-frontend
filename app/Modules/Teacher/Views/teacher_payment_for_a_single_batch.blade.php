@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('../../plugins/datepicker/datepicker3.css')}}">
<!-- DataTables Printing Operation -->
<link rel="stylesheet" href="{{asset('plugins/DataTablePrint/jquery.dataTables.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/DataTablePrint/buttons.dataTables.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
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
<!-- <script src="http://www.position-absolute.com/creation/print/jquery.printPage.js" ></script> -->
<!-- <script src="{{asset('plugins/jqueryPrintArea/jquery.PrintArea.js')}}" ></script> -->
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
	var months = ["January","February","March", "April",
                "May", "June","July", "August",
                "September","October","November","December"];   
	var paid_table = $('#paid_students').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "destroy": true,
        "info": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
                'url': "{{URL::to('/get_paid_students_for_a_batch')}}",
                'data': {
                   batch_id: "{{ $batchID }}",
                   ref_date: "{{ $refDate }}"
                },
            },
        "columns": [
                {"data": "name", "name": "students.name"},
                {"data": "name", "name": "students.name"},
                {"data": "student_phone_number"},
                {"data": "joining_date"},
                {"data": "paid_money", "searchable": false}
            ],
        "fnCreatedRow": function ( row, data, index ) {
            // if (data.last_paid_date !== null) {
                let human_readable_joining_date = moment(data.joining_date);
                human_readable_joining_date = months[human_readable_joining_date.month()] + " - " + human_readable_joining_date.year();
                $(row).children()[3].innerHTML = human_readable_joining_date;
        },
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    var total_price = 0;
                    let total_paid_student_no = 0;
                    for ( var i=0 ; i<aaData.length ; i++ ) {
                        total_price += aaData[i]['paid_money'];
                        total_paid_student_no += 1;
                    }
                    $('#total_paid_money').text(total_price);
                    $('#total_paid_student_no').text("Total no. of  Students : " + total_paid_student_no); 
                },
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
                var index = iDisplayIndex +1;
                $('td:eq(0)',nRow).html(index);
                return nRow;
        },
        dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [  1,2,4 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [  1,2,4 ]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 1,2,4 ]
                        }
                    },
                    {
                        extend: 'print',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 1,2,4 ]
                        }
                    },
            ]
        }); // #teacher_payment_datatable ends
    


	var non_paid_table = $('#non_paid_students').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "destroy": true,
        "info": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
                'url': "{{URL::to('/get_non_paid_students_for_a_batch')}}",
                'data': {
                   batch_id: "{{ $batchID }}",
                   ref_date: "{{ $refDate }}"
                },
            },
        "columns": [
                {"data": "name", "name": "students.name"},
                {"data": "name", "name": "students.name"},
                {"data": "student_phone_number"},
                {"data": "joining_date"},
                {"data": "price", searchable: false }
        ],
        "fnCreatedRow": function ( row, data, index ) {
            // if (data.joining_date !== null) {
                let human_readable_joining_date = moment(data.joining_date);
                human_readable_joining_date = months[human_readable_joining_date.month()] + " - " + human_readable_joining_date.year();
                $(row).children()[3].innerHTML = human_readable_joining_date;
        },
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
                var index = iDisplayIndex +1;
                $('td:eq(0)',nRow).html(index);
                return nRow;
        },
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                let total_non_paid_student_no = 0;
                for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_non_paid_student_no += 1;
                }
                $('#total_non_paid_student_no').text("Total Student: " + total_non_paid_student_no); 
        },
        dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [  1,2,4 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [  1,2,4 ]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 1,2,4 ]
                        }
                    },
                    {
                        extend: 'print',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 1,2,4 ]
                        }
                    },
            ]
    });
    function document_title() {
        let months = ["January","February","March", "April",
            "May", "June","July", "August",
            "September","October","November","December"];
        let payment_for = $('input[id=ref_date]').val();
        let batchName = "{{ $batchName }}";
        let date = "{{ $refDate }}";
        let month = months[parseInt(date.substring(5, 7)) - 1];
        let year = parseInt(date.substring(0, 4));
        return batchName + ", " + month + "-" + year;
    }
});
</script>


@endsection

@section('side_menu')

@endsection

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
<div class="box box-info">
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Payment</a></li>
        <li class="active">Student Payment Page</li>
    </ol>
    
    <h3>
       &nbsp;&nbsp;Batch : <b>{{ $batchName }}</b>
    </h3>
    <h3>
        &nbsp;&nbsp;For Month : <b>{{ $refDate }}</b>
    </h3>
        <br>
</div>
</section>

<!-- Main content -->
<section class="content">
    
    <div class="form-group">
        <div class="col-md-6">
        	<!-- Horizontal Form -->
            <div class="box box-success">
                    <div class="box-header">
                        <h4>
                            Paid Students
                        </h4>            
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="paid_students" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Student Name</th>
                                    <th>Student's Phone Number</th>
                                    <th>Joining Date</th>
                                    <th>Paid Price</th>
                                </tr>
                            </thead>
                            <tfoot>
                                  <tr>
                                    <th></th>
                                    <th id="total_paid_student_no"></th>
                                    <th></th> 
                                    <th>Total:</th>
                                    <th id="total_paid_money"></th>
                                  </tr>
                            </tfoot>
                            <tbody>                            
                                <!-- user list -->
                            </tbody>                        
                        </table>
                    </div>
                    <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6">
            	<!-- Horizontal Form -->
            <div class="box box-danger col-md-6">
                    <div class="box-header">
                        <h4>
                            Not Paid Students
                        </h4>            
                    </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="non_paid_students" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Student Name</th>
                                        <th>Student's Phone Number</th>
                                        <th>Joining Date</th>
                                        <th>Paid Price</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                  <tr>
                                    <th></th>
                                    <th id="total_non_paid_student_no"></th> 
                                    <th></th>
                                    <th>Total:</th>
                                    <th>0</th>
                                  </tr>
                                </tfoot>
                                <tbody>                            
                                    <!-- user list -->
                                </tbody>                        
                            </table>
                        </div>
                        <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>




</section>
<!-- /.content -->

@endsection

