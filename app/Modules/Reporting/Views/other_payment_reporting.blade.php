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

<!-- DataTables Printing Operation -->
<script src="{{asset('plugins/DataTablePrint/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.flash.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/jszip.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/pdfmake.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/vfs_fonts.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/DataTablePrint/buttons.print.min.js')}}"></script>


<script type="text/JavaScript" src="{{asset('plugins/JQueryPrintJS/jQuery.print.js')}}" ></script>
<script>

    var monthNames = ["January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];    

    // add the rule here
    $.validator.addMethod("valueNotEquals", function (value, element, arg) {
        return arg != value;
    }, "Value must not equal arg.");

    $(document).ready(function () {

    
    
    //Date picker for Start Date
    $('.ref_date').datepicker({

      format: 'dd/mm/yyyy',
      autoclose: true
    });

    var daily_payment_reporting_table = "";
    $("#daily_payment_reporting").click(function() {
        
        /* Test Content */
        $( "#daily_reporting_message").show('slow');
        $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $("#second_box_title_border").attr("class","box box-success");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Daily Reporting</h3>");
        /* Test Content */
        
        
        $("#box_color").attr("class","box box-success");
        $("#payment_title").html("<p><b>Daily</b> Payment Reporting</p>");

        $("#invoice_info").text("Invoice ID");
        $("#payment_date_msg").text("Payment Date");
        $("#payment_type_msg").text("Payment Type");
        $("#payment_description_msg").text("Description");
        $("#total_taka_msg").text("Total Amount/-");


        var daily_payment_reporting_table = $('#all_user_list').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{URL::to('/get_other_daily_reporting')}}",
            "columns": [
                    {"data": "serial_number"},
                    {"data": "student.student_permanent_id"},
                    {"data": "student.name"},
                    {"data": "student.student_phone_number"},                    
                    {"data": "payment_date"},
                    {"data": "other_payment_type.description"},
                    {"data": "note"},
                    {"data": "price"},
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    let total_price = parseFloat(0);;
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_price += parseFloat(aaData[i]['price']);
                    }

                    $('#total_taka').text(total_price + ' /-');
                },
            dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: 'DailyPaymentReporting',
                        "footer": true
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'DailyPaymentReporting',
                        "footer": true
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'DailyPaymentReporting',
                        "footer": true
                    },
                    {
                        extend: 'print',
                        title: 'Daily Payment Report',
                        "footer": true
                    }
                ]
            });
    });

    
    
    $("#range_payment_reporting").click(function() {
        if ($('input[id=start_date]').val() && $('input[id=end_date]').val()) {
            $("#box_color").attr("class","box box-info");
            $("#payment_title").html("<p>Payment Reporting from <b>"+$('input[id=start_date]').val()+"</b> to <b>"+$('input[id=end_date]').val()+"</b></p>");

            $("#invoice_info").text("Invoice ID");
            $("#payment_date_msg").text("Payment Date");
            $("#payment_type_msg").text("Payment Type");
            $("#payment_description_msg").text("Description");
            $("#total_taka_msg").text("Total Amount/-");

            var table = $('#all_user_list').DataTable({
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
                    'url': "{{URL::to('/get_other_payment_date_range')}}",
                    'data': {
                       start_date: $('input[id=start_date]').val(),
                       end_date: $('input[id=end_date]').val() 
                    },
                },
                "columns": [
                        {"data": "serial_number"},
                        {"data": "student.student_permanent_id"},
                        {"data": "student.name"},
                        {"data": "student.student_phone_number"},                    
                        {"data": "payment_date"},
                        {"data": "other_payment_type.description"},
                        {"data": "note"},
                        {"data": "price"},
                    ],
                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    
                    let TotalRangePrice = parseFloat(0);
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        TotalRangePrice += parseFloat(aaData[i]['price']);
                    }

                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[nCells.length-1].innerHTML = TotalRangePrice;
                    $('#total_taka').text(TotalRangePrice + ' /-');
                },
                dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: 'Payment Report from '+$('input[id=start_date]').val()+' to '+ $('input[id=end_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Payment Report from '+$('input[id=start_date]').val()+' to '+ $('input[id=end_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Payment Report from '+$('input[id=start_date]').val()+' to '+ $('input[id=end_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'print',
                        title: 'Payment Report from '+$('input[id=start_date]').val()+' to '+ $('input[id=end_date]').val(),
                        "footer": true
                    }
                ]
            });
        }

    });

    $("#monthly_statement").click(function() {
        if ($('input[id=statement_date]').val()) {
            $("#box_color").attr("class","box box-warning");
            $("#payment_title").html("<p>Monthly Statement for <b>"+$('input[id=statement_date]').val()+"</b></p>");
            $("#alternate_data").text("Payment For");
            
            $("#invoice_info").text("Invoice ID");
            $("#payment_date_msg").text("Payment Date");
            $("#payment_type_msg").text("Payment Type");
            $("#payment_description_msg").text("Description");
            $("#total_taka_msg").text("Total Amount/-");
            
            var table = $('#all_user_list').DataTable({
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
                    'url': "{{URL::to('/get_other_monthly_statement')}}",
                    'data': {
                       statement_date: $('input[id=statement_date]').val() 
                    },
                },
                "columns": [
                        {"data": "serial_number"},
                        {"data": "student.student_permanent_id"},
                        {"data": "student.name"},
                        {"data": "student.student_phone_number"},                    
                        {"data": "payment_date"},
                        {"data": "other_payment_type.description"},
                        {"data": "note"},
                        {"data": "price"},
                    ],
                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    
                    let TotalRangePrice = parseFloat(0);
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        TotalRangePrice += parseFloat(aaData[i]['price']);
                    }

                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[nCells.length-1].innerHTML = TotalRangePrice;
                    $('#total_taka').text(TotalRangePrice + ' /-');
                },
                dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: 'Monthly Payment Statement for '+$('input[id=statement_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Monthly Payment Statement for '+$('input[id=statement_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Monthly Payment Statement for '+$('input[id=statement_date]').val(),
                        "footer": true
                    },
                    {
                        extend: 'print',
                        title: 'Monthly Payment Statement for '+$('input[id=start_date]').val()+' to '+ $('input[id=end_date]').val(),
                        "footer": true
                    }
                ]
            });
        }

    });

    $("#due_payment_reporting").click(function() {

        /* Test Content */
        $( "#daily_reporting_message").hide('slow');
        $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $("#second_box_title_border").attr("class","box box-danger");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Due Reporting</h3>");
        /* Test Content */

        $("#box_color").attr("class","box box-danger");
        $("#payment_title").html("<p><b>Due</b> Payment Reporting</p>");
        $("#alternate_data").text("Guardian's Phone Number");
        $("#batch_info").text("Batches(Batch name, Price, Last Paid Date)");
        
        $("#phone_num").text("Student's Phone Number");
        $("#total_amount").text("Total Due Amount/-");

        $("#invoice_info").text("Driving License Number");
        $("#payment_date_msg").text("Guardian's Phone Number");
        $("#payment_type_msg").text("Email Address");
        $("#payment_description_msg").text("Father's Name");
        $("#total_taka_msg").text("School Name");

        
        var table = $('#all_user_list').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{URL::to('/get_other_due_reporting')}}",
            "columns": [
                    {"data": "driving_license_number"},
                    {"data": "student_permanent_id"},
                    {"data": "name"},
                    {"data": "student_phone_number"},                    
                    {"data": "guardian_phone_number"},
                    {"data": "student_email"},
                    {"data": "fathers_name"},
                    {"data": "school.name"},
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    
                    let TotalDuePrice = parseFloat(0);
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        TotalDuePrice += parseFloat(aaData[i]['TotalDuePrice']);
                    }

                    let nCells = nRow.getElementsByTagName('th');
                    nCells[nCells.length-1].innerHTML = TotalDuePrice + ' /-';
                },
            dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: 'DuePaymentReporting',
                        "footer": true
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'DuePaymentReporting',
                        "footer": true
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Due Payment Report',
                        "footer": true
                    },
                    {
                        extend: 'print',
                        title: 'Due Payment Report',
                        "footer": true
                    }
                ]

            });

    });

    

    /* Test content */
    $('#show_range_payment_reporting').click(function(e) {
        e.preventDefault();
        $( "#date_range_statement_div" ).show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide();
        $( "#refund_reporting_message").hide();
        $( "#due_reporting_message").hide();

        $("#second_box_title_border").attr("class","box box-info");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Date Range Reporting</h3>");
    });

    $('#show_monthly_statement').click(function(e) {
        e.preventDefault();
        $( "#monthly_statement_div" ).show('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide('slow');
        $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');

        $("#second_box_title_border").attr("class","box box-warning");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Mothly Statement</h3>");
    });

    $('#show_monthly_due_statement').click(function(e) {
        e.preventDefault();
        $( "#monthly_due_statement_div" ).show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide('slow');
        $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');

        $("#second_box_title_border").attr("class","box box-danger");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Mothly Due Statement</h3>");
    });
    /* Test content */



});
</script>


