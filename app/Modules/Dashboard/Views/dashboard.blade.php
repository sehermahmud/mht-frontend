@extends('master')
@section('css')
<!-- jvectormap -->
<link rel="stylesheet" href="{{asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">
<style type="text/css">
  .my_class {
    color: Red;
  }
</style>
@endsection

@section('scripts')
<!-- Sparkline -->
<script src="{{asset('plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap -->
<script src="{{asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- ChartJS 1.0.1 -->
<script src="{{asset('plugins/chartjs/Chart.min.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js" /> -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{asset('dist/js/pages/dashboard2.js')}}"></script> -->
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
$(document).ready(function () {
   $('.count').each(function () {
      $(this).prop('Counter',0).animate({
          Counter: $(this).text()
      }, {
          duration: 1000,
          easing: 'swing',
          step: function (now) {
              $(this).text(Math.ceil(now));
          }
      });
    });

  var table = $('#all_user_list').DataTable({
        "paging": true,
        "pageLength": 50,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{URL::to('/get_all_batches_and_students')}}",
        "columns": [
                    {"data": "name"},
                    {"data": "schedule"},
                    {"data": "total_number_of_students"},
                    {"data": "number_of_paid_students"},
                    {"data": "number_of_unpaid_students"},
                    {"data": "total_expected_amount"},
                    {"data": "total_paid_amount"},
                    {"data": "total_unpaid_amount"},
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
            ],
        "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                   //  let total_engaged_students = 0;
                   //  for ( let i=0 ; i<aaData.length ; i++ ) {
                   //      total_engaged_students += parseInt(aaData[i]['total_number_of_students'], 10);
                   //  }
                   // $('.total_engaged_students').text(total_engaged_students);
                  
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
                  $('.total_engaged_students').text(total_number_of_students);
                  $('#total_number_of_students').text(total_number_of_students);
                  $('#number_of_paid_students').text(number_of_paid_students);
                  $('#number_of_unpaid_students').text(number_of_unpaid_students);
                  $('#total_expected_amount').text(total_expected_amount + ' /-');
                  $('#total_paid_amount').text(total_paid_amount + ' /-');
                  $('#total_unpaid_amount').text(total_unpaid_amount + ' /-');
        },
        "aoColumnDefs": [
                    { "sClass": "my_class", "aTargets": [ 7 ] }
        ]
    });
    

    // Chart Testing
    // var ctx = document.getElementById('myChart').getContext('2d');
    // var chart = new Chart(ctx, {
    //     // The type of chart we want to create
    //     type: 'bar',

    //     // The data for our dataset
    //     data: {
    //         labels: ["January", "February", "March", "April", "May", "June", "July"],
    //         datasets: [{
    //             label: "My First dataset",
    //             backgroundColor: 'rgb(255, 99, 132)',
    //             borderColor: 'rgb(255, 99, 132)',
    //             data: [0, 10, 5, 2, 20, 30, 45],
    //         }]
    //     },

    //     // Configuration options go here
    //     options: {}
    // });

});
</script>
@endsection


@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-navy color-palette"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Number of Students</span>
              <span class="info-box-number count">{{ $total_students }}</span>
              <span class="info-box-text">Total Number of Active Students</span>
              <span class="info-box-number count">{{ $total_active_students }}</span>
              <span class="info-box-text">Total Number of Engaged Students</span>
              <span class="info-box-number total_engaged_students"></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-cash"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Expected Amount</span>
              <span class="info-box-number count" style="float: left">{{ $total_expected_amount }}</span>
              <strong> &nbsp; /-</strong>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa ion-social-usd-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Paid Amount</span>
              <span class="info-box-number count" >{{ $total_paid_amount }}</span>
              <span class="info-box-text">Total Discount Amount</span>
              <span class="info-box-number">0</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="ion ion-social-usd"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Due Amount</span>
              <span class="info-box-number count" style="float: left">{{ $total_unpaid_amount }}</span>
              <strong> &nbsp; /-</strong>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        
      </div>
      <!-- /.row -->

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Batch list</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="all_user_list" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Batch Name</th>
                        <th>Schedule</th>
                        <th>Total number of Active students</th>
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
                  <!-- user list -->
              </tbody>                        
            </table>
        </div>
            <!-- /.box-body -->
    </div><!-- /.box -->

    <!-- Chart Testing -->
<!--     <div class="box box-danger">
        <div class="box-header">
            <h3 class="box-title">Due</h3>
        </div>
        
        <div class="box-body">

          <canvas id="myChart"></canvas>
            
        </div>
            
    </div> -->

   

    </section>
    <!-- /.content -->
@endsection