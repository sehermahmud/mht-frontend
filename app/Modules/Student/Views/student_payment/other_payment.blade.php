@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('../../plugins/datepicker/datepicker3.css')}}">
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
<script>
    // add the rule here
    $.validator.addMethod("valueNotEquals", function (value, element, arg) {
        return arg != value;
    }, "Value must not equal arg.");

    $(document).ready(function () {

    var month = ["January","February","March", "April",
                "May", "June","July", "August",
                "September","October","November","December"];

    var batch_length = 0;
    let invoice_serial_number = 0;

    //Date picker for Start Date
    $('.ref_date').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true
    });

    $("#admission_payment_print").hide();
    $("#other_payment_print").hide();

    $('#student_id').select2({
        allowClear: true,
        placeholder: 'Select Student',
        ajax: {
            url: "/get_all_student_for_payment",
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

    /****************************** 
    * Showing Student Information * 
    *******************************/
    $("#show_student_info").click(function() {
        
        // $("#admission_payment_print").hide('slow');
        $("#student_info_section").show('slow');
        
        $.get("/get_student_info_for_payment", { 
                student_id: $('select[id=student_id]').val(),
        })
        .done(function( data ) {
            let img_address = "{{ URL::to('/') }}";
            if (($('select[id=student_id]').val() != null) && ($('input[id=ref_date]').val() != null) && !jQuery.isEmptyObject(data)) {
               // $("#student_admission_payment_table").css({ display: "block" });
               $('p#student_name').text(data.name);
               if (data.school) {
                  $('p#school_name').text(data.school.name);
               }
               $('p#student_email').text(data.student_email);
               $('p#fathers_name').text(data.fathers_name);
               $('p#mothers_name').text(data.mothers_name);
               $('p#student_phone_number').text(data.student_phone_number);
               $('p#guardian_phone_number').text(data.guardian_phone_number);
               $("#student_pofile_image").html("<img src='"+img_address+"/"+data.students_image+"' class='img-fluid' height='100' width='100' alt='Student profile picture'>");
               $('input#students_id').val(data.id);
               $('input#students_id_for_other_payment').val(data.id);


                /**************************************************************** 
                * Showing Admission Payment Table if admission fee is not paid. *
                * Otherwise Showing a Admission Paid Completion message.        *
                *****************************************************************/
                if (data.admitted_status == 1) {
                    $("#student_admission_payment_table").hide('slow');
                    $("#student_admission_payment_message").show('slow');
                    $( ".admission_payment_section" ).attr( "class", 'box box-success admission_payment_section');
                }
                else {
                    $("#student_admission_payment_message").hide('slow');
                    $("#student_admission_payment_table").show('slow');
                    $( ".admission_payment_section" ).attr( "class", 'box box-danger admission_payment_section');
                }               


            }
            $(".payment_section").hide('slow');
        });
    });

    /**************************************************************** 
    * Showing Admission Payment Table if admission fee is not paid. *
    * Otherwise Showing a Admission Paid Completion message.        *
    *****************************************************************/
 //    $('#admission_payment_info').click(function() {
	// 	$.get("/admission_payment_info", { 
 //                student_id: $('select[id=student_id]').val(),
 //        })
 //        .done(function( data ) {
 //            if (data.admitted_status == 1) {
 //                $("#student_admission_payment_table").hide('slow');
 //                $("#student_admission_payment_message").show('slow');
 //            }
 //            else {
 //                $("#student_admission_payment_message").hide('slow');
 //                $("#student_admission_payment_table").show('slow');
 //            }
            
 //        });

	// });
    /****************************** 
    * Showing Other Payment Table * 
    *******************************/
	$('#other_payment_info').click(function() {
        if ($('select[id=student_id]').val() != null) {
            $("#student_other_payment_table").show('slow');
        }
		
    });


    /**************************************** 
    * Submitting the Admission Payment Data * 
    *****************************************/
	$("#admission_payment_form").submit(function(e) {
        e.preventDefault();
        var url = "/student_admission_payment_process"; 
        if (parseFloat($('#admission_fee').val()) > 0) {
            $( ".admission_payment_submit" ).attr( "disabled", true );
            $.get('/get_other_payment_invoice_id', { 
                payment_type: "A-" 
            })
            .done(function( serial_number ) {
            invoice_serial_number = serial_number;
            $('#serial_number').val(serial_number);
            $.ajax({
                type: "POST",
                url: url,
                data: $("#admission_payment_form").serialize(), // serializes the form's elements.
                success: function(reply_data) {
                	console.log('reply_data Admission'); 
                    console.log(reply_data); 
                    $.get("/get_student_info_for_payment", {
                            student_id: $('select[id=student_id]').val(),
                    })
                    .done(function( data ) {
                        // console.log("admission_payment_form");
                        // console.log(data);
                        $("#admission_payment_print").show('slow');
                        let msg = '<div class="alert alert-success alert-dismissible">'+
                                    '<button type="button" id="success_admission_payment_msg" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                                    '<h4><i class="icon fa fa-check"></i> Admission Payment Complete for <strong>'+data.name+'</strong></h4>'+
                                    '</div>';
                           
                       $('#payment_success_msg').html(msg);

                       $('#success_admission_payment_msg').click(function(e) {
                            $( ".admission_payment_submit" ).attr( "disabled", false );
                            $("#admission_payment_print").hide('slow');
                            let img_address = "{{ URL::to('/') }}";
                            if (($('select[id=student_id]').val() != null) && ($('input[id=ref_date]').val() != null)) {
                               // $("#student_admission_payment_table").css({ display: "block" });
                               $('p#student_name').text(data.name);
                               $('p#student_email').text(data.student_email);
                               $('p#fathers_name').text(data.fathers_name);
                               $('p#mothers_name').text(data.mothers_name);
                               $('p#student_phone_number').text(data.student_phone_number);
                               $('p#guardian_phone_number').text(data.guardian_phone_number);
                               $('input#students_id').val(data.id);
                               $("#student_pofile_image").html("<img src='"+img_address+"/"+data.students_image+"' class='img-fluid' height='100' width='100' alt='Student profile picture'>");
                               
                               $("#admission_payment_form")[0].reset();
                               $( ".admission_payment_section" ).attr( "class", 'box box-success admission_payment_section');
                               
                               $("#student_admission_payment_table").hide('slow');
                               $("#student_admission_payment_message").show('slow');
                            }
                            e.preventDefault();
                        });
                    });
                }
            });
           }); 
        }
    });

    /**************************************** 
    * Submitting the Other Payment Data * 
    *****************************************/
    $("#other_payment_form").submit(function(e) {
        e.preventDefault();
        var url = "/student_other_payment_process"; 
        if (parseFloat($('#other_fee').val()) > 0) {
            $( ".other_payment_submit" ).attr( "disabled", true );
            $.get('/get_other_payment_invoice_id', { 
                payment_type: "O-" 
            })
            .done(function( serial_number ) {
            invoice_serial_number = serial_number;
            $('#serial_number_for_other_payment').val(serial_number);
            $.ajax({
                type: "POST",
                url: url,
                data: $("#other_payment_form").serialize(), // serializes the form's elements.
                success: function(reply_data) {
                    console.log('reply_data Other'); 
                    console.log(reply_data); 
                    $.get("/get_student_info_for_payment", {
                            student_id: $('select[id=student_id]').val(),
                    })
                    .done(function( data ) {
                        console.log("other_payment_form");
                        console.log(data);
                        console.log('other Payment Show slow');
                        $("#other_payment_print").show('slow');
                        let msg = '<div class="alert alert-success alert-dismissible">'+
                                    '<button type="button" id="success_othre_payment_msg" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                                    '<h4><i class="icon fa fa-check"></i> Payment Complete for <strong>'+data.name+'</strong></h4>'+
                                    '</div>';
                           
                       $('#other_payment_success_msg').html(msg);

                       $('#success_othre_payment_msg').click(function(e) {
                            $( ".other_payment_submit" ).attr( "disabled", false );
                            $("#other_payment_print").hide('slow');
                            let img_address = "{{ URL::to('/') }}";
                            if (($('select[id=student_id]').val() != null) && ($('input[id=ref_date]').val() != null)) {
                               // $("#student_admission_payment_table").css({ display: "block" });
                               $('p#student_name').text(data.name);
                               $('p#student_email').text(data.student_email);
                               $('p#fathers_name').text(data.fathers_name);
                               $('p#mothers_name').text(data.mothers_name);
                               $('p#student_phone_number').text(data.student_phone_number);
                               $('p#guardian_phone_number').text(data.guardian_phone_number);
                               $('input#students_id').val(data.id);
                               $("#student_pofile_image").html("<img src='"+img_address+"/"+data.students_image+"' class='img-fluid' height='100' width='100' alt='Student profile picture'>");
                               
                               $("#other_payment_form")[0].reset();
                            }
                            e.preventDefault();
                        });
                    });
                }
            });
           }); 
        }
    });


    /************************** 
    * Admission Payment Print * 
    ***************************/
    $('#admission_payment_print').click(function() {
        $.get('/get_other_invoice_id_for_print',function(serial_number) {
            console.log(serial_number);
            invoice_serial_number = serial_number;
            $('#serial_number').val(serial_number);
            console.log('Print Option invoice_serial_number: '+invoice_serial_number);
        
        let top = "<div>Money Receipt no: "+invoice_serial_number+"<div/>"+
                    "<div>Date: {{ $refDate }}<div/>"+
                    "<div>Student Name: "+$('p#student_name').text()+"<div/>"+
                    "<div>Father's Name: "+$('p#fathers_name').text()+"<div/>"+
                    "<div>Phone Number: "+$('p#student_phone_number').text()+"<div/>"+
                    "<br>";
        

        let payment_output ="<table class='table table-bordered table-striped'>"+
                            "<thead>"+
                                "<tr>"+
                                    "<th>Payment Type</th>"+
                                    "<th>Description</th>"+
                                    "<th>Amount</th>"+
                                "</tr>"+
                            "</thead>" +                        
        
                             "<tbody>" +
                                "<tr role='row' class='even'>"+
                                    "<td> Admission Fee </td>"+
                                    "<td>"+ $('#admission_dsecription').val()+"</td>"+
                                    "<td>"+$('#admission_fee').val()+"</td>"+
                                "</tr>"+
                                "</tbody >"+
                            "</table>";
                     
        
        let final_output = top + payment_output;
        
        $(final_output).print({
            globalStyles: true,
            mediaPrint: false,
            stylesheet : "http://fonts.googleapis.com/css?family=Inconsolata",
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: null,
            doctype: '<!doctype html>'
        });

        }); 
    });

    /**********************
    * Other Payment Print * 
    ***********************/
    $('#other_payment_print').click(function() {
        $.get('/get_other_invoice_id_for_print',function(serial_number) {
            console.log(serial_number);
            invoice_serial_number = serial_number;
            $('#serial_number_for_other_payment').val(serial_number);
            console.log('Print Option invoice_serial_number: ' + invoice_serial_number);
        
        let top = "<div>Money Receipt no: "+ invoice_serial_number + "<div/>"+
                    "<div>Date: {{ $refDate }}<div/>"+
                    "<div>Student Name: "+$('p#student_name').text()+"<div/>"+
                    "<div>Father's Name: "+$('p#fathers_name').text()+"<div/>"+
                    "<div>Phone Number: "+$('p#student_phone_number').text()+"<div/>"+
                    "<br>";
        

        let payment_output ="<table class='table table-bordered table-striped'>"+
                            "<thead>"+
                                "<tr>"+
                                    "<th>Payment Type</th>"+
                                    "<th>Description</th>"+
                                    "<th>Amount</th>"+
                                "</tr>"+
                            "</thead>" +                        
        
                             "<tbody>" +
                                "<tr role='row' class='even'>"+
                                    "<td> Others </td>"+
                                    "<td>"+ $('#other_dsecription').val()+"</td>"+
                                    "<td>"+$('#other_fee').val()+"</td>"+
                                "</tr>"+
                                "</tbody >"+
                            "</table>";
                     
        
        let final_output = top + payment_output;
        
        $(final_output).print({
            globalStyles: true,
            mediaPrint: false,
            stylesheet : "http://fonts.googleapis.com/css?family=Inconsolata",
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: null,
            doctype: '<!doctype html>'
        });

        }); 
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
        Payment Dashboard
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Payment</a></li>
        <li class="active">Student Payment Page</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <!-- Horizontal Form -->
    <div class="box box-primary">
        
        <div class="box-body">
        
            <div class="box-header with-border">
              <h3 class="box-title">Search for a Student</h3>
            </div>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="search_student_div" class="box-body">
                <div class="row">
	                <div class="col-xs-4">
	                    <label for="batch_id" >Student*</label>
	                    <select class="form-control select2" name="student_id" id="student_id"></select>
                	</div>
                    <div class="col-xs-4">
	                    <label for="" ></label>
	                    <button type="submit" id="show_student_info" class="btn btn-block bg-navy btn-lg">Show Student Info & Admission Status</button>
	                </div>
	                <!-- <div class="col-xs-4">
	                    <label for="" ></label>
	                    <button type="submit" id="admission_payment_info" class="btn btn-block bg-navy btn-lg">Admission Payment</button>
	                </div> -->
                    <div class="col-xs-4">
                        <label for="" ></label>
                        <button type="submit" id="other_payment_info" class="btn btn-block bg-teal btn-lg">Buy Other Equipment</button>
                    </div>
                    
                    
                </div>
            </div>
        </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box-body -->


    <!-- Admission Payment Info -->
    <div class="box box-primary">
            <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Student's Information</h3>
            </div>
            <div id="student_info_section" style="display: none">
                <div class="col-md-4">
                    <div id="student_pofile_image" class="form-group">
                        
                    </div>
                    
                    <div class="form-group">
                        <label for="school_name" >School Name : </label>
                        <p id="school_name"></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" >Student Name : </label>
                        <p id="student_name"></p>
                    </div>
                    <div class="form-group">
                        <label for="fathers_name" >Father's Name : </label>
                        <p id="fathers_name"></p>
                    </div>
                    <div class="form-group">
                        <label for="mothers_name" >Mother's Name : </label>
                        <p id="mothers_name"></p>
                    </div>
                    
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="student_phone_number" >Student's Phone Number : </label>
                        <p id="student_phone_number"></p>
                    </div>
                    <div class="form-group">
                        <label for="guardian_phone_number" >Guardian's Phone Number : </label>
                        <p id="guardian_phone_number"></p>
                    </div>
                    <div class="form-group">
                        <label for="student_email" >Email : </label>
                        <p id="student_email"></p>
                    </div>
                </div>
            </div>
            </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box -->




	<!-- Admission Payment Section -->
    <div class="box box-primary admission_payment_section">
            <div class="box-header with-border">
                <h4>
                    Admission Payment Section
                </h4>
                <div id="payment_success_msg"></div>            
            </div>
            
            <div id="student_admission_payment_table" class="box-body" style="display: none;">
                {!! Form::open(array('id' => 'admission_payment_form', 'class' => 'form-horizontal')) !!}
                <input id="ref_date" type='hidden' class="form-control ref_date" name="payment_date" value="{{ $refDate }}">
                <input type='hidden' id="students_id" name="students_id">
                <input type='hidden' id="serial_number" name="serial_number">
                <table id="all_user_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Payment Type</th>
                            <th style="text-align: center;">Description</th>
                            <th style="text-align: center;">Amount /=</th>
                        </tr>
                        
                    </thead>
                    <tbody id="batch_table">
                    	<tr>
                            <td style="text-align: center;">Admission Fee</th>
                            <td><input id="admission_dsecription" class="form-control" type="text" name="description" placeholder="Description"></td>
                            <td><input id="admission_fee" class="form-control" type="number" name="admission_fee" value="1000"></td>
                        </tr>                          
                    </tbody >
                </table>
                <div class="footer">
                    <label for="" ></label>
                    <div class="row">
                    <div class="col-md-6">
                        <button id="admission_payment_print" type="button" class="btn btn-block bg-purple btn-lg">Print</button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-block bg-navy btn-lg admission_payment_submit">Payment</button>
                    </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div id="student_admission_payment_message" class="box-body" style="display: none;">
                <div class="callout callout-success">
                    <h3>Admission fee is paid for this student !</h3>
                </div>
            </div>
    </div>
    


	<!-- Other Payment Section -->
        <div class="box box-primary other_payment_section">
            <div class="box-header with-border">
                <h4>
                    Other Payment Section
                </h4>
                <div id="other_payment_success_msg"></div>            
            </div>
            
            <div id="student_other_payment_table" class="box-body" style="display: none;">
                {!! Form::open(array('id' => 'other_payment_form', 'class' => 'form-horizontal')) !!}
                <input id="ref_date" type='hidden' class="form-control ref_date" name="payment_date" value="{{ $refDate }}">
                <input type='hidden' id="students_id_for_other_payment" name="students_id">
                <input type='hidden' id="serial_number_for_other_payment" name="serial_number">
                <table id="all_user_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Payment Type</th>
                            <th style="text-align: center;">Description</th>
                            <th style="text-align: center;">Amount /=</th>
                        </tr>
                        
                    </thead>
                    <tbody id="batch_table">
                        <tr>
                            <td style="text-align: center;">Other Fee</th>
                            <td><input id="other_dsecription" class="form-control" type="text" name="other_dsecription" placeholder="Description"></td>
                            <td><input id="other_fee" class="form-control" type="number" name="other_fee" placeholder="Amount"></td>
                        </tr>                          
                    </tbody >
                </table>
                <div class="footer">
                    <label for="" ></label>
                    <div class="row">
                    <div class="col-md-6">
                        <button id="other_payment_print" type="button" class="btn btn-block bg-purple btn-lg">Print</button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-block bg-teal btn-lg other_payment_submit">Payment</button>
                    </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
    </div>




</section>
<!-- /.content -->

@endsection

