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
        <div id="tfilter"></div>

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
               @foreach( $applications as $applicant )
				            <tr>
										<td><div><a href="{{route('view-application',['id'=>$applicant->id])}}" title="View Application">{{ $applicant->refno }}</a></div></td>
										<td>{{ isset($applicant->person) ? $applicant->person->personname : '' }}</td>
										<td>{{ $applicant->created_at }}</td>
				            <td>{{ isset($applicant->person) ? $applicant->person->mobile : '' }}</td>
				            <td>{{ isset($applicant->unit) ? $applicant->unit->unit : '' }}</td>
										<td>{{ isset($applicant->area) ? $applicant->area->area : '' }}</td>
				            <td>{{ isset($applicant->district) ? $applicant->district->district : '' }}</td>
										<td>{{ isset($applicant->course) ? $applicant->course->coursename : $applicant->course_other  }}</td>
				            <td>
				              @if($applicant->getStatus->status_text == 'Granted')
		            			<span class="label bg-green">Granted : Rs.{{ $applicant->amount_granted }} </span>
		            		  @elseif($applicant->getStatus->status_text == 'Rejected')
		            			<a href="#" class="get-reason label bg-red"  data-toggle="modal"  data-target="#RejectionModel"  data-content="{!! nl2br($applicant->reason_status) !!}" data-whatever="@mdo">Rejected: View</a>
		            		  @elseif($applicant->getStatus->status_text == 'Completed')
		            			<span class="label bg-black">Completed : Rs.{{ $applicant->amount_granted }} </span>
		            		  @else
								<div data-toggle="modal" class="btn-group">
				                  <button type="button" class="btn btn-info ">
					                  {{ $applicant->getStatus->status_text }}
				                  </button>
				                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
				                    <span class="caret"></span>
				                    <span class="sr-only">Actions</span>
				                  </button>
													<ul class="dropdown-menu status-buttons" role="menu">
													<li><a class="change-status-button" href="{{ route('admin-app-edit', ['appli_id'=> $applicant->id, 'pers_id'=> $applicant->persid ] ) }}">Verify / Edit</a></li>

				                  @if($applicant->getStatus->status_text == 'Registered' || $applicant->getStatus->status_text == 'Incomplete')
				                	<li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Incomplete' ] ) }}">Incomplete</a></li>
													<li><a class="change-status-button" href="{{ route('admin-app-delete', ['appli_id'=> $applicant->id, 'pers_id'=> $applicant->persid ] ) }}">Delete</a></li>
													@elseif($applicant->getStatus->status_text == 'Verified' )
				                	<li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Interview' ] ) }}">Interview</a></li>
													<li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
				                  @elseif($applicant->getStatus->status_text == 'Interview' )
				                	<li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
				                  @elseif($applicant->getStatus->status_text == 'Meeting' )
				                	<li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#grantModal">Grant</a></li>
				                	<li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Pending' ] ) }}">Pending</a></li>
				                	<li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
				                  @elseif($applicant->getStatus->status_text == 'Pending' )
				                	<li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}"  data-target="#grantModal">Grant</a></li>
				                	<li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ $applicant->person->personname }}" data-appli-id="{{ $applicant->id }}"  data-target="#rejectModal">Reject</a></li>
													@elseif($applicant->getStatus->status_text == 'Granded' )
				                	<li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Interview' ] ) }}">Completed</a></li>
				                  @endif
				                  </ul>
				                </div>
				                @endif
                			</td>
				            </tr>
            @endforeach
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
		$("#result-table").DataTable(
			{
        dom: "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
		 			"<<'col-md-5'i><'col-md-6 pull-right'p>>",
        buttons: [
            {
                extend: 'print',
                customize: function ( win ) {
                    $(win.document.body).find( 'table' )
                        .find('tr').not(':first').find('td:last-child')
                        .html( '<span></span>' );
                },
                text: 'Print all',
                exportOptions: {
									  columns: ':visible',
                    modifier: {
                        selected: null
                    }
                }
            },
            {
                extend: 'print',
                customize: function ( win ) {
                    $(win.document.body).find( 'table' )
                        .find('tr').not(':first').find('td:last-child')
                        .html( '<span></span>' );
                },
								text: 'Print selected',
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
				"columnDefs": [
            {
            	"targets":[6],
            	"visible":false
						}],
						initComplete: function () {
            this.api().columns([4,5,6]).every( function () {
                var column = this;
                var select_name;

                if (column[0][0] == 4) {
                	select_name = 'unit';
                }

                if (column[0][0] == 5) {
                	select_name = 'area';
                }

                if (column[0][0] == 6) {
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
						,
        select: true
    } );

		// 	{
  	// 	"dom" : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
		// 			"<<'col-md-5'i><'col-md-6 pull-right'p>>",
    //  	"buttons": [{
    //     "extend":'print',
    //     "exportOptions":{
    //     	"columns":':visible',
    //     }    },
    //     {
    //     "extend":'excel',
    //     "exportOptions":{
    //     	"columns":':visible',
    //     }    },
    //     "colvis"],
    //      "order": [[ 0, "desc" ]],
    //      "columnDefs": [
    //         {
    //         	"targets":[6],
    //         	"visible":false
    //         }
    //     ],
    //         initComplete: function () {
    //         this.api().columns([4,5,6]).every( function () {
    //             var column = this;
    //             var select_name;

    //             if (column[0][0] == 4) {
    //             	select_name = 'unit';
    //             }

    //             if (column[0][0] == 5) {
    //             	select_name = 'area';
    //             }

    //             if (column[0][0] == 6) {
    //             	select_name = 'district';
    //             }

    //             var select = $('<select id="'+select_name+'" class="form-c"><option value="">Filter by '+select_name+'</option></select>')
    //                 .appendTo( $("#tfilter") )
    //                 .on( 'change', function () {
    //                     var val = $.fn.dataTable.util.escapeRegex(
    //                         $(this).val()
    //                     );

    //                     column
    //                         .search( val ? '^'+val+'$' : '', true, false )
    //                         .draw();
    //                 } );

    //             column.data().unique().sort().each( function ( d, j ) {
    //                 select.append( '<option value="'+d+'">'+d+'</option>' )
    //             } );
    //         } );
    //     }
  	// });
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

	        $('.status-buttons li a.change-status-button').click(function(e){
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
