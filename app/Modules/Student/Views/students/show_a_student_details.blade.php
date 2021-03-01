@extends('master')

@section('css')

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


<script>
    $(document).ready(function () {
        var table = $('#student_payment_history').DataTable({
            "paging": false,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/get_student_payment_history')}}",
                    'data': {
                       student_id: "{{ $getStudent->id }}",
                    },
                },
            "columns": [
                    {"data": "name"},
                    {"data": "price"},
                    {"data": "pivot.last_paid_date"},
                ]
        });
        var refund_table = $('#get_student_refund_history').DataTable({
            "paging": false,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/get_student_refund_history')}}",
                    'data': {
                       student_id: "{{ $getStudent->id }}",
                    },
                },
            "columns": [
                    {"data": "invoice_detail.invoice_master.serial_number"},
                    {"data": "invoice_detail.invoice_master.payment_date"},
                    {"data": "invoice_detail.batch.name"},
                    {"data": "invoice_detail.payment_from"},
                    {"data": "amount"},
                ],
        });
        var transaction_table = $('#student_transaction_history').DataTable({
            "paging": false,
            "lengthChange": true,
            "searching": false,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/get_student_transaction_history')}}",
                    'data': {
                       student_id: "{{ $getStudent->id }}",
                    },
                },
            "columns": [
                    {"data": "invoice_master.serial_number"},
                    {"data": "invoice_master.payment_date"},
                    {"data": "batch.name"},
                    {"data": "payment_from"},
                    {"data": "price"},
                ],
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
        Student Detail Information
        
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Student</a></li>
        <li class="active">Detail</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Horizontal Form -->
    <div class="box box-info">
        
        
            <div class="box-body">
            
            
                <div class="col-md-4">
                    
                    <div class="form-group">
                        <img src="{{ URL::to('/') }}/{{ $getStudent->students_image }}" class='profile-user-img img-responsive' height='200' width='200' alt='Student profile picture'>
                    </div>
                    <div class="form-group">
                        <h2 class="profile-username text-center">Student Name : {{ $getStudent->name }}</h2>
                    </div>
                    <div class="form-group">
                        <h3 class="text-muted text-center">Permanent ID : {{ $getStudent->student_permanent_id }}</h3>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fathers_name" >Fathers Name</label>
                        <p>{{ $getStudent->fathers_name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="mothers_name" >Mothers Name</label>
                        <p>{{ $getStudent->mothers_name }}</p>
                    </div>
                    <div class="form-group">
                      <label for="phone_home" >Student's Phone Number</label>
                      <p>{{ $getStudent->student_phone_number }}</p>
                    </div>
                    <div class="form-group">
                        <label for="phone_away" >Guardian's Phone Number</label>
                        <p>{{ $getStudent->guardian_phone_number }}</p>  
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" >Email</label>
                        <p>{{ $getStudent->student_email }}</p>
                    </div>
                    <div class="form-group">
                        <label for="school" >School</label>
                        @if ($getStudent->school)
                            <p>{{ $getStudent->school->name }}</p>
                        @else
                            <p></p>
                        @endif  
                    </div>
                    <div class="form-group">
                        <label for="batch_type">Education Board</label>
                        @if ($getStudent->batch_type)
                            <p>{{ $getStudent->batch_type->name }}</p>
                        @else
                            <p></p>
                        @endif 
                    </div>
                    <div class="form-group">
                        <label for="driving_license_number">Driving License Number</label>
                        <p>{{ $getStudent->driving_license_number }}</p>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->

            <!-- /.box-footer -->
        </form>
        <!-- /.form ends here -->
    </div>
    <!-- /.box -->


    <!-- Stydent Payment History -->
    <div class="box box-success">
        <div class="box-header animated fadeInUp">
                <h4><strong>Payment Status</strong></h4>
        </div><!-- /.box-header -->
            
            <div class="box-body">
                <table id="student_payment_history" class="table table-bordered table-striped animated fadeInUp">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>Unit Price</th>
                            <th>Last paid Date</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <!-- user list -->
                    </tbody>                        
                </table>
            </div><!-- /.box-body -->
    </div>
    <!-- /.box -->

    <!-- Stydent Refund History -->
    <div class="box box-primary">
        <div class="box-header animated fadeInUp">
                <h4><strong>All Refunds</strong></h4>
        </div><!-- /.box-header -->
            
            <div class="box-body">
                <table id="get_student_refund_history" class="table table-bordered table-striped animated fadeInUp">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Payment Date</th>
                            <th>Batch Name</th>
                            <th>Payment For</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <!-- user list -->
                    </tbody>                        
                </table>
            </div><!-- /.box-body -->
    </div>
    <!-- /.box -->


    <!-- Stydent Transaction History -->
    <div class="box box-warning">
        <div class="box-header animated fadeInUp">
                <h4><strong>All Transactions</strong></h4>
        </div><!-- /.box-header -->
            
            <div class="box-body">
                <table id="student_transaction_history" class="table table-bordered table-striped animated fadeInUp">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Payment Date</th>
                            <th>Batch Name</th>
                            <th>Payment For</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>                            
                        <!-- user list -->
                    </tbody>                        
                </table>
            </div><!-- /.box-body -->
    </div>
    <!-- /.box -->




</section>
<!-- /.content -->

@endsection

