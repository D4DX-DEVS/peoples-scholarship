@extends('layouts.dashboard')

@section('title', 'Admin | Select Meeting Applications')

@section('content')
  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

<div class="box table-responsive">
	<div class="box-header">
		<h3 class="box-title">Select Applications for meeting "{{$meeting->serial_no}}"</h3>
	</div>	
	<form action="{{route('post-add-applications',['id'=>$meeting->id])}}" method="post">
	<div class="box-body">
	{!!csrf_field()!!}
		<table class="table table-hover" id="result-table">
		<thead>
			<th></th>
			<th>application number</th>
			<th>Contact no</th>
			<th>Status</th>
			<th>Applicant Name</th>
			<th>District</th>
			<th>Area</th>
		</thead>
		<tfoot>
			<th></th>
			<th>application number</th>
			<th>Contact no</th>
			<th>Status</th>
			<th>Applicant Name</th>
			<th>District</th>
			<th>Area</th>
		</tfoot>
		<tbody>
		<div class="form-group has-feedback {{ ($errors->has('applications_selected')) ? 'has-error' : '' }}">
		@if($errors->has('applications_selected'))
                    <label class="control-label" for="inputError"><i class="fa fa-exclamation"></i> Please select at least one application</label>
                  @endif
         </div>
		@foreach ($applications as $application)
			<tr>
				<td>{{$application->id}}</td>
				<td><label class="control-label"><input type="checkbox" name="applications_selected[]" value="{{$application->id}}">{{$application->refno}}</label></td>
				<td>{{$application->person->mobile}}</td>
				<td><span class="btn-primary status bg-blue}}">{{ $application->getStatus->status_text }}</span></td>
				<td>{{$application->person->personname}}</td>
				<td>{{$application->district->district}}</td>
				<td>{{$application->area->area}}</td>
			</tr>
		@endforeach
		</tbody>
		</table>
	</div>
	<div class="box-footer">
		<div class="pull-right">
			<input type="submit" name="submit" class="btn btn-success" value="Add">
			<a href="{{route('meeting-applications',['id'=>$meeting->id])}}" class="btn btn-success cancel"> Cancel</a>
		</div>
	</div>
	</form>
</div>

@stop
@section('js')
@parent
<script type="text/javascript">
  	$(document).ready(function(){
	// Setup - add a text input to each footer cell
        $('#result-table tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        } );
	var table= $('#result-table').DataTable({
		"order": [ 0, "desc" ],
		"columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }]
		});
	// Apply the search
         table.columns().every( function () {
             var that = this;
             $( 'input', this.footer() ).on( 'keyup change', function () {
                 if ( that.search() !== this.value ) {
                     that
                         .search( this.value )
                         .draw();
                 }
             } );
         } );    
        });
</script>
@stop