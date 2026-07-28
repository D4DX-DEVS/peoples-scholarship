@extends('layouts.dashboard')
@section('adminlte_css')
  @parent
  <style type="text/css">
@media print
{
.noprint {display:none;}
.dataTables_filter, .dataTables_info, .dataTables_length, .dataTables_paginate { display: none; }
}
</style>
@stop
@section('title', 'Admin | Granted Listing')

@section('content_header')
    <h1>Granted List</h1>
@stop

@section('content')
  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

<div class="row">
<div class="col-xs-12">
	<div class="box">
	<div class="box-header">
		<h3 class="box-title">Applications Granted</h3>=		
	</div>
	<div class="box-body table-responsive" style="min-height:300px;over-flow:hidden">
		<div id="tfilter"></div>
		<table class="table table-bordered table-hover" id="result-table">
			<thead>
				<th>ID</th>
				<th>Appl. ID</th>
				<th width="130">Applicant Name</th>
				<th width="150">Address</th>
				<th>Status</th>
				<th>Granted Date</th>
				<th>Category</th>
				<th>Unit</th>
				<th>Area</th>
				<th>District</th>
				<th width="80">Actions</th>
			</thead>
			<tbody>
				@foreach($applications as $application)
				<tr>
					<td>{{$application->id}}</td>
					<td>{{$application->refno}}</td>
					<td>{{$application->person->personname}}</td>
					<td>{{$application->person->address}}</td>
					<td>
						@if ($application->grant_status===6)
							Documents Waiting
						@elseif($application->grant_status===7)
							Forwaded to Accounts
						@elseif($application->grant_status===8)
							Installment Due
						@elseif($application->grant_status===9)
							Completed						
						@else
						Undefined
						@endif
					</td>
					<td>
					@if ($application->status!==1)
					{{date('d M Y',strtotime($application->granted_date))}}
					@endif
					</td>
					<td>{{$application->category->catname}}</td>
					<td>{{$application->unit->unit}}</td>
					<td>{{$application->area->area}}</td>
					<td>{{$application->district->district}}</td>
					<td><div class="btn-group">
						<a href="{{ route('admin-app-edit',['id'=> $application->id,'pers_id'=>$application->persid])}}" target="_blank" class="noprint"> <button title="Edit" class="btn btn-warning btn-xs"> <i class="fa fa-pencil"></i> </button> </a>
						<a href="{{ route('edit-installments',['id'=> $application->id])}}" target="_blank" class="noprint"> <button title="Installments" class="btn btn-info btn-xs"> <i class="fa fa-money"></i> </button> </a>
					</div>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	</div>	
	</div>
</div>

@stop
@section('js')
  @parent
  <script type="text/javascript">
  	var table = $("#result-table").DataTable({
  		"dom" : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
					"<<'col-md-5'i><'col-md-6 pull-right'p>>",
     	"buttons": [{
        "extend":'print',
        "exportOptions":{
        	"columns":':visible',
        }    },
        {
        "extend":'excel',
        "exportOptions":{
        	"columns":':visible',
        }    },
        "colvis"],
         "order": [[ 0, "desc" ]],
         "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
            	"targets":[8],
            	"visible":false
            },
            {
            	"targets":[4],
            	"visible":false
            }
        ],
            initComplete: function () {
            this.api().columns([4,6,7,8,9]).every( function () {
                var column = this;
                var select_name;
                
                if (column[0][0] == 4) {
                	select_name = 'grant type';
                }

                if (column[0][0] == 6) {
                	select_name = 'Category';
                }

                if (column[0][0] == 7) {
                	select_name = 'unit';
                }

                if (column[0][0] == 8) {
                	select_name = 'area';
                }

                if (column[0][0] == 9) {
                	select_name = 'district';
                }

                var select = $('<select id="'+select_name+'" class="form-c"><option value="">Filter by '+select_name+'</option></select>')
                    .appendTo( $("#tfilter") )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
  	});
  	$(document).ready(function(){
  		if($('#status').text()!= ""){
  			var column = table.column(4);
  			// Toggle the visibility
	        column.visible( ! column.visible() );
  		}
  		$('.file-delete').click(function(){
  			return confirm('Are you sure you want to delete? All cheques, loan details etc associated with this file will be deleted.');
  		})
  });
  </script>
 @stop