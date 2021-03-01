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

    $("#daily_payment_reporting").click(function() {
        hide_tables();
    	$("#daily_reporting_table").show();
        hide_divs();
        set_attributes("box box-success", "<h3 class='box-title animated fadeInUp'>Daily Reporting</h3>", "box box-success", 
        				"<h3 class='box-title animated fadeInUp'>Daily Reporting</h3>", "Showing Daily Payment Reporting");
        var daily_payment_reporting_table = $('#daily_reporting_table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{URL::to('/get_daily_reporting')}}",
            "columns": [
                    {"data": "serial_number"},
                    {"data": "student.name"},
                    {"data": "paid_batches"},
                    {"data": "discount_per_batch"},
                    {"data": "due_per_batch"},                    
                    {"data": "total"},
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            		let total_daily_discount = parseFloat(0);
                    let total_daily_pending = parseFloat(0);
                    let total_price = parseFloat(0);

                    for ( let i=0 ; i<aaData.length ; i++ ) {
                        total_daily_discount += parseFloat(aaData[i]['invoice_detail'][0]['discount_amount']);
                        total_daily_pending += parseFloat(aaData[i]['invoice_detail'][0]['due_amount']);
                        total_price += parseFloat(aaData[i]['total']);
                    }
					$('#total_daily_discount').text(total_daily_discount + ' /-');
                    $('#total_daily_pending').text(total_daily_pending + ' /-');
                    $('#total_daily_amount').text(total_price + ' /-');
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

   $("#due_payment_reporting").click(function() {
		hide_tables();
    	$("#due_reporting_table").show();
        hide_divs();
        set_attributes("box box-danger", "<h3 class='box-title animated fadeInUp'>Due Reporting</h3>", "box box-danger", 
        				"<h3 class='box-title animated fadeInUp'>Due Reporting</h3>", "Showing Due Payment Reporting");
        let due_payment_reporting = $('#due_reporting_table').DataTable({
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
                    $('#total_due_amount').text(TotalDuePrice + ' /-');
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

   $("#show_monthly_due_statement").click(function() {
   		hide_divs();
   		$("#monthly_due_statement_div").show('slow');
   		set_attributes("", "", "box box-danger", "<h3 class='box-title animated fadeInUp'>Mothly Due Reporting</h3>","");
   		$("#btn_monthly_due_statement").click(function() {
   			if ($('input[id=due_statement_date]').val()) {
	   			set_attributes("box box-danger", 
				   				"<h3 class='box-title animated fadeInUp'><p>Due Payment Statement for <b>"+$('input[id=due_statement_date]').val()+"</b></p></h3>", 
				            	"box box-danger", 
				            	"<h3 class='box-title animated fadeInUp'><p>Due Payment Statement for <b>"+$('input[id=due_statement_date]').val()+"</b></p></h3>", 
				            	"");
	            hide_tables();
		    $("#monthly_due_reporting_table").show();
		        let monthly_due_reporting_table = $('#monthly_due_reporting_table').DataTable({
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
	                    $('#total_monthly_due_amount').text(TotalDuePrice + ' /-');
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
   	});

   $("#show_range_payment_reporting").click(function() {
   		hide_divs();
   		$("#date_range_statement_div").show('slow');
   		set_attributes("", "", "box box-info", "<h3 class='box-title animated fadeInUp'>Date Range Reporting</h3>","");
   		$("#btn_range_payment_reporting").click(function() {
	   		if ($('input[id=start_date]').val() && $('input[id=end_date]').val()) {
	            set_attributes("box box-info", 
	            			"<h3 class='box-title animated fadeInUp'>Payment Reporting from <b>" +$('input[id=start_date]').val()+"</b> to <b>"+$('input[id=end_date]').val() + "</b></h3>", 
	            			"box box-info", 
	            			"<h3 class='box-title animated fadeInUp'>Payment Reporting from <b>" +$('input[id=start_date]').val()+"</b> to <b>"+$('input[id=end_date]').val() + "</b></h3>", 
	            			"");
	            hide_tables();
		    	$("#date_range_reporting_table").show();
	            let date_range_reporting_table = $('#date_range_reporting_table').DataTable({
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
		                    {"data": "student.name"},
		                    {"data": "paid_batches"},
		                    {"data": "discount_per_batch"},
		                    {"data": "due_per_batch"},                    
		                    {"data": "total"},
	                    ],
	                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
	                    let total_date_range_discount = parseFloat(0);
	                    let total_date_range_pending = parseFloat(0);
	                    let total_date_range_price = parseFloat(0);

	                    for ( let i=0 ; i<aaData.length ; i++ ) {
	                        total_date_range_discount += parseFloat(aaData[i]['invoice_detail'][0]['discount_amount']);
	                        total_date_range_pending += parseFloat(aaData[i]['invoice_detail'][0]['due_amount']);
	                        total_date_range_price += parseFloat(aaData[i]['total']);
	                    }
						$('#total_date_range_discount').text(total_date_range_discount + ' /-');
	                    $('#total_date_range_pending').text(total_date_range_pending + ' /-');
	                    $('#total_date_range_price').text(total_date_range_price + ' /-');
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
    });

	$("#show_monthly_statement").click(function() {
		hide_divs();
   		$("#monthly_statement_div").show('slow');
   		set_attributes("", "", "box box-warning", "<h3 class='box-title animated fadeInUp'>Mothly Statement</h3>","");
        $("#btn_monthly_statement").click(function() {
        	if ($('input[id=statement_date]').val()) {
	            set_attributes("box box-warning", 
					   				"<h3 class='box-title animated fadeInUp'><p>Monthly Payment Statement for <b>"+$('input[id=statement_date]').val()+"</b></p></h3>", 
					            	"box box-warning", 
					            	"<h3 class='box-title animated fadeInUp'><p>Monthly Payment Statement for <b>"+$('input[id=statement_date]').val()+"</b></p></h3>", 
					            	"");
		            hide_tables();
			    	$("#monthly_reporting_table").show();
	            let monthly_reporting_table = $('#monthly_reporting_table').DataTable({
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
	                        {"data": "invoice_master.student.name"},
	                        {"data": "payment_from"},
	                        {"data": "batch.name"},
	                        {"data": "discount_amount"},
	                        {"data": "due_amount"},
	                        {"data": "price"},
					],
	                "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
	                    let total_monthly_statement_discount = parseFloat(0);
	                    let total_monthly_statement_pending = parseFloat(0);
	                    let total_monthly_statement_price = parseFloat(0);

	                    for ( let i=0 ; i<aaData.length ; i++ ) {
	                        total_monthly_statement_discount += parseFloat(aaData[i]['discount_amount']);
	                        total_monthly_statement_pending += parseFloat(aaData[i]['due_amount']);
	                        total_monthly_statement_price += parseFloat(aaData[i]['price']);
	                    }
						$('#total_monthly_statement_discount').text(total_monthly_statement_discount + ' /-');
	                    $('#total_monthly_statement_pending').text(total_monthly_statement_pending + ' /-');
	                    $('#total_monthly_statement_price').text(total_monthly_statement_price + ' /-');
	                    
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
        
	});

	$("#show_refund_reporting").click(function() {
		hide_divs();
   		$("#monthly_refund_div").show('slow');
   		set_attributes("", "", "box box-primary", "<h3 class='box-title animated fadeInUp'>Mothly Refund Statement</h3>","");
   		$("#btn_monthly_refund_reporting").click(function() {

   			if($('input[id=refund_statement_date]').val()) {
   				set_attributes("box box-primary", 
					   				"<h3 class='box-title animated fadeInUp'><p>Refund Statement for <b>"+$('input[id=refund_statement_date]').val()+"</b></p></h3>", 
					            	"box box-warning", 
					            	"<h3 class='box-title animated fadeInUp'><p>Refund Statement for <b>"+$('input[id=refund_statement_date]').val()+"</b></p></h3>", 
					            	"");
		            hide_tables();
			    	$("#monthly_refund_reporting_table").show();
		        let monthly_refund_reporting_table = $('#monthly_refund_reporting_table').DataTable({
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
		                    {"data": "serial_number"},
		                    {"data": "student.name"},
		                    {"data": "paid_batches"},                    
		                    {"data": "total"}
		                ],
		            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
		                    let total_refund_amount = parseFloat(0);
		                    for ( let i=0 ; i<aaData.length ; i++ ) {
		                        total_refund_amount += parseFloat(aaData[i]['total']);
		                    }
						    $('#total_refund_amount').text(total_refund_amount + ' /-');
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
			}
   		});
    
    });

   function hide_tables () {
    	$( "#daily_reporting_table").hide();
    	$( "#daily_reporting_table_wrapper").hide();
    	$( "#due_reporting_table").hide();
    	$( "#due_reporting_table_wrapper").hide();
    	$( "#monthly_due_reporting_table").hide();
    	$( "#monthly_due_reporting_table_wrapper").hide();
    	$("#date_range_reporting_table").hide();
    	$("#date_range_reporting_table_wrapper").hide();
		$("#monthly_reporting_table").hide();
    	$("#monthly_reporting_table_wrapper").hide();
		$("#monthly_refund_reporting_table").hide();
    	$("#monthly_refund_reporting_table_wrapper").hide();
    }

    function hide_divs () {
    	$( "#monthly_statement_div").hide('slow');
        $( "#monthly_refund_div").hide('slow');
        $( "#monthly_due_statement_div").hide('slow');
        $( "#date_range_statement_div").hide('slow');
    }

    function set_attributes(table_box_color, table_payment_title, second_box_title_border, second_box_title, reporting_message) {
		$("#second_box_title").html(second_box_title);
        $("#second_box_title_border").attr("class",second_box_title_border);
		if (table_box_color) {
        	$("#table_box_color").attr("class",table_box_color)
        }
        if (table_payment_title) {
        	$("#table_payment_title").html(table_payment_title);
        }
		$("#reporting_message").text(reporting_message);
    }    



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
    <!--Reporting Type Button Section -->
    <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Choose Reporting Option</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-2">
                    <button type="submit" id="daily_payment_reporting" class="btn btn-block bg-green margin btn-lg"><strong>Daily</strong> Collection</button>
                </div>
                <div class="col-md-2">
                    <button type="submit" id="due_payment_reporting" class="btn btn-block bg-red margin btn-lg"><strong>Due</strong> Reporting</button>
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
    <!-- ./ Reporting Type Button Section Ends -->

    <!--Reporting Type Input Section-->
    <div id="second_box_title_border" class="box box-primary">
        <!-- Temporary Message before choosing any button -->
        <div id="second_box_title" class="box-header with-border">
          <h3 class="box-title animated fadeInUp">Choose Reporting Option</h3>
        </div>

        <div class="box-body second_content_section">
            <h3 id="reporting_message"></h3>
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
                    <button type="submit" id="btn_monthly_refund_reporting" class="btn btn-block btn-primary"><strong>Monthly Refund </strong> Statement</button>
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
                    <button type="submit" id="btn_range_payment_reporting" class="btn btn-block btn-info"><strong>Show Date Range</strong>  Collection</button>
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
                    <button type="submit" id="btn_monthly_statement" class="btn btn-block btn-warning"><strong>Monthly Payment </strong> Statement</button>
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
                    <label for=""></label>
                    <button type="submit" id="btn_monthly_due_statement" class="btn btn-block btn-danger"><strong>Monthly Due </strong> Statement</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ./ Reporting Type Input Section Ends -->

    <div id="table_box_color" class="box box-primary">
            <div class="box-header">
                <h3 class="box-title" id="table_payment_title">Payment Reporting</h3>
            </div>
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
	                        <th id="total_daily_discount"></th>
	                        <th id="total_daily_pending"></th>
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
	                        <th>Student Id</th>
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
	                        <th></th>
	                        <th id="total_due_amount"></th> 
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Due Reporting Rows -->
	                </tbody>
                </table>
                
                <!-- Refund Reporting Table -->
                <table id='monthly_refund_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                        <th>Invoice ID</th>
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
                            <th id="total_date_range_discount"></th>
                            <th id="total_date_range_pending"></th>
                            <th id="total_date_range_price"></th> 
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
                            <th id="total_monthly_statement_discount"></th>
                            <th id="total_monthly_statement_pending"></th>
                            <th id="total_monthly_statement_price"></th>
	                    </tr>
	                </tfoot>                
	                <tbody>
	                    <!-- Monthly Reporting Rows -->
	                </tbody>
                </table>
                
                <!-- Monthly Due Reporting Table -->
                <table id='monthly_due_reporting_table' class='table table-bordered table-striped' style="display: none;">
	                <thead>
	                    <tr>
	                    	<th>Student Id</th>
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

