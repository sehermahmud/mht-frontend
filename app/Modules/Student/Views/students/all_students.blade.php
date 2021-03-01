@extends('master')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('scripts')
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
    $(document).ready(function () {        
        var table = $('#all_students_list').DataTable({
            "paging": true,
            "pageLength": 50,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{URL::to('/students_get_students')}}",
            "columns": [
                    {"data": "name"},
                    {"data": "student_phone_number"},
                    {"data": "guardian_phone_number"},
                    {"data": "driving_license_number"},
                    {"data": "student_email"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ]
        });

        // Delete Customer
       $('#confirm_delete').on('show.bs.modal', function(e) {
           var $modal = $(this),
               user_id = e.relatedTarget.id;
               console.log(user_id);

           $('#delete_customer').click(function(e){    
               // event.preventDefault();
               $.ajax({
                   cache: false,
                   type: 'POST',
                   url: 'student/' + user_id + '/delete',
                   data: user_id,
                   success: function(data) {
                       console.log("Deleted Successfully");
                       table.ajax.reload(null, false);
                       $('#confirm_delete').modal('toggle');
                   }
               });
           });
        })
    });
</script>

@endsection



@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Students
        <small>all student list</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Students</a></li>
        <li class="active">All Students</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">            

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Student list</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="all_students_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Student name</th>
                                <th>Student number</th>
                                <th>Guardian number</th>
                                <th>Car reg. number</th>
                                <th>e-mail address</th>
                                <th>Action</th>                            
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

     <!-- Delete Customer Modal -->
   <div class="modal fade" id="confirm_delete" role="dialog">
       <div class="modal-dialog">
           <!-- Modal content-->
           <div class="modal-content">
               <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   <h4 class="modal-title">Remove Parmanently</h4>
               </div>
               <div class="modal-body">
                   <p>Are you sure about this ?</p>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger" id="delete_customer">Delete</button>
                   <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
               </div>
           </div>
           <!-- /. Modal content ends here -->
       </div>
   </div>
   <!--  Delete Customer Modal ends here -->    

</section>
<!-- /.content -->
@endsection

