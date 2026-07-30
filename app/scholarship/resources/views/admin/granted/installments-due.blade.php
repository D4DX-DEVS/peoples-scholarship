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
				{{-- Rows are loaded ten at a time from dues-data. --}}
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
     

   
     var table = $('#result-table').DataTable({
      processing: true,
      serverSide: true,
      deferRender: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      ajax: { url: "{{ route('dues-data') }}" },
      // Column 5 holds the raw due date and only exists to sort column 3 by.
      order: [[ 5, "asc" ]],
      columnDefs: [{ targets: [5], visible: false }]
     });

     // Apply the search. Debounced, because each keystroke now costs a
     // request to the server rather than a filter over rows already loaded.
         table.columns().every( function () {
             var that = this;
             var timer = null;

             $( 'input', this.footer() ).on( 'keyup change', function () {
                 var input = this;
                 clearTimeout( timer );
                 timer = setTimeout( function () {
                     if ( that.search() !== input.value ) {
                         that.search( input.value ).draw();
                     }
                 }, 350 );
             } );
         } );    
  });
  </script>
 @stop