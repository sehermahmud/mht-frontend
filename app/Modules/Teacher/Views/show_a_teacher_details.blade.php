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
<script>
    // add the rule here
    $.validator.addMethod("valueNotEquals", function (value, element, arg) {
        return arg != value;
    }, "Value must not equal arg.");

    $(document).ready(function () {
        var table = $('#all_user_list').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{URL::to('/get_batches/'.$getTeacher->id)}}",
            "columns": [
                    {"data": "schedule"},
                    {"data": "name"},
                    {"data": "price"},
                    {"data": "expected_students"},
                    {"data": "total_students"},
                    {"data": "start_date"},
                    {"data": "end_date"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ]
        });                  



        //Date picker for Start Date
        $('#start_date').datepicker({
          format: 'dd/mm/yyyy',
          autoclose: true
        });

        //Date picker End Date
        $('#end_date').datepicker({
          format: 'dd/mm/yyyy',
          autoclose: true
        });

        //Date picker for Start Date Edit
        $('#start_date_edit').datepicker({
          format: 'dd/mm/yyyy',
          autoclose: true
        });

        //Date picker End Date Edit
        $('#end_date_edit').datepicker({
          format: 'dd/mm/yyyy',
          autoclose: true
        });


        // Edit Batch
        $('#confirm_edit').on('shown.bs.modal', function(e) {
            
           var $modal = $(this),
            user_id = e.relatedTarget.id;
            console.log(user_id);
           
            $.ajax({
               cache: false,
               type: 'GET',
               url: '/batch/' + user_id + '/edit',
               data: user_id,
               success: function(data) {
                   // console.log(data);
                   $('input#batch_id').val(data.id);
                   $('input#price').val(data.price);
                   $('input#schedule').val(data.schedule);
                   $('input#start_date_edit').val(data.start_date);
                   $('input#end_date_edit').val(data.end_date);
                   $('option#edit_batch_types_id').text(data.batch_type.name);
                   $("option#edit_batch_types_id").attr("value",data.batch_types_id);
                   $('option#edit_grades_id').text(data.grade.name);
                   $("option#edit_grades_id").attr("value",data.grades_id);
                   $('option#edit_subject_id').text(data.subject.name);
                   $("option#edit_subject_id").attr("value",data.subjects_id);
                   $("input#expected_students_edit").attr("value",data.expected_students);
                   //table.ajax.reload(null, false);
                   // $('#confirm_edit').modal('toggle');
               }
            });

            /* Batch Edit Form Submission */
            $('#edit_batch').click(function(e) {
               
              var edit_batch_form = $("#edit_batch_form").serialize();
              
              $.ajax({
                  type: "POST",
                  url: "/batch_update_process",
                  data: edit_batch_form,
                  success: function(data) {
                      // console.log(data);
                      location.reload();
                  },
                  error: function() {
                      // delete_msg
                      alert('Other infomation is related to this batch. So You can not delete it');
                  }
              });

              $('#confirm_edit').modal('toggle');
              table.ajax.reload(null, false);
              e.preventDefault();               
              
            });
        });



      var user_id = null;      
      // Delete Customer
     $('#confirm_delete').on('show.bs.modal', function(e) {
         var $modal = $(this);
         if(e.relatedTarget.id){
          user_id = e.relatedTarget.id;
         }
         
          console.log(user_id);
          if (user_id!=null) {
            $('#delete_batch').click(function(e){    
               $.ajax({
                   cache: false,
                   type: 'POST',
                   url: '/batch/' + user_id + '/delete',
                   data: user_id,
                   success: function(data) {
                       console.log("Deleted Successfully");
                       console.log(data);
                       table.ajax.reload(null, false);
                       $('#confirm_delete').modal('toggle');
                   },
                   error: function() {
                    $('div#delete_msg').html("<div class='alert alert-danger'><strong>Danger!</strong> This Batch is related to Other Information !!!</div>");
                      
                  }
               });
              });
          }
         
         $('#cancel_batch_modal').click(function(e){    
             user_id = null;
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
        Teacher Information
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Teacher</a></li>
        <li class="active">Teacher Detail Page</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Horizontal Form -->
    <div class="box box-info">
            <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Teacher's Information</h3>
            </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="name" >Teacher Name</label>
                        <p>{{ $getTeacher->user->name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="email" >Email</label>
                        <p>{{ $getTeacher->user->email }}</p>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="teacher_percentage" >Teacher's Percentage</label>
                        <p>{{ $getTeacher->teacher_percentage }}</p>  
                    </div>
                    <div class="form-group">
                        <label for="addrs" >Sucjects</label>
                        <p>{{ $getTeacher->description }}</p>  
                    </div>
                </div>
                <div class="col-md-4">
                    
                </div>
                
                <div class="col-md-4">
                </div>
            </div>
            <!-- /.box-body -->
    </div>
    <!-- /.box -->

  <!-- Horizontal Form -->
    <div class="box box-success">
        
        <div class="box-body">
        
            <div class="box-header with-border">
              <h3 class="box-title">Create a New Batch</h3>
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
            <div class="box-body">
                <div class="row">
                {!! Form::open(array('url' => 'create_new_batch_process','id' => 'add_batch_form')) !!}
                <div class="col-md-3">
                    <label for="batch_number" >Batch No.*</label>
                    <input type="number" min="1" class="form-control" name="batch_number" id="batch_number" value="1">
                </div>
                <div class="col-md-3">
                    <label for="price" >Price*</label>
                    <input type="text" class="form-control" name="price" id="price" placeholder="Price">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="batch_types_id" >Education Board*</label>
                        <select class="form-control" name="batch_types_id">
                                <option value="default">Choose...</option>
                                @foreach ($batchType as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="grades_id" >Grade*</label>
                        <select class="form-control" name="grades_id">
                            <option value="default">Choose...</option>
                            @foreach ($getGrades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="subjects_id" >Subject*</label>
                        <select class="form-control" name="subjects_id">
                            <option value="default">Choose...</option>
                            @foreach ($getSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- <div class="col-md-2">
                    <div class="form-group">
                        <label for="batch_id" >Schedule*</label>
                        <select class="form-control select2" name="batch_day_time[]" id="batch_id" multiple></select>
                    </div>
                </div> -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="batch_id" >Schedule*</label>
                        <input type="text" class="form-control" name="schedule" id="schedule" placeholder="Schedule">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date" >Start Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Select Start Date" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date" >End Date</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control" id="end_date" name="end_date" placeholder="Select End date" autocomplete="off">
                        </div>
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-3">
                    <label for="expected_students" >Expected Students*</label>
                    <input type="number" min="1" class="form-control" name="expected_students" id="expected_students" value="1">
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-6"> 
                  <input type="hidden" name="teacher_details_id" value="{{ $getTeacher->id }}">
                  <input type="hidden" name="teacher_details_users_id" value="{{ $getTeacher->user->id }}">
                  <div class="col-md-12">
                      <label for="" ></label>
                      <button type="submit" class="btn btn-block btn-success">Add Batch</button>
                  </div>
                {!! Form::close() !!}
              </div>
              </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    


    <!-- Horizontal Form -->
    <div class="box box-warning">
            <div class="box-header">
                <h4>
                    All Batches under <b>{{ $getTeacher->user->name }}</b>
                </h4>            
            </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="all_user_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Schedule</th>
                                <th>Batch Name</th>
                                <th>Price Tk/=</th>
                                <th>Expected Students</th>
                                <th>Total Number of Students</th>
                                <th>Start Date</th>
                                <th>End Date</th>
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




   <!-- Edit Batch Modal -->
   <div class="modal fade" id="confirm_edit" role="dialog">
       <div class="modal-dialog">
           <!-- Modal content-->
           <div class="modal-content">
           
               <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   <h4 class="modal-title">Edit Batch</h4>
               </div>
               {!! Form::open(array('id' => 'edit_batch_form')) !!}
               <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <label for="batch_number" >Batch No.*</label>
                            <input type="number" min="1" class="form-control" name="batch_number" id="batch_number" value="1">
                        </div>
                        <div class="col-xs-6">
                            <label for="price" >Price*</label>
                            <input type="text" class="form-control" name="price" id="price" placeholder="Price">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="batch_types_id" >Education Board*</label>
                                <select class="form-control" name="batch_types_id">
                                        <option id="edit_batch_types_id" value=""></option>
                                        @foreach ($batchType as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="grades_id" >Grade*</label>
                                <select class="form-control" name="grades_id">
                                    <option id="edit_grades_id" value=""></option>
                                    @foreach ($getGrades as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                        <!-- <div class="col-xs-2">
                            <div class="form-group">
                                <label for="batch_id" >Schedule*</label>
                                <select class="form-control select2" name="batch_day_time[]" id="batch_id" multiple></select>
                            </div>
                        </div> -->
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="batch_id" >Schedule*</label>
                                <input type="text" class="form-control" name="schedule" id="schedule" placeholder="Schedule">
                            </div>
                        </div>
                        <div class="col-xs-6">
                          <label for="subjects_id" >Subject*</label>
                          <select class="form-control" name="subjects_id">
                              <option id="edit_subject_id" value=""></option>
                              @foreach ($getSubjects as $subject)
                                  <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                              @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="start_date" >Start Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" id="start_date_edit" name="start_date" placeholder="Select Start Date">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="end_date" >End Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" id="end_date_edit" name="end_date" placeholder="Select end date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label for="expected_students" >Expected Students*</label>
                        <input type="number" min="1" class="form-control" name="expected_students" id="expected_students_edit">
                      </div>
                      <div class="col-md-6"></div>
                    </div>
               </div>
               <div class="modal-footer">
                    <input type="hidden" id="batch_id" name="batch_id">
                    <input type="hidden" name="teacher_details_id" value="{{ $getTeacher->id }}">
                    <input type="hidden" name="teacher_details_users_id" value="{{ $getTeacher->user->id }}">
                   <button type="submit" class="btn btn-warning" id="edit_batch">Edit</button>
                   <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
               </div>
            {!! Form::close() !!}
           </div>
           <!-- /. Modal content ends here -->
       </div>
   </div>
   <!--  Edit Batch Modal --> 








   <!-- Delete Batch Modal -->
   <div class="modal fade" id="confirm_delete" role="dialog">
       <div class="modal-dialog">
           <!-- Modal content-->
           <div class="modal-content">
               <div class="modal-header">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   <h4 class="modal-title">Remove Parmanently</h4>
               </div>
               <div class="modal-body">
                   <p >Are you sure about this ?</p>
                   <div id="delete_msg"></div>
                   
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-danger" id="delete_batch">Delete</button>
                   <button type="button" class="btn btn-default" id="cancel_batch_modal" data-dismiss="modal">Cancel</button>
               </div>
           </div>
           <!-- /. Modal content ends here -->
       </div>
   </div>
   <!--  Delete Batch Modal ends here -->  


</section>
<!-- /.content -->

@endsection