@endsection

@section('side_menu')

@endsection

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Payment Reporting Dashboard
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Reporting > </a></li>
        <li class="active">Payment Reporting Page</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <!-- Test content -->
    <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Choose Reporting Option</h3>
        </div>
        
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <button type="submit" id="daily_payment_reporting" class="btn btn-block bg-green margin btn-lg"><strong>Daily</strong> Collection</button>
                </div>
                <div class="col-md-3">
                    <button type="submit" id="due_payment_reporting" class="btn btn-block bg-red margin btn-lg"><strong>Due</strong> Reporting</button>
                </div>
                <div class="col-md-3">
                    <button  id="show_range_payment_reporting" class="btn btn-block bg-aqua margin btn-lg"><strong>Show Date Range</strong>  Collection</button>
                </div>
                <div class="col-md-3">
                    <button  id="show_monthly_statement" class="btn btn-block bg-yellow margin btn-lg"><strong>Monthly Payment </strong> Statement</button>
                </div>
            </div>  
        </div>
        
    </div>
    <div id="second_box_title_border" class="box box-primary">
        <div id="second_box_title" class="box-header with-border">
          <h3 class="box-title animated fadeInUp">Choose Reporting Option</h3>
        </div>
        
        <div class="box-body second_content_section">


            <h3 id="daily_reporting_message"   style="display: none;">Showing Daily Payment Reporting</h3>
            <h3 id="refund_reporting_message"   style="display: none;">Showing Refund Payment Reporting</h3>
            <h3 id="due_reporting_message"   style="display: none;">Showing Due Payment Reporting</h3>

            <div id="date_range_statement_div" class="row" style="display: none;">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="start_date">From</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="start_date" type="text" class="form-control ref_date" name="start_date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="end_date">To</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="end_date" type="text" class="form-control ref_date" name="end_date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <label for="" ></label>
                    <button type="submit" id="range_payment_reporting" class="btn btn-block btn-info"><strong>Show Date Range</strong>  Collection</button>
                </div>

            </div>
            <div id="monthly_statement_div" class="row" style="display: none;">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="statement_date">Month (For Monthly Payment Statement)</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="statement_date" type="text" class="form-control ref_date" name="statement_date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="" ></label>
                    <button type="submit" id="monthly_statement" class="btn btn-block btn-warning"><strong>Monthly Payment </strong> Statement</button>
                </div>
            </div>

            <div id="monthly_due_statement_div" class="row" style="display: none;">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="due_statement_date">Month (For Monthly Due Statement)</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="due_statement_date" type="text" class="form-control ref_date" name="due_statement_date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="" ></label>
                    <button type="submit" id="monthly_due_statement" class="btn btn-block btn-danger"><strong>Monthly Due </strong> Statement</button>
                </div>
            </div>
        </div>
        
    </div>
    <!-- Test content -->

    <div id="box_color" class="box box-primary">
            <div class="box-header">
                <h3 class="box-title" id="payment_title">Payment Reporting</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id='all_user_list' class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th id="invoice_info">Invoice ID</th>
                        <th>Student Permanent ID</th>
                        <th>Student Name</th>
                        <th>Phone Number</th>
                        <th id="payment_date_msg">Payment Date</th>
                        <th id="payment_type_msg">Payment Type</th>
                        <th id="payment_description_msg">Description</th>
                        <th id="total_taka_msg">Total Amount/-</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total:</th>
                        <th id="total_taka"></th> 
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

