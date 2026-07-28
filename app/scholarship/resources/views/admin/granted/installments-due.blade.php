@extends('layouts.dashboard')
@section('title', 'Admin | Installments due')

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
		<h3 class="box-title">Installments due</h3>
	</div>
	<div class="box-body table-responsive" style="min-height:300px;over-flow:hidden">
		<div id="tfilter"></div>
		<table class="table table-responsive" id="result-table">
			<thead>
				<th>application number</th>
				<th>Name</th>
				<th>Installment number</th>
				<th>due date</th>
				<th>status</th>
				<th></th><!-- due date for sorting-->
			</thead>
			<tfoot>
				<th>application number</th>
				<th>Name</th>
				<th>installment number</th>
				<th>due date</th>
				<th>status</th>
				<th></th>
			</tfoot>
			<tbody>
				@foreach($installments as $installment)
				<tr>
				<td><a href="{{route('view-application',['id'=>$installment->application->id])}}" target="_blank">{{$installment->application->refno}}</a></td>
				<td><{{$installment->application->applicant_name}}</td>
				<td><a href="{{route('edit-installments',['id'=>$installment->application->id])}}" target="_blank">{{$installment->installment_number}}</a></td>
				<td>@if ($installment->due_date=='0000-00-00 00:00:00' || $installment->due_date==null)
					Not Scheduled
				@else
					{{date('d-M-Y',strtotime($installment->due_date))}}
				@endif</td>
				<td><span class="label status bg-{{$installment->getStatus($installment->status)['bgColour']}}" >{{$installment->getStatus($installment->status)['statusText']}}</span></td>
				<td>{{$installment->due_date}}</td>
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
  	$(document).ready(function(){
  		$('.file-delete').click(function(){
  			return confirm('Are you sure you want to delete? All cheques, loan details etc associated with this file will be deleted.');
  		})

  		// Setup - add a text input to each footer cell
        $('#result-table tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        } );
     

   
     var table= $('#result-table').DataTable({ 
      "order": [[ 4, "asc" ]],
      "columnDefs": [
            {
                "targets": [ 4 ],
                "visible": false,
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