@extends('layouts.dashboard')
@section('title')
Application - {{$application->refno}} Details
@endsection

@section('content_header')
    <h1>Application Details</h1>
@stop

@section('content')

  <section class="content-header">
  	<div class='col-md-offset-2'><h4><strong>Application Number :</strong> {{$application->refno}}</h4>
    @if ($application->grant_status!==0)
        <div>
            <h5><strong>Status : </strong><span class='label bg-green'>{{$application->getGrantStatus($application->grant_status)['statusText']}}</span></h5>
        </div>
    @endif
    </div>
  </section>
  <div class="col-md-8 col-md-offset-2">
    @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @elseif(Session::has('fail'))
        <div class="alert alert-danger">{{ Session::get('fail') }}</div>
    @endif
    @if(Session::has('success-delete'))
        <input type="hidden" id="showStatistics" value="1">
    @elseif(Session::has('fail-delete'))
        <input type="hidden" id="showStatistics" value="1">
    @endif
  </div>

<div class="row">
	{{--Display application details --}}
	<div class="col-md-10 col-md-offset-2">
			<div class='row'>
                <div class="col-xs-3"><strong>Applicant Name</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->applicant_name}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Address</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->address}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Phone number</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->person->mobile}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Category </strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{optional($application->category)->catname}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Course</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{isset($application->course) ? $application->course->coursename : $application->course_other}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>District</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->district->district}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Area</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->area->area}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Unit</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->unit->unit}}</div>
            </div>
            @if($application->external_source === 'erp')
            <div class='row'>
                <div class="col-xs-3"><strong>Submitted via</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">
                    <span class="label bg-olive">Beneficiary Portal</span>
                    @if($application->external_number)
                        <span class="text-muted">&nbsp;{{$application->external_number}}</span>
                    @endif
                </div>
            </div>
            @if(!empty($application->external_documents))
            <div class='row'>
                <div class="col-xs-3"><strong>Portal documents</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">
                    @foreach($application->external_documents as $document)
                        <div><a href="{{ $document['url'] ?? '#' }}" target="_blank" rel="noopener">{{ $document['name'] ?? 'Document' }}</a></div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif
            <div class='row'>
                <div class="col-xs-3"><strong>Status</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-3"><span class="label bg-{{$application->getStatus->status_color}}">{{$application->getStatus->status_text}}</span>
				@if($application->reason_status)
					{{$application->reason_status}}
				@endif
                @if($application->further_action)
                    <br/><strong>further action</strong>:{{$application->further_action}}
                @endif
                @if($application->status === '4')
                    <br/><strong>Meeting Serial number</strong>:<a href="{{route('view-meeting',['id'=>$application->getLatestStatistic()->meeting->id])}}" target="_blank">{{$application->getLatestStatistic()->meeting->serial_no}}</a>
                @endif
				</div>
                <div>
                @if($application->grant_status>0 && $application->status !==8 )
                    <a href="{{route('cancel-grant',['id'=>$application->id])}}" class="btn btn-danger cancel btn-xs" id="cancel-grant">Cancel grant</a>
                @endif
                </div>
            </div>

            @if ($application->amount_granted!=='0.00')
                <div class='row'><br/>
                    <div class="col-xs-3"><strong>Amount Granted</strong></div>
                    <div class="col-xs-1"><strong>:</strong></div>
                    <div class="col-xs-6">₹{{$application->amount_granted}}</div>
                </div>
            @endif
            @if ($application->granted_date!==null)
                <div class='row'>
                    <div class="col-xs-3"><strong>Granted Date</strong></div>
                    <div class="col-xs-1"><strong>:</strong></div>
                    <div class="col-xs-6">{{date('d-m-Y',strtotime($application->granted_date))}}</div>
                </div>
            @endif
            @if($application->no_of_installments!==0)
            <div class='row'>
                <div class="col-xs-3"><strong>Number of installments</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->no_of_installments}}
                 @if ($application->no_of_installments>0)
                <a href="{{ route('edit-installments',['id'=>$application->id])}}"><i class="fa fa-fw fa-pencil"></i></a>
                @endif
                </div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Number of installments Pending</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->no_of_installments-($application->getInstallmentsCount(4)+$application->getInstallmentsCount(5))}}
            </div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Amount Pending</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">₹{{$application->amount_granted-$application->getAmountPayed()}}
            </div>
            </div>
            @endif
            @if($application->additional_info!="")
            <div class='row'>
                <div class="col-xs-3"><strong>Additional Information</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->additional_info}}</div>
            </div>
            @endif
            @if($application->app_track)
            <div class='row'>
               <br/><strong>Application Track</strong>:{!!nl2br($application->app_track)!!}
            </div>
            @endif
            <br />
            @if($application->status!==8 && $application->status!==10)
            <a href="{{route('admin-app-edit',['appli_id'=> $application->id,'pers_id'=>$application->persid])}}" class="btn btn-success">Edit Details</a>
            @endif
            @if($application->status >=6 && $application->status <=8)
            <a href="{{route('meeting-sheet',['id'=>$application->id])}}" class="btn btn-primary" target="_blank">Meeting Sheet</a>
            @endif
	</div>
</div>

@stop
@section('js')
@parent
<script type="text/javascript">
	$(document).ready(function(){
        $('.delete').click(function(){
            return confirm("Are you sure you wish to delete this attachment?");
        });
        $('#cancel-grant').click(function(){
            return confirm("Are you sure you wish to cancel amount granted to this file?")
        })
        $('.file-delete').click(function(){
            return confirm('Are you sure you want to delete? All cheques, loan details etc associated with this file will be deleted.');
        });
        $('#date-input').datepicker({
              format:'dd-mm-yyyy'
            });
        $('#date-input1').datepicker({
              format:'dd-mm-yyyy'
            });
        $('input[type=radio][name=frequency]').change(function() {
            if (this.value == 'other') {
                $('#freq-months').removeClass('hidden');
            }
            else{
                $('#freq-months').addClass('hidden');
            }
        });

        //Confirm deletion
        $(".deleteLink").click(function(){
            return confirm('Are you sure you wish to delete?');
        });

	});
</script>
@stop