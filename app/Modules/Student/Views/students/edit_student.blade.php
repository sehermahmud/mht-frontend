@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('../../plugins/datepicker/datepicker3.css')}}">
@endsection

@section('scripts')
<script src="{{asset('../../plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>

$(document).ready(function () {

    // initialize tooltipster on form input elements
    $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false, // allow multiple tips to be open at a time
        position: 'right'  // display the tips to the right of the element
    });

    //Date picker for Start Date
    $('.ref_date').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true
    });

    // initialize validate plugin on the form
    // $('#add_user_form').validate({
    //     errorPlacement: function (error, element) {

    //         var lastError = $(element).data('lastError'),
    //                 newError = $(error).text();

    //         $(element).data('lastError', newError);

    //         if (newError !== '' && newError !== lastError) {
    //             $(element).tooltipster('content', newError);
    //             $(element).tooltipster('show');
    //         }
    //     },
    //     success: function (label, element) {
    //         $(element).tooltipster('hide');
    //     },
    //     rules: {
    //         name: {required: true, minlength: 4},
    //         fathers_name: {required: true, minlength: 4},
    //         mothers_name: {required: true, minlength: 4},
    //         student_phone_number: {required: true},
    //         guardian_phone_number: {required: true},
    //         schools_id: {valueNotEquals: "default"},
    //         grades_id: {valueNotEquals: "default"},
    //         batch_types_id: {valueNotEquals: "default"},

    //     },
    //     messages: {
    //         name: {required: "Enter Student Name"},
    //         fathers_name: {required: "Enter Student's Father Name"},
    //         mothers_name: {required: "Enter Student's Mother's Name"},
    //         student_phone_number: {required: "Enter Student's Phone Number"},
    //         guardian_phone_number: {required: "Enter Guardian's Phone Number"},
    //         schools_id: {valueNotEquals: "Select a School"},
    //         grades_id: {valueNotEquals: "Select a Grade"},
    //         batch_types_id: {valueNotEquals: "Select Edexcel or Cambridge"},
    //     }
    // });

    $.get("/get_student_batch_for_edit", {
            student_id: "{{ $getStudent->id }}" 
    })
    .done(function( data ) {
        // console.log(data);
        // var batchType = $('#batch_types_id').find(":selected").val();
        // var grade = $('#grades_id').find(":selected").val();
        // console.log("batchType "+batchType);
        // console.log("Grade " + grade);

        for (var i = 0; i < data.length; i++) {
            
            let subject_id = "#subject" + data[i].subjects_id;
            let subject_id_for_select2 = data[i].subjects_id;
            console.log("subject_id_for_select2 " + subject_id_for_select2);
            
            $( subject_id ).select2({
            allowClear: true,
            data: [{ 
                    id: data[i].id, 
                    text:  data[i].name 
                }],
            ajax: {
                url: "/getallbatch",
                dataType: 'json',
                delay: 250,
                tags: true,
                data: function (params) {
                  console.log("Inside data object " + subject_id_for_select2);
                  return {
                        q: params.term, // search term
                        page: params.page,
                        subject_id: subject_id_for_select2,
                        // batchType_id:batchType,
                        // grades_id:grade
                    };
                },
                processResults: function (data, params) {
                  // parse the results into the format expected by Select2
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data, except to indicate that infinite
                  // scrolling can be used
                  console.log("Inside Initial Select2");
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

        }
    });

    
    $.get("/get_batch_joining_date_for_edit", {
            student_id: "{{ $getStudent->id }}" 
    })
    .done(function( batch ) {
        console.log('get_batch_joining_date_for_edit');
        for (var i = 0; i < batch.length; i++) {
            console.log(batch[i].id + " - " + batch[i].subjects_id + " - " + batch[i].pivot.joining_date);
            let class_start_date_id = '#class_start_date' + batch[i].subjects_id;
            $(class_start_date_id).val(batch[i].pivot.joining_date);
        }
    });



    $('#batch_types_id').change(function(event){
        $('.sub_checkbox').attr('checked',false);
        $('.batchSelection').hide();
        $('.select2').val('');
    });

    $('#grades_id').change(function(event){
        $('.sub_checkbox').attr('checked',false);
        $('.batchSelection').hide();
        $('.select2').val('');
    });

    $(".sub_checkbox").change(function() {


        $("#class_start_date" + this.value).val('');
        if(this.checked) {
           // console.log(this.value);
           // console.log($( this ).siblings());
           $( this ).parent().siblings(".form-group").show();
           $( this ).parent().siblings(".class_start_selection").show('slow');
            
            var batchType = $('#batch_types_id').find(":selected").val();
            var grade = $('#grades_id').find(":selected").val();
            console.log("batchType "+batchType);
            console.log("Grade " + grade);
            // console.log(batchType);
            var subject_id = "#subject" + this.value;
            var subject_id_for_select2 = this.value;
            // var full_url = "/getallbatch/" + subject_id + "/" + batchType;
            $( subject_id ).select2({
                allowClear: true,
                placeholder: 'Select batch',
                ajax: {
                    url: "/getallbatch",
                    dataType: 'json',
                    delay: 250,
                    tags: true,
                    data: function (params) {
                      return {
                            q: params.term, // search term
                            page: params.page,
                            subject_id: subject_id_for_select2,
                            batchType_id:batchType,
                            grades_id:grade
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

        }
        else{
            // $("#box_color").attr("class","box box-success");
            $( this ).parent().siblings(".form-group").hide('slow');
            var subject_id = "#subject" + this.value;
            $(subject_id).val('');
            $( this ).parent().siblings(".class_start_selection").hide('slow');
        }
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
        Student Module
        <small>it all starts here</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Student</a></li>
        <li class="active">Create Student</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- <div class="col-md-6"> -->
    <!-- Horizontal Form -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Student Update Page</h3>
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
        <!-- /.box-header -->
        <!-- form starts here -->
        
		{!! Form::open(array('url' => '/student_update_process/'.$getStudent->id.'/', 'id' => 'add_user_form', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data')) !!}
        
        {!! csrf_field() !!}
		{{ method_field('PATCH') }}

        <div class="box-body">
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Fullname*</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Student name" value="{{$getStudent->name}}">
                </div>
                
                <div class="form-group">
                    <label for="student_email" >Email</label>
                    <input type="email" class="form-control" id="student_email" name="student_email" size="35" value="{{ $getStudent->student_email }}">
                    
                </div>
                
                <div class="form-group">
                    <label for="fathers_name">Father's name</label>
                    <input type="text" class="form-control" id="fathers_name" name="fathers_name" placeholder="Enter Father's name" value="{{$getStudent->fathers_name}}">
                </div>
                
                <div class="form-group">
                    <label for="mothers_name">Mother's name</label>
                    <input type="text" class="form-control" id="mothers_name" name="mothers_name" placeholder="Enter Mother's name" value="{{$getStudent->mothers_name}}">
                </div>
                
                <div class="form-group">
                    <label for="student_phone_number">Student's Phone Number</label>
                    <input type="text" class="form-control" id="student_phone_number" name="student_phone_number" placeholder="Enter Phone number" value="{{$getStudent->student_phone_number}}">
                </div>
                
                <div class="form-group">
                    <label for="guardian_phone_number">Guardian's Phone Number</label>
                    <input type="text" class="form-control" id="guardian_phone_number" name="guardian_phone_number" placeholder="Enter additinal Phone number" value="{{ $getStudent->guardian_phone_number }}">
                </div>

                <div class="form-group">
                    <label for="driving_license_number" >Car Registration Number</label>
                    <input type="text" class="form-control" id="driving_license_number" name="driving_license_number" size="255" placeholder="Enter Driving License Number">
                </div>                
                
                <div class="form-group">
                    <label for="pic" >Upload Photo</label>
                    <input type="file" name="pic" id="pic">
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="schools_id" >School</label>
                        <select class="form-control" name="schools_id">
                        @if ($getStudent->school)
                            <option value="{{$getStudent->school->id}}">{{$getStudent->school->name}}</option>
                            @foreach ($Schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        @else
                            <option value="default">Choose...</option>
                            @foreach ($Schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        @endif
                        </select>
                </div>
                
                <div class="form-group">
                    <label for="batch_types_id" >Education Board</label>
                    <select class="form-control" id="batch_types_id" name="batch_types_id">
                        @if ($getStudent->batch_type)
                            <option value="{{ $getStudent->batch_type->id }}">{{ $getStudent->batch_type->name}}</option>
                            @foreach ($batchTypes as $batchType)
                                <option value="{{ $batchType->id }}">{{ $batchType->name }}</option>
                            @endforeach
                        @else
                            <option value="default">Choose...</option>
                            @foreach ($batchTypes as $batchType)
                                <option value="{{ $batchType->id }}">{{ $batchType->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label for="subjects_id">Choose Subject</label>
                    @if( ! $getStudent->subject->isEmpty() )
                    @foreach($Subjects as $subject)
                    <div class="checkbox">

                        @foreach($getStudent->subject as $selected_subject)
                            @if($selected_subject->id === $subject->id)
                                <label>
                                    <input class="sub_checkbox" type="checkbox" name="subject[]" value="{{ $subject->id }}" checked>
                                  {{ $subject->name }}
                                </label>
                                <div class="form-group batchSelection">
                                    <select class="form-control select2" name="batch_name[]" id="{{ 'subject' . $subject->id }}" ></select>
                                </div>
                                <div class="input-group date class_start_selection">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input id="{{ 'class_start_date' . $subject->id }}" type="text" class="form-control ref_date" name="joining_date[]" autocomplete="off">
                                </div>
                              @break
                            <!-- @elseif($selected_subject === end($getStudent->subject)) -->
                            @elseif($getStudent->subject->last()->id == $selected_subject->id)
                                <label>
                                    <input class="sub_checkbox" type="checkbox" name="subject[]" value="{{ $subject->id }}">
                                    {{ $subject->name }}
                                </label>
                                <div class="form-group batchSelection" style="display:none;">
                                    <select class="form-control select2" name="batch_name[]" id="{{ 'subject' . $subject->id }}" ></select>
                                </div>
                                <div class="input-group date class_start_selection" style="display:none;">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input id="{{ 'class_start_date' . $subject->id }}" type="text" class="form-control ref_date" name="joining_date[]" autocomplete="off">
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endforeach
                    @else
                    @foreach ($Subjects as $subject)
                    <div class="checkbox">
                        <label>
                          <input class="sub_checkbox" type="checkbox" name="subject[]" value="{{ $subject->id }}">
                          {{ $subject->name }}
                        </label>
                        <div class="form-group batchSelection" style="display:none;">
                            <select class="form-control select2" name="batch_name[]" id="{{ 'subject' . $subject->id }}" ></select>
                        </div>
                        <div class="input-group date class_start_selection" style="display:none;">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input id="{{ 'class_start_date' . $subject->id }}" type="text" class="form-control ref_date" name="joining_date[]" autocomplete="off">
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>


            </div>
            <!-- /.col -->
            <div class="col-md-1"></div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">Submit</button>
        </div>
        <!-- /.box-footer -->
        {!! Form::close() !!}
        <!-- /.form ends here -->
    </div>
    <!-- /.box -->
    <!-- </div> -->
</section>
<!-- /.content -->

@endsection

