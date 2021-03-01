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

    
    // $("#daily_payment_reporting_btn").click(function() {
    function noInputDataTable(parameter) {
        let base_url = "{{ URL::to('/') }}";
        console.log(base_url + parameter.url);
        let daily_payment_reporting_table = $(parameter.table_id).DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": base_url + parameter.url,
            'columns': [
                    {"data": "serial_number"},
                    {"data": "student.name"},
                    {"data": "paid_batches"},
                    {"data": "discount_per_batch"},                    
                    {"data": "due_per_batch"},
                    {"data": "total"}
            ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    let total_price = parseFloat(0);;
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_price += parseFloat(aaData[i]['total']);
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
    }
    // });

    $("#monthly_refund_reporting").click(function() {

        /* Test Content */
        $( "#daily_reporting_message").hide('slow');
        $( "#refund_reporting_message").show('slow');
        $( "#due_reporting_message").hide('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $("#second_box_title_border").attr("class","box box-primary");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Refund Reporting</h3>");
        /* Test Content */

        $("#box_color").attr("class","box box-primary");
        $("#payment_title").html("<p><b>Refund</b> Payment Reporting</p>");
        $("#alternate_data").text("Batch Name");
        $("#batch_info").text("Payment For");
        $("#invoice_info").text("Invoice ID");
        $("#phone_num").text("Payment Date");
        $("#total_amount").text("Total Amount/-");
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
            "ajax": {
                'url': "{{URL::to('/refund_reporting')}}",
                'data': {
                   refund_statement_date: $('input[id=refund_statement_date]').val() 
                },
            },
            "columns": [
                    {"data": "invoice_detail.invoice_master.serial_number"},
                    {"data": "invoice_detail.invoice_master.student.student_permanent_id"},
                    {"data": "invoice_detail.invoice_master.student.name"},
                    {"data": "invoice_detail.invoice_master.payment_date"},                    
                    {"data": "invoice_detail.batch.name"},
                    {"data": "invoice_detail.payment_from"},
                    {"data": "amount"},
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    let total_price = parseFloat(0);;
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_price += parseFloat(aaData[i]['amount']);
                    }

                    // let nCells = nRow.getElementsByTagName('th');
                    // nCells[nCells.length-1].innerHTML = total_price;
                    $('#total_taka').text(total_price + ' /-');
                    // nCells = total_price;
                },
            dom: 'Bfrtip',
            buttons: [
                    'copy',
                    {
                        extend: 'csvHtml5',
                        title: 'Refund Reporting',
                        "footer": true
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Refund Reporting',
                        "footer": true
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Refund Reporting',
                        "footer": true
                    },
                    {
                        extend: 'print',
                        title: 'Refund Reporting',
                        "footer": true
                    }
                ]
            });
    });
    
    $("#range_payment_reporting").click(function() {
        if ($('input[id=start_date]').val() && $('input[id=end_date]').val()) {
            $("#box_color").attr("class","box box-info");
            $("#payment_title").html("<p>Payment Reporting from <b>"+$('input[id=start_date]').val()+"</b> to <b>"+$('input[id=end_date]').val()+"</b></p>");
            $("#alternate_data").text("Payment Date");
            $("#batch_info").text("Batches(Batch name, Price, Payment for)");
            $("#invoice_info").text("Invoice ID");
            $("#phone_num").text("Phone Number");
            $("#total_amount").text("Total Paid Amount/-");
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
                    'url': "{{URL::to('/payment_date_range')}}",
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
                        {"data": "paid_batches"},
                        {"data": "total"},
                    ],
                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    
                    let TotalRangePrice = parseFloat(0);
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        TotalRangePrice += parseFloat(aaData[i]['total']);
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
            $("#alternate_data").text("Batches(Batch name)");
            $("#batch_info").text("Discount");
            $("#invoice_info").text("Invoice ID");
            $("#phone_num").text("Payment For");
            $("#total_amount").text("Total Paid Amount/-");
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
                    'url': "{{URL::to('/monthly_statement')}}",
                    'data': {
                       statement_date: $('input[id=statement_date]').val() 
                    },
                },
                "columns": [
                        {"data": "invoice_master.serial_number"},
                        {"data": "invoice_master.student.student_permanent_id"},
                        {"data": "invoice_master.student.name"},
                        {"data": "payment_from"},
                        {"data": "batch.name"},
                        {"data": "discount_amount"},
                        {"data": "price"},
                    ],
                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                    // calculating total discount price per batch
                    let TotalDiscountPrice = parseFloat(0);
                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        TotalDiscountPrice += parseFloat(aaData[i]['discount_amount']);
                    }
                    $("#extra_info").text(TotalDiscountPrice + ' /-');
                    
                    // calculating total price per batch
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
        $("#invoice_info").text("Student ID");
        $("#phone_num").text("Student's Phone Number");
        $("#total_amount").text("Total Due Amount/-");
        
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
            "ajax": "{{URL::to('/get_due_reporting')}}",
            "columns": [
                    {"data": "id"},
                    {"data": "student_permanent_id"},
                    {"data": "name"},
                    {"data": "student_phone_number"},                    
                    {"data": "guardian_phone_number"},
                    {"data": "due_batches"},
                    {"data": "TotalDuePrice"},
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

    $("#monthly_due_statement").click(function() {
        if ($('input[id=due_statement_date]').val()) {
            console.log($('input[id=due_statement_date]').val());
            $("#box_color").attr("class","box box-danger");
            $("#payment_title").html("<p>Due Payment Statement for <b>"+$('input[id=due_statement_date]').val()+"</b></p>");
            $("#alternate_data").text("Payment Date");
            $("#phone_num").text("Phone Number");
            $("#total_amount").text("Total Amount/-");
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
                    'url': "{{ URL::to('/monthly_due_statement') }}",
                    'data': {
                       due_statement_date: $('input[id=due_statement_date]').val() 
                },
            },
            "columns": [
                    {"data": "id"},
                    {"data": "student_permanent_id"},
                    {"data": "name"},
                    {"data": "student_phone_number"},                    
                    {"data": "guardian_phone_number"},
                    {"data": "due_batches"},
                    {"data": "TotalDuePrice"},
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
        }
    });

    
    $("#daily_payment_reporting_btn").click(function() {
        $( "#daily_reporting_message").show('slow');
        $( "#due_reporting_message").hide('slow');

        $("#table_box_color").attr("class","box box-success");
        $("#table_payment_title").html("<p><b>Daily</b> Payment Reporting</p>");
        $("#second_box_title_border").attr("class","box box-success");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Daily Reporting</h3>");
        
        $( "#monthly_statement_div" ).hide('slow');
        $( "#monthly_refund_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        let parameter = {
            'url': '/get_daily_reporting',
            'table_id': '#daily_reporting_table',
            'columns': [
                    {"data": "serial_number"},
                    {"data": "student.name"},
                    {"data": "paid_batches"},
                    {"data": "discount_per_batch"},                    
                    {"data": "due_per_batch"},
                    {"data": "total"}
            ]
        }
        noInputDataTable(parameter);
    });

    $('#show_range_payment_reporting').click(function(e) {
        e.preventDefault();
        $( "#date_range_statement_div" ).show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide();
        // $( "#refund_reporting_message").hide();
        $( "#due_reporting_message").hide();
        $( "#monthly_refund_div" ).hide('slow');

        $("#second_box_title_border").attr("class","box box-info");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Date Range Reporting</h3>");
    });

    $('#show_refund_reporting').click(function(e) {
        e.preventDefault();
        $( "#monthly_refund_div" ).show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide('slow');
        // $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');

        $("#second_box_title_border").attr("class","box box-primary");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Refund</h3>");
    });

    $('#show_monthly_statement').click(function(e) {
        e.preventDefault();
        $( "#monthly_statement_div" ).show('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#monthly_due_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide('slow');
        // $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');
        $( "#monthly_refund_div" ).hide('slow');

        $("#second_box_title_border").attr("class","box box-warning");
        $("#second_box_title").html("<h3 class='box-title animated fadeInUp'>Mothly Statement</h3>");
    });

    $('#show_monthly_due_statement').click(function(e) {
        e.preventDefault();
        $( "#monthly_due_statement_div" ).show('slow');
        $( "#monthly_statement_div" ).hide('slow');
        $( "#date_range_statement_div" ).hide('slow');
        $( "#daily_reporting_message").hide('slow');
        // $( "#refund_reporting_message").hide('slow');
        $( "#due_reporting_message").hide('slow');
        $( "#monthly_refund_div" ).hide('slow');

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
    
    <!-- All Button Section -->
    <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Choose Reporting Option</h3>
        </div>
        
        <div class="box-body">
            <div class="row">
                <div class="col-md-2">
                    <button type="button" id="daily_payment_reporting_btn" class="btn btn-block bg-green margin btn-lg"><strong>Daily</strong> Collection</button>
                </div>
                <div class="col-md-2">
                    <button type="button" id="due_payment_reporting_btn" class="btn btn-block bg-red margin btn-lg"><strong>Due</strong> Reporting</button>
                </div>
                <div class="col-md-2">
                    <button type="submit" id="show_refund_reporting" class="btn btn-block bg-light-blue color-palette margin btn-lg"><strong>Refund</strong> Reporting</button>
                </div>
                <div class="col-md-2">
                    <button  id="show_range_payment_reporting" class="btn btn-block bg-aqua margin btn-lg"><strong>Show Date Range</strong>  Collection</button>
                </div>
                <div class="col-md-2">
                    <button  id="show_monthly_statement" class="btn btn-block bg-yellow margin btn-lg"><strong>Monthly Payment </strong> Statement</button>
                </div>
                <div class="col-md-2">
                    <button  id="show_monthly_due_statement" class="btn btn-block bg-red margin btn-lg"><strong>Monthly Due </strong> Statement</button>
                </div>

           </div>  
        </div>
    </div>
    <!-- ./ All Button Section -->


    <!-- Payment Method Messages and necessary Input box Section -->
    <div id="second_box_title_border" class="box box-primary">
        <div id="second_box_title" class="box-header with-border">
          <h3 class="box-title animated fadeInUp">Choose Reporting Option</h3>
        </div>
        
        <div class="box-body second_content_section">
            
            <h3 id="daily_reporting_message"   style="display: none;">Showing Daily Payment Reporting</h3>
            <h3 id="due_reporting_message"   style="display: none;">Showing Due Payment Reporting</h3>
            
            <div id="monthly_refund_div" class="row" style="display: none;">
                <div class="col-xs-6">
                    <div class="form-group">
                        <label for="refund_statement_date">Month (For Monthly Refund Statement)</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="refund_statement_date" type="text" class="form-control ref_date" name="refund_statement_date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="" ></label>
                    <button type="submit" id="monthly_refund_reporting" class="btn btn-block btn-primary"><strong>Monthly Refund </strong> Statement</button>
                </div>
            </div>
            

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
   <!-- ./ Payment Method Messages and necessary Input box Section -->

    <div id="table_box_color" class="box box-primary">
            <div class="box-header">
                <h3 class="box-title" id="table_payment_title">Payment Reporting</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

            	<!-- Daily Reporting Table -->
                <table id='daily_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Invoice ID</th>
	                        <th>Student Name</th>
	                        <th>Batches(name, price, payment for)</th>
	                        <th>Discount</th>
	                        <th>Pending</th>
	                        <th>Paid Amount /-</th>
	                    </tr>
	                </thead>
	                <tfoot>
	                    <tr>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th id="total_daily_pending"></th>
	                        <th id="total_daily_discount"></th>
	                        <th id="total_daily_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Daily Reporting Rows -->
	                </tbody>
                </table>
                
                <!-- Due Reporting Table -->
                <table id='due_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Student Name</th>
	                        <th>Student Phone no.</th>
	                        <th>Guardian's Phone no.</th>
	                        <th>Batches(name, price, Last Paid Date)</th>
	                        <th>Total Due /-</th>
	                    </tr>
	                </thead>
	                <tfoot>
	                    <tr>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th id="total_due_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Due Reporting Rows -->
	                </tbody>
                </table>
                
                <!-- Refund Reporting Table -->
                <table id='monthly_refund_reporting' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Invoice ID</th>
	                        <th>Refund ID</th>
	                        <th>Student Name</th>
	                        <th>Invoice Details(name, price, payment for)</th>
	                        <th>Paid Amount /-</th>
	                    </tr>
	                </thead>
	                <tfoot>
	                    <tr>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th id="total_refund_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Refund Reporting Rows -->
	                </tbody>
                </table>

                <!-- Date Range Reporting Table -->
                <table id='date_range_reporting_table' class='table table-bordered table-striped' style="display: none;">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Student Name</th>
                            <th>Batches(name, price, payment for)</th>
                            <th>Discount</th>
                            <th>Pending</th>
                            <th>Paid Amount /-</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="total_range_pending"></th>
                            <th id="total_range_discount"></th>
                            <th id="total_range_amount"></th> 
                        </tr>
                    </tfoot>                
                    <tbody>
                        <!-- Date Range Reporting Rows -->
                    </tbody>
                </table>
                
                <!-- Monthly Reporting Table -->
                <table id='monthly_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Invoice ID</th>
	                        <th>Student Name</th>
	                        <th>Payment For</th>
	                        <th>Batch name</th>
	                        <th>Discount</th>
	                        <th>Pending</th>
	                        <th>Paid Amount /-</th>
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
	                        <th id="total_monthly_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Monthly Reporting Rows -->
	                </tbody>
                </table>
                
                <!-- Monthly Due Reporting Table -->
                <table id='due_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Student Name</th>
	                        <th>Student Phone no.</th>
	                        <th>Guardian's Phone no.</th>
	                        <th>Batches(name, price, Last Paid Date)</th>
	                        <th>Total Due /-</th>
	                    </tr>
	                </thead>
	                <tfoot>
	                    <tr>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th id="total_monthly_due_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Monthly Due Reporting Rows -->
	                </tbody>
                </table>
            </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box -->


</section>
<!-- /.content -->

@endsection

