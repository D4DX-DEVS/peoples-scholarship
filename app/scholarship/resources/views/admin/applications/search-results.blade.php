@extends('layouts.dashboard')

@section('title', 'Admin | Search Listing')

@section('content_header')
    <h1>Applications - Search List</h1>
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
		<h3 class="box-title">Applications</h3>
		<div class="pull-right">
		@if($status!=="all")
			<h3 class="box-title">Status :</h3> <strong><span id="status">{{$status}}</span></strong>
		@endif
		</div>
		
	</div>
	<div class="box-body table-responsive" style="min-height:300px;over-flow:hidden">
		<div id="tfilter"></div>
		<table class="table table-bordered table-hover" id="result-table">
			<thead>
				<th>ID</th>
				<th>Appl.ID</th>
				<th>Applicant Name</th>
				<th>Address</th>
				<th>Status</th>
				<th>Unit</th>
				<th>Area</th>
				<th>District</th>
				<th>_______Actions_______</th>
			</thead>
			<tbody>
				@foreach($applications as $application)
				<tr>
					<td>{{$application->id}}</td>
					<td><a href="{{route('view-application',['id'=> $application->id])}}" target="_blank">{{$application->refno}}</a></td>
					<td>{{$application->applicant_name}}</td>
					<td>{{$application->person->address}}</td>
					<td>
					<span class="label status bg-{{$application->getStatus->status_color}}">{{$application->getStatus->status_text}}</span>
					@if ($application->grant_status>0 )
						<span class="label status bg-{{$application->getGrantStatus($application->grant_status)['bgColour']}}">{{$application->getGrantStatus($application->grant_status)['statusText']}}</span>
					@endif
					</td>
					<td>
					@if ($application->status!==1)
						@if(count($application->getLatestStatistic())!=0)
							{{date('d M Y',strtotime($application->getLatestStatistic()->date))}}
						@endif
					@endif
					</td>
					<td>{{$application->unit->unit}}</td>
					<td>{{$application->area->area}}</td>
					<td>{{$application->district->district}}</td>
					<td><div class="btn-group">
					@if($application->status!==8 && $application->status!==10)
						<a href="{{ route('admin-app-edit',['id'=> $application->id,'persid'=>$application->persid])}}" target="_blank"> <button title="Edit" class="btn btn-warning btn-xs"> <i class="fa fa-pencil"></i> </button> </a>
					@endif
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
        {"extend":'excel',
        	"exportOptions":{
        		"columns":':visible'
        	}
    },
    'colvis'],
         "order": [[ 0, "desc" ]],
         "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
            	"targets":[7],
            	"visible":false
            }
        ],
            initComplete: function () {
            this.api().columns([10,9,8,7,6]).every( function () {
                var column = this;
                var select_name;
                
                if (column[0][0] == 10) {
                	select_name = 'district';
                }

                if (column[0][0] == 9) {
                	select_name = 'area';
                }


                if (column[0][0] == 8) {
                	select_name = 'unit';
                }

                if (column[0][0] == 6) {
                	select_name = 'scheme';
                }

                if (column[0][0] == 7) {
                	select_name = 'department';
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
  		$('.application-delete').click(function(){
  			return confirm('Are you sure you want to delete? All cheques, loan details etc associated with this application will be deleted.');
  		})
  });
  </script>
 @stop