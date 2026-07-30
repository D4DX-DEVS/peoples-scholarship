{{--
    The Action cell of the applications listing.

    Extracted so the server-side DataTables endpoint can render exactly the
    markup the page used to render inline — there is no second copy of this
    logic in JavaScript to keep in step.
--}}
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
    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
  @elseif($applicant->getStatus->status_text == 'Interview' )
    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
  @elseif($applicant->getStatus->status_text == 'Meeting' )
    <li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#grantModal">Grant</a></li>
    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Pending' ] ) }}">Pending</a></li>
    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}" data-target="#rejectModal">Reject</a></li>
  @elseif($applicant->getStatus->status_text == 'Pending' )
    <li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}"  data-target="#grantModal">Grant</a></li>
    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $applicant->refno }}" data-person-name="{{ optional($applicant->person)->personname }}" data-appli-id="{{ $applicant->id }}"  data-target="#rejectModal">Reject</a></li>
    @elseif($applicant->getStatus->status_text == 'Granded' )
    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $applicant->id, 'status'=> 'Interview' ] ) }}">Completed</a></li>
  @endif
  </ul>
</div>
@endif
