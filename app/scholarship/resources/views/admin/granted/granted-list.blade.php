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
		<div id="tfilter">
			<select id="filter-grant-status" class="form-c">
				<option value="">Filter by grant type</option>
				@foreach($grantTypes as $code => $label)<option value="{{ $code }}">{{ $label }}</option>@endforeach
			</select>
			<select id="filter-category" class="form-c">
				<option value="">Filter by category</option>
				@foreach($filterCategories as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach
			</select>
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
				{{-- Rows are loaded ten at a time from granted-data. --}}
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
	var grantedFilterIds = ['#filter-grant-status', '#filter-category', '#filter-unit', '#filter-area', '#filter-district'];

	function grantedFilters(d) {
		d.grant_status = $('#filter-grant-status').val();
		d.category     = $('#filter-category').val();
		d.unit         = $('#filter-unit').val();
		d.area         = $('#filter-area').val();
		d.district     = $('#filter-district').val();
	}

	var table = $("#result-table").DataTable({
		processing: true,
		serverSide: true,
		deferRender: true,
		pageLength: 10,
		lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
		ajax: { url: "{{ route('granted-data') }}", data: grantedFilters },
		dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" + "t" +
		     "<<'col-md-5'i><'col-md-6 pull-right'p>>",
		buttons: [
			{
				// Rebuilt server-side: the page only holds the current ten rows.
				text: 'Print all',
				action: function () { window.open(grantedExportUrl('print'), '_blank'); }
			},
			{
				text: 'Excel',
				action: function () { window.location = grantedExportUrl('csv'); }
			},
			'colvis'
		],
		order: [[1, "desc"]],
		columnDefs: [
			{ targets: [0], visible: false, searchable: false },
			{ targets: [4], visible: false },
			{ targets: [8], visible: false },
			{ targets: [10], orderable: false, searchable: false }
		]
	});

	function grantedExportUrl(format) {
		var order = table.order()[0] || [1, 'desc'];
		return "{{ route('granted-export') }}?" + $.param({
			format: format,
			grant_status: $('#filter-grant-status').val() || '',
			category: $('#filter-category').val() || '',
			unit: $('#filter-unit').val() || '',
			area: $('#filter-area').val() || '',
			district: $('#filter-district').val() || '',
			search: table.search() || '',
			order: [{ column: order[0], dir: order[1] }]
		});
	}

	$(grantedFilterIds.join(',')).on('change', function () { table.ajax.reload(); });

  </script>
 @stop