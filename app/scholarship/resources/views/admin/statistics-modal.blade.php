<div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Current Status : <span class="label bg-{{$application->getStatus($application->status)['bgColour']}}">{{$application->getStatus($application->status)['statusText']}}</span></h4>
        @if(Session::has('success-delete'))
          <div class="alert alert-success">{{ Session::get('success-delete') }}</div>
        @elseif(Session::has('fail-delete'))
          <div class="alert alert-danger">{{ Session::get('fail-delete') }}</div>
        @endif
      </div>
      <div class="modal-body" id="statisticsBody">
      <div class="row">
      <div class="col-md-12">
        <ul class="timeline">
          @foreach ($statistics as $statistic)
          <!-- timeline time label -->
          <li class="time-label">
            <span class="bg-{{$application->getStatus($statistic->status)['bgColour']}}">
                {{ $application->getStatus->status_text }}
            </span>
          </li>
          <!-- /.timeline-label -->
          <!-- timeline item -->
    <li>
        <!-- timeline icon -->
        <div class="timeline-item">
            <span class="time"><i class="fa fa-calendar"></i> {{date('d-m-Y',strtotime($statistic->date))}}</span>

            <div class="timeline-body">
                @if ($statistic->application->status===4)
                <strong>Meeting Serial no:</strong>
                @if($statistic->meeting_id!==0)
                @if ($statistic->meeting)
                  <a href="{{route('view-meeting',['id'=>$statistic->meeting->id])}}" target="_blank">{{$statistic->meeting->serial_no}}</a>
                  @else
                  <i>Meeting Deleted</i>
                @endif
                @else
                  <i>Removed from meeting</i>
                @endif
                @endif
                @if ($statistic->application->reason_status)
                <p><strong>Remarks:</strong>{!!$statistic->application->reason_status!!}</p>
                @endif
                 @if ($statistic->application->further_action)
                <p><strong>further action:</strong>{!!$statistic->->application->further_action!!}</p>
                @endif
                @if(($statistic->application->status===6) || ($statistic->application->status===7) ||($statistic->application->status === 8) ||($statistic->application->status===9))
                 <p><b>Amount granted:</b><i class="fa fa-fw fa-inr"></i>{{$application->amount_granted}}</p>
                @endif
            </div>

        </div>
    </li>
    <!-- END timeline item -->

          @endforeach
          <li><a href="{{route('print-statistics',['id'=>$application->id])}}" class="btn btn-success" target="_blank">Print</a></li>
        </ul>
      </div>
      </div>
      </div>
      <div class="modal-footer">

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->