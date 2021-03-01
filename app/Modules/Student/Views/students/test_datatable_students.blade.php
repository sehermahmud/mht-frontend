@extends('master')

@section('css')

<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">  
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('scripts')
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<!-- {!! $dataTable->scripts() !!}  -->
<script type="text/javascript">
    var table = $("#dataTableBuilder").DataTable({
        "serverSide": true,
        "processing": true,
        "ajax": {
            url: '',
            data: function (data) {
                data.subscription_type = $('#subscription_type').val();
                data.territory = $('#territory').val();
                data.sector = $('#sector').val();
            }
        },
       "columns": [{
            "name": "id",
            "data": "id",
            "title": "Id",
            "orderable": true,
            "searchable": true
        }, {
            "name": "name",
            "data": "name",
            "title": "Name",
            "orderable": true,
            "searchable": true
        }, {
            "name": "created_at",
            "data": "created_at",
            "title": "Created At",
            "orderable": true,
            "searchable": true
        }, {
            "name": "updated_at",
            "data": "updated_at",
            "title": "Updated At",
            "orderable": true,
            "searchable": true
        }],
            "dom": "Bfrtip",
            "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
        });
</script>

@endsection

@section('side_menu')

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    
</section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-xs-12">            
       <div class="box">
            <div class="box-header">
                <h3 class="box-title">Student list</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                {!! $dataTable->table() !!}
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