@extends('layouts.dashboard')
@section('adminlte_css')
  @parent
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
@stop
@section('title')
 Application Listing @if(isset($status)) &nbsp;&nbsp;Status :{{ $status}} @endif
@stop
@section('content_header')
    <h1>Applications</h1>
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
          <h2 class="box-title">Application Details
					@if(isset($status))
          &nbsp;&nbsp;Status :
          {{ $status}} @endif</h2>

        </div><!-- /.box-header -->
        <div class="box-body table-responsive " style="min-height:350px;over-flow:hidden">
        <div id="tfilter">
          <select id="filter-unit" class="form-c">
            <option value="">Filter by unit</option>
            @foreach($filterUnits as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach
          </select>
          <select id="filter-area" class="form-c">
            <option value="">Filter by area</option>
            @foreach($filterAreas as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach
          </select>
          <select id="filter-district" class="form-c">
            <option value="">Filter by district</option>
            @foreach($filterDistricts as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach
          </select>
        </div>

          <table class="table " id="result-table">
           <thead>
            <tr>
              <th width="60">ID</th>
              <th>Name</th>
              <th width="60">Date</th>
              <th>Phone</th>
              <th width="120">Unit</th>
              <th>Area</th>
              <th width="120">District</th>
              <th width="120">Course</th>
              <th width="100">Action</th>
            </tr>
            </thead>
             <tbody>
               {{-- Rows are loaded ten at a time from applications-data. --}}
             </tbody>
          </table>

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
</div>

@include('admin.grant-modal')
@include('admin.reject-modal')
@include('admin.rejection-reason-modal')

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

function rejectionReason(reason){
	//var content = $(this).data('content');
	$(".reason").html(content);
}
	function getAppModal(id,name){
		$("#modal-id").html(id);
		$("#modal-name").html(name);
	}
		$(document).ready(function(){
		// Filtering starts
		var statusFilter = @json($status ?? null);

		// Extra parameters the server needs: the status this listing is scoped
		// to, plus the three dropdowns. DataTables sends its own draw/start/
		// length/search/order alongside these.
		function tableFilters(d) {
			d.status   = statusFilter;
			d.unit     = $('#filter-unit').val();
			d.area     = $('#filter-area').val();
			d.district = $('#filter-district').val();
		}

		var table = $("#result-table").DataTable({
			processing: true,
			serverSide: true,
			deferRender: true,
			pageLength: 10,
			lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
			ajax: {
				url: "{{ route('applications-data') }}",
				data: tableFilters
			},
			dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" + "t" +
			     "<<'col-md-5'i><'col-md-6 pull-right'p>>",
			buttons: [
				{
					// The browser only holds the current page, so printing the
					// whole list has to be done by the server, using the same
					// search and filters that are on screen.
					text: 'Print all',
					className: 'btn-default',
					action: function () { window.open(exportUrl('print'), '_blank'); }
				},
				{
					extend: 'print',
					text: 'Print selected',
					customize: function (win) {
						$(win.document.body).find('table')
							.find('tr').not(':first').find('td:last-child')
							.html('<span></span>');
					},
					exportOptions: { columns: ':visible', modifier: { selected: true } }
				},
				{
					text: 'Excel',
					className: 'btn-default',
					action: function () { window.location = exportUrl('csv'); }
				},
				'colvis'
			],
			order: [[0, "desc"]],
			columnDefs: [{ targets: [6], visible: false }, { targets: [8], orderable: false, searchable: false }],
			select: true
		});

		// Mirror the table's state onto the export links.
		function exportUrl(format) {
			var order = table.order()[0] || [0, 'desc'];
			return "{{ route('applications-export') }}?" + $.param({
				format: format,
				status: statusFilter || '',
				unit: $('#filter-unit').val() || '',
				area: $('#filter-area').val() || '',
				district: $('#filter-district').val() || '',
				search: table.search() || '',
				order: [{ column: order[0], dir: order[1] }]
			});
		}

		$('#filter-unit, #filter-area, #filter-district').on('change', function () {
			table.ajax.reload();
		});

		  $('.table').on('click','.get-reason',function(){
					var content = $(this).data('content');
					$(".reason").html(content);
			});

			$(".data").on("click",function(){
				var id = $(this).attr('id');
				var name = $(this).html();
				$("#modal-id").html(id);
				$("#modal-name").html(name);
			});


			$('#edit').click(function(){
				var data = "edit/" + $("#modal-id").html();
				location.assign(data);
			});

			$('#view').click(function(){
				var data = "view/" + $("#modal-id").html();
				location.assign(data);
			});

			$(".table").on('click','.grant-modal',function(){
				var appli_id = $(this).data('appli-id');
				var ref_no = $(this).data('ref-no');
				var person_name = $(this).data('person-name')

				$("#grant-modal-person").html(person_name);
				$("#grant-modal-refno").html(ref_no);
				$("#app_appli_id").val(appli_id);
			});

			$(".table").on('click','.reject-modal',function(){
				var appli_id = $(this).data('appli-id');
				var ref_no = $(this).data('ref-no');
				var person_name = $(this).data('person-name')

				$("#reject-modal-person").html(person_name);
				$("#reject-modal-refno").html(ref_no);
				$("#r_app_appli_id").val(appli_id);
			});

	        $(document).on('click', '.status-buttons li a.change-status-button', function(e){
	            e.preventDefault();
	            url = $(this).attr('href');
	            swal({
	            title: "Are you sure?",
	            text: "Please make sure your actions!",
	            type: "warning",
	            showCancelButton: true,
	            confirmButtonClass: "btn btn-flat bg-green",
	            confirmButtonText: "Yes, apply!",
	            cancelButtonText: "No, cancel please!",
	            cancelButtonClass: "btn btn-flat bg-red",
	            closeOnConfirm: false,
	            closeOnCancel: false
	          },
	          function(isConfirm)
	          {
	              if (isConfirm)
	              {
	                window.location.href = url;
	              }
	              else
	              {
	                swal("Cancelled", "Ok got it :)", "error");
	              }
	          });

	        });



	         $('#grant-amount').click(function(e){
	            e.preventDefault();
	            url = $(this).attr('href');
	            swal({
	            title: "Are you sure?",
	            text: "Please make sure your actions! You're granting the Application",
	            type: "warning",
	            showCancelButton: true,
	            confirmButtonClass: "btn btn-flat bg-green",
	            confirmButtonText: "Yes, Proceed!",
	            cancelButtonText: "No, cancel please!",
	            cancelButtonClass: "btn btn-flat bg-red",
	            closeOnConfirm: false,
	            closeOnCancel: false
	          },
	          function(isConfirm)
	          {
	              if (isConfirm)
	              {
	                $("#form-grant").submit();
	              }
	              else
	              {
	                swal("Cancelled", "Close the window", "error");
	              }
	          });

	        });

	         $('#reject-amount').click(function(e){
	            e.preventDefault();

	            swal({
	            title: "Are you sure?",
	            text: "Please make sure your actions! You're rejecting the Application",
	            type: "warning",
	            showCancelButton: true,
	            confirmButtonClass: "btn btn-flat bg-green",
	            confirmButtonText: "Yes, Proceed!",
	            cancelButtonText: "No, cancel please!",
	            cancelButtonClass: "btn btn-flat bg-red",
	            closeOnConfirm: false,
	            closeOnCancel: false
	          },
	          function(isConfirm)
	          {
	              if (isConfirm)
	              {
	                $("#form-reject").submit();
	              }
	              else
	              {
	                swal("Cancelled", "Close the window", "error");
	              }
	          });

	        });
		});
	</script>
@stop
