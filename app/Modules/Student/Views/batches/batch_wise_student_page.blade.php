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

    $("#all_batch_subject_wise").click(function() {
        console.log($('select[name=subjects_id]').val());
        var table = $('#all_batches_datatable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "destroy": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                    'url': "{{URL::to('/students_get_all_batches_for_a_subject')}}",
                    'data': {
                       subjects_id: $('select[name=subjects_id]').val(),
                    },
                },
            "columns": [
                    {"data": "name"},
                    {"data": "schedule"},
                    {"data": "teacher_detail.user.name"},
                    {"data": "total_number_of_students"},
                    {"data": "number_of_paid_students"},
                    {"data": "number_of_unpaid_students"},
                    {"data": "total_expected_amount"},
                    {"data": "total_paid_amount"},
                    {"data": "total_unpaid_amount"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                let total_number_of_students = parseFloat(0);
                let number_of_paid_students = parseFloat(0);
                let number_of_unpaid_students = parseFloat(0);
                let total_expected_amount = parseFloat(0);
                let total_paid_amount = parseFloat(0);
                let total_unpaid_amount = parseFloat(0);

                for ( let i=0 ; i<aaData.length ; i++ ) {
                    total_number_of_students += parseFloat(aaData[i]['total_number_of_students']);
                    number_of_paid_students += parseFloat(aaData[i]['number_of_paid_students']);
                    number_of_unpaid_students += parseFloat(aaData[i]['number_of_unpaid_students']);
                    total_expected_amount += parseFloat(aaData[i]['total_expected_amount']);
                    total_paid_amount += parseFloat(aaData[i]['total_paid_amount']);
                    total_unpaid_amount += parseFloat(aaData[i]['total_unpaid_amount']);
                }
                $('#total_number_of_students').text(total_number_of_students);
                $('#number_of_paid_students').text(number_of_paid_students);
                $('#number_of_unpaid_students').text(number_of_unpaid_students);
                $('#total_expected_amount').text(total_expected_amount + ' /-');
                $('#total_paid_amount').text(total_paid_amount + ' /-');
                $('#total_unpaid_amount').text(total_unpaid_amount + ' /-');
            },
        }); // #teacher_payment_datatable ends
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
        Batch Details
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Subject</a></li>
        <li class="active">Batches</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    
    <!-- Horizontal Form -->
    <div class="box box-primary">
        
        <div class="box-body">
        
            <div class="box-header with-border">
              <h3 class="box-title">Choose a subject to Show Batches</h3>
            </div>
            <div class="box-body">
                <div class="row">
	                
	                <div class="col-xs-6">
                    	<label for="schools_id" >Subjects</label>
	                    <select class="form-control" name="subjects_id">
		                    @foreach ($getSubjects as $subject)
		                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
		                    @endforeach
		                </select>
	                </div>
	                
	                <div class="col-xs-6">
	                    <label for="" ></label>
	                    <button type="submit" id="all_batch_subject_wise" class="btn btn-block btn-success">Show</button>
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
                <h4>Batches</h4>      
        </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="all_batches_datatable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch Name</th>
                            <th>Schedule</th>
                            <th>Teacher</th>
                            <th>Total number of students</th>
                            <th>Number of Paid students</th>
                            <th>Number of Due students</th>
                            <th>Total Expected Amount /-</th>
                            <th>Total Paid Amount /-</th>
                            <th>Total Due Amount /-</th>
                            <th>Search for all the students</th>                            
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th id="total_number_of_students"></th>
                            <th id="number_of_paid_students"></th>
                            <th id="number_of_unpaid_students"></th>
                            <th id="total_expected_amount"></th>
                            <th id="total_paid_amount"></th>
                            <th id="total_unpaid_amount"></th>
                            <th></th> 
                        </tr>
                    </tfoot>
                    <tbody>                            
                        
                    </tbody>                        
                </table>
            </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->

@endsection

