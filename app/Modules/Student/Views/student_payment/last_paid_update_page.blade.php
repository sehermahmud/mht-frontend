@extends('master')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('../../plugins/datepicker/datepicker3.css')}}">

<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('scripts')
<!-- DataTables -->
<!-- bootstrap datepicker -->
<script src="{{asset('../../plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/jQuery/jquery.form.min.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>

<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('plugins/momentjs/moment.min.js')}}"></script>
<!-- <script src="http://www.position-absolute.com/creation/print/jquery.printPage.js" ></script> -->
<!-- <script src="{{asset('plugins/jqueryPrintArea/jquery.PrintArea.js')}}" ></script> -->
<script type="text/JavaScript" src="{{asset('plugins/JQueryPrintJS/jQuery.print.js')}}" ></script>
<!-- bootstrap datepicker -->


<script>
    $(document).ready(function () {     

           

        var table = $('#all_user_list').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/student/get_all_batches_for_last_paid_update')}}",
                    'data': {
                       student_id: {{ $studentDetails->id }},
                    },
                },
            "initComplete": function(settings, json) {
                $('.ref_date').datepicker({
                  format: 'dd/mm/yyyy',
                  autoclose: true
                });
                
                $(".update_button").click(function() {
                    var date_value = '.update_'+this.id;
                    $.post( "/student/last_payment_date_update",{ 
                        student_id: {{ $studentDetails->id }}, 
                        batch_id: this.id,
                        last_paid_date: $(date_value).val()
                    })
                    .done(function( data ) {
                        console.log(data);
                        location.reload();
                    });
                });
              
            },
            "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "price"},
                    {"data": "LastPaidDate"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ]
        });

    });
</script>

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Invoice Details History
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Students</a></li>
        <li class="active">Invoice Details History</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">            

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Student name: <b>{{ $studentDetails->name }}</b></h3>
                </div>
                <div class="box-header">
                	<h3 class="box-title">Phone number: <b>{{ $studentDetails->phone_home }}</b></h3>
                </div>
                <div class="box-header">
                	<h3 class="box-title">Father's name: <b>{{ $studentDetails->fathers_name }}</b></h3>
                </div>    
                
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="all_user_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Batch Id</th>
                                <th>Batch Name</th>
                                <th>Price</th>
                                <th>Last Paid Date</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <!-- user list -->
                        </tbody>                        
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@endsection

