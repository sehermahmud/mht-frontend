@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
@endsection

@section('scripts')
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
<script>

$(document).ready(function () {

    // // initialize tooltipster on form input elements
    // $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
    //     trigger: 'custom', // default is 'hover' which is no good here
    //     onlyOne: false, // allow multiple tips to be open at a time
    //     position: 'right'  // display the tips to the right of the element
    // });

    // // initialize validate plugin on the form
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
    //         name: {required: true, minlength: 3},
    //         email: {required: true, email: true},
    //         description: {required: true},
    //         teacher_percentage: {required: true},
    //         password: {required: true, minlength: 6},
    //         password_re: {required: true, equalTo: "#password"},
    //     },
    //     messages: {
    //         name: {required: "Please give fullname"},
    //         email: {required: "Insert email address"},
    //         description: {required: 'Insert Description'},
    //         teacher_percentage: {required: 'Teacher Percentage'},
    //         password: {required: "Six digit password"},
    //         password_re: {required: "Re-enter same password"},
    //     }
    // });


});



</script>

@endsection

@section('side_menu')

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Teacher Module
        <small>it all starts here</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Teacher</a></li>
        <li class="active">Edit Teacher Users</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- <div class="col-md-6"> -->
    <!-- Horizontal Form -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Teacher Edit Page</h3>
            <div class="form-group">
                @if (count($errors) > 0)
                <div class="alert alert-danger alert-login col-sm-4">
                    <ul class="list-unstyled">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        <!-- /.box-header -->
        <!-- form starts here -->
        {!! Form::open(array('url' => 'teacher_update_process'.'/'.$getTeacher->id.'/', 'id' => 'add_user_form', 'class' => 'form-horizontal')) !!}
        {!! csrf_field() !!}
        {{ method_field('PATCH') }}
        <div class="box-body">
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Fullname*</label>
                    
                        <input type="text" class="form-control" id="name" name="name" value="{{ $getTeacher->user->name }}">
                    
                </div>
                <div class="form-group">
                    <label for="email" >Email*</label>
                    
                        <input type="email" class="form-control" id="email" name="email" value="{{ $getTeacher->user->email }}">
                    
                </div>
                <div class="form-group">
                    <label for="description">description*</label>
                    
                        <input type="text" class="form-control" id="description" name="description" value="{{ $getTeacher->description }}">
                    
                </div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="teacher_percentage">Percentage*</label>
                        <input type="number" step=".01" class="form-control" id="teacher_percentage" name="teacher_percentage" value="{{ $getTeacher->teacher_percentage }}">
                </div>
                <div class="form-group">
                    <label for="password">Password*</label>
                    
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                    
                </div>
                <div class="form-group">
                    <label for="upassword_re">Confirm Password*</label>
                    
                        <input type="password" class="form-control" id="password_re" name="password_re" placeholder="Enter password again">
                    
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

