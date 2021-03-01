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
    
    //Date picker for Start Date
    $('.ref_date').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true
    });


	$('#teacher_user_id').select2({
        allowClear: true,
        placeholder: 'Select Teacher',
        ajax: {
            url: "/get_all_teacher_for_payment",
            dataType: 'json',
            delay: 250,
            tags: true,
            data: function (params) {
              return {
                term: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              params.page = params.page || 1;
              // console.log(data);
              return {
                results: data,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
        }
    });



    $("#all_batch_for_teacher_payment").click(function() {
        var table = $('#teacher_payment_datatable').DataTable({
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
                    'url': "{{URL::to('/get_all_batch_for_teacher_payment')}}",
                    'data': {
                       teacher_user_id: $('select[id=teacher_user_id]').val(),
                       ref_date: $('input[id=ref_date]').val()
                    },
                },
            "columns": [
                    {"data": "batch_name", "name": "batch.name"},
                    {"data": "batch_schedule", searchable: false},
                    {"data": "total_no_students", searchable: false},
                    {"data": "no_of_paid_students", searchable: false},
                    {"data": "no_of_unpaid_students", searchable: false},
                    {"data": "total_expected_amount", searchable: false},
                    {"data": "pending_amount", searchable: false},
                    {"data": "calculated_price", searchable: false},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    let total_batch_no = 0;
                    let total_no_students = 0;
                    let no_of_paid_students = 0;
                    let no_of_unpaid_students = 0;
                    let total_expected_amount = 0;
                    let pending_amount = 0;
                    let calculated_price = 0;
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_batch_no += 1;
                        total_no_students += parseInt(aaData[i]['total_no_students'], 10);
                        no_of_paid_students += parseInt(aaData[i]['no_of_paid_students'], 10);
                        no_of_unpaid_students += parseInt(aaData[i]['no_of_unpaid_students'], 10);
                        total_expected_amount += parseInt(aaData[i]['total_expected_amount'], 10);
                        pending_amount += parseInt(aaData[i]['pending_amount'], 10);
                        if (aaData[i]['calculated_price']) {
                            calculated_price += parseInt(aaData[i]['calculated_price'], 10);
                        }
                        // else {
                        //     calculated_price += 0;
                        // }
                        // calculated_price += parseInt(aaData[i]['calculated_price'], 10);
                    }
                    $('#total_batch_no').text("Total number of Batches: " + total_batch_no);
                    $('#total_no_students').text(total_no_students);
                    $('#no_of_paid_students').text(no_of_paid_students);
                    $('#no_of_unpaid_students').text(no_of_unpaid_students);
                    $('#total_expected_amount').text(total_expected_amount + ' /-');
                    $('#pending_amount').text(pending_amount + ' /-');
                    $('#calculated_price').text(calculated_price + ' /-');

                    let payment_for = $('input[id=ref_date]').val();
                    let month = months[parseInt(payment_for.substring(3, 5)) - 1];
                    let year = parseInt(payment_for.substring(6, 10));
                    $('#month').text(month);
                    $('#year').text(year);
                    // console.log(month +"-"+year);
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
                            columns: [ 0, 1,2,3,4,5,6,7 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 0, 1,2,3,4,5,6,7 ]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 0, 1,2,3,4,5,6,7 ]
                        }
                    },
                    {
                        extend: 'print',
                        title: function(e) {
                            return document_title();
                        },
                        "footer": true,
                        exportOptions: {
                            columns: [ 0, 1,2,3,4,5,6,7 ]
                        }
                    },
                ]
        }); // #teacher_payment_datatable ends

        function document_title() {
            let payment_for = $('input[id=ref_date]').val();
            let month = months[parseInt(payment_for.substring(3, 5)) - 1];
            let year = parseInt(payment_for.substring(6, 10));
            return $('#select2-teacher_user_id-container').attr( "title" )+"\n"+", Date: "+ month + "-" + year;
        }

    });// #all_batch_for_teacher_payment ends



});
</script>


@endsection

@section('side_menu')

@endsection

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Payment Dashboard
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Payment</a></li>
        <li class="active">Teacher Payment Page</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <!-- Horizontal Form -->
    <div class="box box-primary">
        
        <div class="box-body">
        
            <div class="box-header with-border">
              <h3 class="box-title">Teacher Payment for all Batches</h3>
            </div>
            <div class="box-body">
                <div class="row">
	                <div class="col-xs-4">
	                    <div class="form-group">
	                        <label for="start_date">Refference Date</label>
	                        <div class="input-group date">
	                            <div class="input-group-addon">
	                                <i class="fa fa-calendar"></i>
	                            </div>
	                            <input id="ref_date" type="text" class="form-control ref_date" name="ref_date" value="{{ $refDate }}" autocomplete="off">
	                        </div>
	                    </div>
	                </div>
	                
                    
	                <div class="col-xs-4">
	                    <label for="batch_id" >Teacher*</label>
	                    <select class="form-control select2" name="teacher_user_id" id="teacher_user_id"></select>
                	</div>
	                <div class="col-xs-4">
	                    <label for="" ></label>
	                    <button type="submit" id="all_batch_for_teacher_payment" class="btn btn-block btn-success">Show</button>
	                </div>
                    
                    
                </div>
            </div>
        </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box-body -->

    <!-- Teacher payment Datatable -->
    <div class="box box-warning">
        <div class="box-header">
                <h4>Teacher's Payment Table</h4>      
        </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="teacher_payment_datatable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>Schedule</th>
                            <th>Total Students</th>
                            <th>Total Paid Students</th>
                            <th>Total Unpaid Students</th>
                            <th>Total Expected Amount</th>
                            <th>Pending amount/=</th>
                            <th>Paid amount/=</th>
                            <th>Action</th>                            
                        </tr>
                    </thead>
                    <tfoot>
                      <tr>
                      <th id="total_batch_no"></th>
                      <th>Total:</th>
                      <th id="total_no_students"></th>
                      <th id="no_of_paid_students"></th>
                      <th id="no_of_unpaid_students"></th>
                      <th id="total_expected_amount"></th>
                      <th id="pending_amount"></th>
                      <th id="calculated_price"></th>  
                      <th></th>
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





</section>
<!-- /.content -->

@endsection

