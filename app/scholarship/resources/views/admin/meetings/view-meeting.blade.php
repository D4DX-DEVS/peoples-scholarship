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
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
@stop
@section('title', 'Admin | Meeting Detail')
@section('content_header')
    <h1>Meeting Details {{$meeting->serial_no}}</h1>
		<h5> Date: {{ date('d-m-Y', strtotime($meeting->date)) }}</h5>
@stop
<hr>
@section('content')

@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif
<div class="box noprint">
	<div class="box-header with-border">
		<div class="box-title"><strong>Serial no : {{$meeting->serial_no}}</strong></div>
	</div>
	<div class="box-body">
		<div class='row'>
                <div class="col-xs-3"><strong>Date</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{date('d-m-Y', strtotime($meeting->date))}}</div>
        </div>
        <div class='row'>
                <div class="col-xs-3"><strong>Type of meeting</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$meeting->meeting_type}}</div>
        </div>
        <div class='row'>
                <div class="col-xs-3"><strong>Time</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$meeting->time}}</div>
        </div>
		<div class='row'>
                <div class="col-xs-3"><strong>Participants & Remarks</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{!!nl2br($meeting->remarks)!!}</div>
        </div>
        <div class="row">
        	<div class="col-xs-3"><strong>No of Applications</strong></div>
            <div class="col-xs-1"><strong>:</strong></div>
            <div class="col-xs-2">{{$noofApplications}}</div>
            <div class="col-xs-2">
					
   	                	<a class="btn btn-info" href="{{route('meeting-applications',['id'=>$meeting->id])}}"><i class="fa fa-applications-o"></i>Applications</a>
					   
	        </div>
        </div>
	</div>
	<div class="box-footer">
		<a href="{{route('edit-meeting',['id'=>$meeting->id])}}" class="btn btn-success pull-right">Edit</a>
	</div>
</div>
@if (date('Y-m-d H:i:s')>$meeting->date)
	<div class="box"> <!-- Meeting summary -->
	<div class="box-header">
	<h3 class="box-title">Meeting {{$meeting->serial_no}} Summary</h3>
		<div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_11" data-toggle="tab">Granted Applications - {{sizeof($granted_applications)}}</a></li>
              <li><a href="#tab_12" data-toggle="tab">Pending Applications - {{sizeof($pending_applications)}}</a></li>
              <li><a href="#tab_13" data-toggle="tab">Rejected Applications - {{sizeof($rejected_applications)}}</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_11">
                <table class="table " id="granted-table">
                	<thead>
                		<th>App. ID</th>
                		<th>Name</th>
                		<th>Address</th>
                		<th class="noprint">Category</th>
                		<th>Unit</th>
                		<th>Area</th>
                		<th>District</th>
                		<th>Date</th>
                    <th>Further action</th>
                		<th>Amount</th>
                	</thead>
                	<tfoot>
                		<tr>
                			<th colspan="8" style="text-align:right">Total:</th>
                			<th>₹{{$granted_total}}</th>
                		</tr>
                	</tfoot>
                	<tbody>
                	@foreach ($granted_applications as $app)
                		<tr>
                		<td>{{$app->refno}}</td>
                		<td>{{$app->person->personname}}</td>
                		<td>{{$app->person->address}}</td>
                		<td class="noprint">{{$app->category->catname}}</td>
                		<td>{{$app->unit->unit}}</td>
                		<td>{{$app->area->area}}</td>
                		<td>{{$app->district->district}}</td>
                		<td>{{date('d-M-Y',strtotime($app->granted_date))}}</td>
						<td>{{$app->further_action}}</td>
                		<td>₹{{$app->amount_granted}}</td>
                		</tr>
                	@endforeach
                	</tbody>
                </table>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_12">
                <table class="table table-hover" id="pending-table">
                	<thead>
                		<th>App. ID</th>
                		<th>Name</th>
                		<th>Address</th>
                		<th>Category</th>
                		<th>Unit</th>
                		<th>Area</th>
                		<th>District</th>
                		<th>Date</th>
                        <th>Further action</th>
                		<th>Reason</th>
                	</thead>
                	<tbody>
                	@foreach ($pending_applications as $app)
                		<tr>
                		<td>{{$app->refno}}</td>
                		<td>{{$app->person->applicant_name}}</td>
                		<td>{{$app->person->address}}</td>
                		<td>{{$app->category->catname}}</td>
                		<td>{{$app->unit->unit}}</td>
                		<td>{{$app->area->area}}</td>
                		<td>{{$app->district->district}}</td>
                		<td>{{date('d-M-Y',strtotime($app->created_at))}}</td>
                        <td>{{$app->further_action}}</td>
                		<td>{!!$app->reason_status!!}</td>
                		</tr>
                	@endforeach
                	</tbody>
                </table>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_13">
                <table class="table table-hover" id="rejected-table">
                	<thead>
                		<th>App. ID</th>
                		<th>Name</th>
                		<th>Address</th>
                		<th>Category</th>
                		<th>Unit</th>
                		<th>Area</th>
                		<th>District</th>
                		<th>Date</th>
                    <th>Further action</th>
                		<th>Reason</th>
                	</thead>
                	<tbody>
                	@foreach ($rejected_applications as $app)
                		<tr>
                		<td>{{$app->refno}}</td>
                		<td>{{$app->person->applicant_name}}</td>
                		<td>{{$app->person->address}}</td>
                		<td>{{$app->category->catname}}</td>
                		<td>{{$app->unit->unit}}</td>
                		<td>{{$app->area->area}}</td>
                		<td>{{$app->district->district}}</td>
                		<td>{{date('d-M-Y',strtotime($app->created_at))}}</td>
                    <td>{{$app->further_action}}</td>
					<td>{!!$app->reason_status!!}</td>
                		</tr>
                	@endforeach
                	</tbody>
                </table>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
	</div>
	<div class="box-body"></div>
</div> <!-- ./Meeting summary -->
@endif

@stop
@section('js')
@parent
<script src="https://code.jquery.com/jquery-3.3.1.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js" type="text/javascript"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){


		$('#granted-table').DataTable({
			dom : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
					"<<'col-md-5'i><'col-md-6 pull-right'p>>",
			buttons: [
            {
                extend: 'print',
                text: 'Print all',
				title:'Meeting:{{$meeting->serial_no}} - Granted List',
                exportOptions: {
					columns: ':visible',
                    modifier: {
                        selected: null
                    }
                }
            },
            {
                extend: 'print',
					text: 'Print selected',
					title:'Meeting:{{$meeting->serial_no}} - Granted List',
					exportOptions:{
							columns:':visible',
						}  
			},
			{
					extend:'excel',
					exportOptions:{
						columns:':visible',
						}   
			},
            'colvis'
				],
		order: [[ 0, "desc" ]],	
        select: true
						
		});
		// $('#pending-table').DataTable({
		// 	"dom" : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
		// 			"<<'col-md-5'i><'col-md-6 pull-right'p>>",
     	// "buttons": [{
        // "extend":'print',
        // "exportOptions":{
        // 	"columns":':visible',
        // }    }]
		// });
		// $('#rejected-table').DataTable({
		// 	"dom" : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
		// 			"<<'col-md-5'i><'col-md-6 pull-right'p>>",
     	// "buttons": [{
        // "extend":'print',
        // "exportOptions":{
        // 	"columns":':visible',
        // }    }]
		// });
	});
</script>
@stop