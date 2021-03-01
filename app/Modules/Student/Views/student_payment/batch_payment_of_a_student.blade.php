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

    $("#payment_print").hide();

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

    function getBatches(id) {
        console.log("Batch Info: "+id);
        $.get( "/get_batch_info_for_payment", { student_id: id } )
          .done(function( batches ) {
            // console.log("/get_batch_info_for_payment");
            // console.log(batches);
            var output='';
            $('#batch_table').html(''); 
            var c = 0;
            batch_length = batches.length;

            for (var i = 0; i < batches.length; i++) {
                var current = moment();
                var last_paid = moment(batches[i].pivot.last_paid_date);
                var month_diffrence = current.diff(last_paid, 'months');
                
                if (month_diffrence < 0) {
                    month_diffrence = 0;
                }
                console.log(batches[i].pivot.last_paid_date);
                var human_readable_last_paid_date = moment(batches[i].pivot.last_paid_date);
                human_readable_last_paid_date = moment(human_readable_last_paid_date).add(1, 'M');
                human_readable_last_paid_date = month[human_readable_last_paid_date.month()] + " - " + human_readable_last_paid_date.year();
                var payment_for_each_batch = month_diffrence * batches[i].price;
                             
                output += "<tr role='row' class='even'>"+
                                "<input type='hidden' name=batch_id[] value='"+batches[i].id+"'>"+
                                "<input type='hidden' name=subjects_id[] value='"+batches[i].subjects_id+"'>"+
                                "<input type='hidden' name=last_paid_date[] value='"+batches[i].pivot.last_paid_date+"' readonly>"+
                                "<input type='hidden' id='unit_price_"+i+"' name=batch_unit_price[] value='"+batches[i].price+"'>"+
                                "<input type='hidden' name=batch_name[] value='"+batches[i].name+"' readonly>"+
                                
                                
                                "<td>"+batches[i].name+"</td>"+
                                "<td>"+human_readable_last_paid_date+"</td>"+
                                "<td id='per_batch_price'>"+batches[i].price+"</td>"+
                                "<td class='no_of_month_per_batch'>"+
                                    "<select class='form-control' id='month_" + i + "' name='month[]' >"+
                                            "<option value='"+month_diffrence+"'>"+month_diffrence+"</option>"+
                                            "<option value=0>0</option>"+
                                            "<option value=1>1</option>"+
                                            "<option value=2>2</option>"+
                                            "<option value=3>3</option>"+
                                            "<option value=4>4</option>"+
                                            "<option value=5>5</option>"+
                                    "</select>"+
                                "</td>"+                       
                                "<td class='td_total_price_per_course'>"+"<input id='total_price_"+i+"' class='totalprice' name=total_price[] min='0' value='"+payment_for_each_batch+"' readonly></td>"+
                                "<td class='regular_radio_td'>"+
                                "<input type='radio' class='radio_regular' name='due_or_discount_"+i+"[0]' value='regular' checked>"+
                                "</td>"+
                                "<td class='due_radio_td'>"+
                                "<input type='radio' class='radio_due' name='due_or_discount_"+i+"[0]' value='due'>"+
                                "<input type='number' class='input_due' disabled='true'  min='0' name='due_or_discount_"+i+"[]' >"+
                                "</td>"+
                                "<td class='discount_radio_td'>"+
                                "<input type='radio' class='radio_discount' name='due_or_discount_"+i+"[0]' value='discount'>"+
                                "<input type='number' class='input_due' disabled='true'  min='0' name='due_or_discount_"+i+"[]' >"+
                                "</td>"+

                            "</tr>";
            }

            output += "<tr role='row' class='even'>"+
                                "<td></td>"+
                                "<td></td>"+
                                "<td></td>"+
                                "<td>Total Price</td>"+
                                "<td>"+"<input id='totalpriceAmount' name=total value='' readonly></td>"+
                            "</tr>";


            $('#batch_table').append(output);

            var sum = 0;
            $('.totalprice').each(function(){
                sum += parseFloat(this.value);
            });

            $('input#totalpriceAmount').val(sum);
            
            $('[class^=radio_regular]').click(function(event)  {
                let aaa = $(this).parent().siblings(".due_radio_td").children()[1];
                $(aaa).val("");
                aaa.disabled = true;
                
                let bbb = $(this).parent().siblings(".discount_radio_td").children()[1];
                $(bbb).val("");
                bbb.disabled = true;


                let unit_price_per_batch = $(this).parent().siblings("#per_batch_price")[0].innerHTML;
                let no_of_month_per_batch = $(this).parent().siblings(".no_of_month_per_batch").children().find("option:selected").val();
                let td_total_price_per_course = no_of_month_per_batch * unit_price_per_batch;
                $(this).parent().siblings(".td_total_price_per_course").children().val(td_total_price_per_course);
                sum = 0;
                $('.totalprice').each(function(){
                    // console.log(this.value);
                    sum += parseFloat(this.value);
                    $('input#totalpriceAmount').val(sum);
                });

            });
            
            $('[class^=radio_due]').click(function(event)  {
                console.log("radio_due Clicked");
                let ccc = $(this).siblings()[0];
                ccc.disabled = false;
                
                let ddd = $(this).parent().siblings(".discount_radio_td").children()[1];
                $(ddd).val("");
                ddd.disabled = true;

                let unit_price_per_batch = $(this).parent().siblings("#per_batch_price")[0].innerHTML;
                let no_of_month_per_batch = $(this).parent().siblings(".no_of_month_per_batch").children().find("option:selected").val();
                let td_total_price_per_course = no_of_month_per_batch * unit_price_per_batch;
                $(this).parent().siblings(".td_total_price_per_course").children().val(td_total_price_per_course);
                sum = 0;
                $('.totalprice').each(function(){
                    // console.log(this.value);
                    sum += parseFloat(this.value);
                    $('input#totalpriceAmount').val(sum);
                });
            });
            $('[class^=radio_discount]').click(function(event)  {
                console.log("radio_discount Clicked");
                let eee = $(this).siblings()[0];
                eee.disabled = false;
                let fff = $(this).parent().siblings(".due_radio_td").children()[1];
                $(fff).val("");
                fff.disabled = true;

                let unit_price_per_batch = $(this).parent().siblings("#per_batch_price")[0].innerHTML;
                let no_of_month_per_batch = $(this).parent().siblings(".no_of_month_per_batch").children().find("option:selected").val();
                let td_total_price_per_course = no_of_month_per_batch * unit_price_per_batch;
                $(this).parent().siblings(".td_total_price_per_course").children().val(td_total_price_per_course);
                sum = 0;
                $('.totalprice').each(function(){
                    // console.log(this.value);
                    sum += parseFloat(this.value);
                    $('input#totalpriceAmount').val(sum);
                });
            });
            
            $('[name^=due_or_discount_]').keyup(function(event)  {

                // if(event.which == 13) {
                    let due_or_discount_value = this.value;
                    let unit_price_per_batch = $(this).parent().siblings("#per_batch_price")[0].innerHTML;
                    let no_of_month_per_batch = $(this).parent().siblings(".no_of_month_per_batch").children().find("option:selected").val();
                    let td_total_price_per_course = no_of_month_per_batch * unit_price_per_batch;
                    // let td_total_price_per_course = $(this).parent().siblings(".td_total_price_per_course").children().val();
                    
                    let final_price_per_course = td_total_price_per_course - due_or_discount_value;
                    // console.log('final_price_per_course');
                    // console.log(final_price_per_course);
                    if(final_price_per_course < 0)  { 
                        final_price_per_course = 0; 
                    }
                    
                    $(this).parent().siblings(".td_total_price_per_course").children().val(final_price_per_course);
                    sum = 0;
                    $('.totalprice').each(function(){
                        // console.log(this.value);
                        sum += parseFloat(this.value);
                        $('input#totalpriceAmount').val(sum);
                    });

                    // $('input#totalpriceAmount').val(sum);
                    
                    // let final_price_total = $('#totalpriceAmount').val() - due_or_discount_value;
                    // if(final_price_total < 0)  { 
                    //     final_price_total = 0; 
                    // }
                    // $('#totalpriceAmount').val(final_price_total);
                    
                    // return false;
                // }

            });

            $('[id^=month_]').change(function(event)  {
                var no_of_month = this.value;
                var month_id = this.id;
                var unit_price_id = "#unit_price_" + month_id.substring(month_id.length-1);
                // console.log(unit_price_id);
                var total_price_id = "#total_price_"+month_id.substring(month_id.length-1);
                var unit_price_amount = $(unit_price_id).val();
                var total_price_amount = $(total_price_id).val(unit_price_amount * no_of_month);
                // console.log(total_price_amount);
                var sum = 0;
                $('.totalprice').each(function(){
                    sum += parseFloat(this.value);
                    $('input#totalpriceAmount').val(sum);
                });
                $('input#totalpriceAmount').val(sum);
                // console.log(sum);
            });
        });
    }


    // function print_money_receipt() {
    $('#payment_print').click(function() {
        $.get('/get_invoice_id_for_print',function(serial_number) {
            console.log(serial_number);
            invoice_serial_number = serial_number;
            $('#serial_number').val(serial_number);
            console.log('Print Option invoice_serial_number: '+invoice_serial_number);
        // });        
        var top = "<div>Money Receipt no: "+invoice_serial_number+"<div/>"+
                    "<div>Date: {{ $refDate }}<div/>"+
                    "<div>Student Name: "+$('p#student_name').text()+"<div/>"+
                    "<div>Father's Name: "+$('p#fathers_name').text()+"<div/>"+
                    "<div>Phone Number: "+$('p#student_phone_number').text()+"<div/>"+
                    "<br>";
        

        var header =    "<table class='table table-bordered table-striped'>"+
                            "<thead>"+
                                "<tr>"+
                                    "<th>Batch Name</th>"+
                                    "<th>Payment From</th>"+
                                    "<th>Unit Price /=</th>"+
                                    "<th>no of month</th>"+
                                    "<th>Total Price Per Course /= </th>"+
                                "</tr>"+
                            "</thead>"+
                        "<tbody>";                         
                        


        
        // var date_of_payment = $('.ref_date').attr('value');
        let date_of_payment = "{{ $refDate }}";
        var payment_data = $('#student_payment').serializeArray();
        console.log(payment_data);
        var payment_data_count = 0;
        var payment_output = "";

        for (var count = 0; count < batch_length; count++) {
            var human_readable_last_paid_date = moment(payment_data[6+payment_data_count].value);
                human_readable_last_paid_date = moment(human_readable_last_paid_date).add(1, 'M');
                human_readable_last_paid_date = month[human_readable_last_paid_date.month()] + " - " + human_readable_last_paid_date.year();
            payment_output += "<tr role='row' class='even'>"+
                                "<td>"+payment_data[8+payment_data_count].value+"</td>"+
                                "<td>"+human_readable_last_paid_date+"</td>"+
                                "<td>"+payment_data[7+payment_data_count].value+"</td>"+
                                "<td>"+payment_data[9+payment_data_count].value+"</td>"+
                                "<td>"+payment_data[10+payment_data_count].value+"</td>"+
                            "</tr>";                


            if (payment_data[11 + payment_data_count].value == "due" || payment_data[11+payment_data_count].value == "discount") {
                payment_data_count += 1;
                console.log("payment_data_count" + payment_data_count);
            }

            payment_data_count += 8;
        }

            payment_output += "<tr role='row' class='even'>"+
                                "<td></td>"+
                                "<td></td>"+
                                "<td></td>"+
                                "<td>Total Price</td>"+
                                "<td>"+payment_data[ payment_data.length - 1 ].value+"</td>"+
                            "</tr></tbody ></table>";                
        var final_output = top + header + payment_output;
        
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

        }); // $.get('/get_invoice_id',function(serial_number){
    }); // End of Print Function
    
    // }


    $("#student_info_for_payment").click(function() {
        $("#payment_print").hide('slow');
        $("#student_info_section").show('slow');
        $.get("/get_student_info_for_payment", { 
                student_id: $('select[id=student_id]').val(),
                student_phonenumber: $("#student_phonenumber").val()
        })
        .done(function( data ) {
            console.log(data);
            let img_address = "{{ URL::to('/') }}";
            if (($('select[id=student_id]').val() != null) && ($('input[id=ref_date]').val() != null) && !jQuery.isEmptyObject(data)) {
               $("#student_payment_div").css({ display: "block" });
               $('p#student_name').text(data.name);
               if (data.school) {
                    $('p#school_name').text(data.school.name);
               }
               $('p#student_email').text(data.student_email);
               $('p#fathers_name').text(data.fathers_name);
               $('p#mothers_name').text(data.mothers_name);
               $('p#student_phone_number').text(data.student_phone_number);
               $('p#guardian_phone_number').text(data.guardian_phone_number);
               $('input#students_id').val(data.id);
               $("#student_pofile_image").html("<img src='"+img_address+"/"+data.students_image+"' class='img-fluid' height='100' width='100' alt='Student profile picture'>");
               getBatches(data.id);
            }
            
           
        });

    });

    $("#student_due_payment").click(function() {
            var due_table = $('#student_due_datatable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/due_payment_student')}}",
                    'data': {
                       student_id: $('select[id=student_id]').val(),
                    },
                },
            "initComplete": function(settings, json) {
                
                $('.temp_due').click(function(event)  {
                    $.post("/clear_due_payment", { 
                        invoice_detail_id: this.id,
                    })
                    .done(function( data ) {
                        console.log(data);
                        due_table.ajax.reload(null, false);
                    });
                    due_table.ajax.reload(null, true);
                    event.preventDefault();
                });
              
            },
            "columns": [
                    {"data": "batch_name"},
                    {"data": "payment_date"},
                    {"data": "payment_to"},
                    {"data": "invoice_details_price"},
                    {"data": "due_amount"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ]
        });
    });
    
    
    

    $("#student_payment").submit(function(e) {
        e.preventDefault();
        // $(':button[class="payment_submit"]').prop('disabled', true);
        var url = "/student_payment"; // the script where you handle the form input.
        if (parseFloat($('#totalpriceAmount').val()) > 0) {
            $( ".payment_submit" ).attr( "disabled", true );
            $.get('/get_payment_invoice_id',function(serial_number) {
                invoice_serial_number = serial_number;
                $('#serial_number').val(serial_number);
                // console.log(invoice_serial_number);
            // });
            $.ajax({
                type: "POST",
                url: url,
                data: $("#student_payment").serialize(), // serializes the form's elements.
                success: function(reply_data) {
                    console.log(reply_data); 
                    $.get("/get_student_info_for_payment", {
                            student_id: $('select[id=student_id]').val(),
                            student_phonenumber: $("#student_phonenumber").val() 
                    })
                    .done(function( data ) {
                        console.log("student_payment");
                        console.log(data);
                        $("#payment_print").show('slow');
                        let msg = '<div class="alert alert-success alert-dismissible">'+
                                    '<button type="button" id="success_payment_msg" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                                    '<h4><i class="icon fa fa-check"></i> Payment Complete for <strong>'+data.name+'</strong></h4>'+
                                    '</div>';
                           
                       $('#payment_success_msg').html(msg);

                       $('#success_payment_msg').click(function(e) {
                            $( ".payment_submit" ).attr( "disabled", false );
                            $("#payment_print").hide('slow');
                            let img_address = "{{ URL::to('/') }}";
                            if (($('select[id=student_id]').val() != null) && ($('input[id=ref_date]').val() != null)) {
                               $("#student_payment_div").css({ display: "block" });
                               $('p#student_name').text(data.name);
                               $('p#student_email').text(data.student_email);
                               $('p#fathers_name').text(data.fathers_name);
                               $('p#mothers_name').text(data.mothers_name);
                               $('p#student_phone_number').text(data.student_phone_number);
                               $('p#guardian_phone_number').text(data.guardian_phone_number);
                               $('input#students_id').val(data.id);
                               $("#student_pofile_image").html("<img src='"+img_address+"/"+data.students_image+"' class='img-fluid' height='100' width='100' alt='Student profile picture'>");
                               getBatches(data.id);
                            }
                            e.preventDefault();
                        });
                        
                       
                    });
                }
            });
           }); // $.get('/get_invoice_id',function(serial_number){
        }
        // print_money_receipt();
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
        Batch Payment Dashboard
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
	                <div class="col-xs-3">
	                    <label for="batch_id" >Student*</label>
	                    <select class="form-control select2" name="student_id" id="student_id"></select>
                	</div>
                    <div class="col-xs-3">
                        <label for="student_phonenumber" >Phone Number*</label>
                        <input type="text" class="form-control" name="student_phonenumber" id="student_phonenumber">
                    </div>
	                <div class="col-xs-3">
	                    <label for="" ></label>
	                    <button type="submit" id="student_info_for_payment" class="btn btn-block btn-success">Show Info</button>
	                </div>
                    <div class="col-xs-3">
                        <label for="" ></label>
                        <button type="submit" id="student_due_payment" class="btn btn-block btn-danger">Show Due</button>
                    </div>
                    
                    
                </div>
            </div>
        </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box-body -->

        <!-- /.box-header -->
        <div class="box box-danger">
            <div class="box-body">
                <div class="box-header">
                  <h3 class="box-title">Due Information</h3>
                </div>
                <table id="student_due_datatable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>Payment Date</th>
                            <th>Due For</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Clear</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <!-- user list -->
                    </tbody>                        
                </table>
            </div>
        </div>
        <!-- /.box-body -->


    <!-- Horizontal Form -->
    <div class="box box-info">
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


	<!-- Horizontal Form -->
    <div class="box box-warning">
            <div class="box-header">
                <h4>
                    Payment 
                </h4>
                <div id="payment_success_msg"></div>            
            </div>
            
            <div id="student_payment_div" class="box-body" style="display: none;">
                {!! Form::open(array('id' => 'student_payment', 'class' => 'form-horizontal')) !!}
                <input id="ref_date" type='hidden' class="form-control ref_date" name="payment_date" value="{{ $refDate }}">
                <input type='hidden' id="students_id" name="students_id">
                <input type='hidden' id="serial_number" name="serial_number">
                <table id="all_user_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>Payment From</th>
                            <th>Unit Price /=</th>
                            <th>no of months</th>
                            <th>Total Price Per Course /= </th>
                            <th>Regular</th>
                            <th>Due</th>
                            <th>Discount</th>
                        </tr>
                    </thead>
                    <tbody id="batch_table" >                            
                    </tbody >
                </table>
                <div class="footer">
                    <label for="" ></label>
                    <div class="row">
                    <div class="col-md-6">
                        <button id="payment_print" type="button" class="btn btn-block bg-purple btn-lg">Print</button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-block bg-green btn-lg payment_submit">Payment</button>
                    </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box -->


</section>
<!-- /.content -->

@endsection

