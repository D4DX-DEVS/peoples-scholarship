@extends('layouts.dashboard')

@section('title', 'Admin | Installmets')

@section('content_header')
    <h1>Installments | {{$application->refno}} | {{$application->applicant_name}}</h1>
@stop

@section('content')
@include("admin.add-installment-modal")
  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Installments for {{$application->refno}}</h3>
              <h4>Current Application Status <span class="label bg-blue">{{$application->getStatus->status_text}}</span></h4>
              <h4>Current Grant Status <span class="label bg-aqua">{{$application->getGrantStatus($application->grant_status)['statusText']}}</span></h4>
              </div>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table class="table table-hover">
              <thead>
                <th>Installment #</th>
                <th>
                <div class="form-group has-error">
                @if($errors->has('due_date')) <label class="error-msg" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('due_date') }}</label> @endif
                </div>
                Due date
                </th>
                <th>
                <div class="form-group has-error">
                  @if($errors->has('amount')) <label class="error-msg" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('amount') }}</label> @endif
              </div>
                Amount
                </th>
                <th>Reason/Other Information</th>
                <th>Status</th>
                <th>Actions</th>
              </thead>
              <tbody>
              <div class="container">
                @foreach($installments as $installment)
                    <form action="{{ route('save-installments',['id'=>$installment->id])}}" method="post">
                    {!! csrf_field() !!}
                    <tr>
                      <td>
                      <input type="hidden" name="installment_number" value="{{$installment->installment_number}}">
                      {{$installment->installment_number}}
                      </td>
                      <td>
                      <input type="text" name="due_date" value="{{($installment->due_date!==null)?date('d-m-Y',strtotime($installment->due_date)):''}}" class="form-control date-input" @if($installment->status>3) disabled @endif>
                      </td>
                      <td>
                      <input type="number" step=".01" name="amount" value="{{$installment->amount}}" class="form-control" @if($installment->status>3) disabled @endif>
                      </td>
                      <td>
                        <input type="text" name="reason" value="{{$installment->reason}}" class="form-control" placeholder="Reason/ other information" @if($installment->status>3) disabled @endif>
                      </td>
                      <td>
                      <select name="status" class="form-control">
                         <option value="1" {{ ($installment->status===1)? 'selected="selected"' : '' }} > Pending </option> 
                         <option value="2" {{ ($installment->status===2)? 'selected="selected"' : '' }} > Current </option> 
                         <option value="3" {{ ($installment->status===3)? 'selected="selected"' : '' }} > To Accounts </option> 
                         <option value="4" {{ ($installment->status===4)? 'selected="selected"' : '' }} > Delivered </option> 
                      </select>
                     </td>
                      <td>
                      @if ($installment->status==1 || $installment->status==2 || $installment->status==3)
                        <input type="submit" name="submit" class="btn btn-success btn-sm" value="Save">
                      @endif
                      @if (sizeOf($installments)>1 && ($installment->status<4 || $installment->status==5))
                      <a href="{{route('delete-installment',['id'=>$installment->id])}}" class="btn btn-warning btn-sm delete-inst" title="Delete"> <i class="fa fa-trash"></i></a>
                      @endif
                      </td>
                    </tr>
                    </form>
                @endforeach
                </div>
              </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          <div class="box-footer">
          <span class="label status bg-aqua"><strong>Total Amount: ₹{{$application->amount_granted}}</strong></span>
          <div class="pull-right">
          <a href="#" data-toggle="modal" data-target="#addInstallmentModal" class="btn btn-sm btn-success">Add Installment</a> 
          <a href="{{route('view-application',['id'=> $application->id])}}" class="btn btn-sm btn-success"> View Application Details</a>
            @if ($application->status!==8 )
            <a href="{{route('admin-app-edit',['appli_id'=> $application->id,'pers_id'=>$application->persid])}}" class="btn btn-sm btn-success">Edit Application</a>
            @endif
          </div>
          </div>
          <!-- /.box -->
        </div>
</section>
</div>
@stop
@section('js')
@parent
<script src="{{ asset ('pluggins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.date-input').datepicker({
      format:'dd-mm-yyyy'
      });
    if($('#hasError').html()==1){
      $('#addInstallmentModal').modal("show");
    }
    $('.delete-inst').click(function(){
      return confirm("Are you sure you want to delete?");
    });
    $('.cancel-btn').click(function(e){
      e.preventDefault();
              url = $(this).attr('href');
              swal({
              title: "Are you sure you want to cancel this installment?",
              text: "Any corresponding cheques will be deleted",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn btn-flat bg-green",
              confirmButtonText: "Yes, I'm sure!",
              cancelButtonText: "No, cancel please!",
              cancelButtonClass: "btn btn-flat bg-red",
              closeOnConfirm: false,
              closeOnCancel: true
            },
            function(isConfirm) 
            {
                if (isConfirm) 
                {
                  window.location.href = url;
                } 
            });
    });
    $('.revive-btn').click(function(){
      return confirm("Revive this installment?");
    });
  });
</script>
@stop