@extends('layouts.dashboard')

@section('title', 'Admin | View Application - {{$application->refno}}')

@section('content')
<div class="content-wrapper">
<section class="content">
  <section class="content-header">
  	<div class='col-md-offset-2'><h3><strong>Application number :</strong> {{$application->refno}}</h3>
    @if ($application->grant_status!==0)
        <div>
            <h4><strong>Status : </strong><span class='label bg-green'>{{ $application->getStatus->status_text }}</span></h4>
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
    
@include("office.status-change-modal")
@include("office.statistics-modal")
<div class="row">
	{{--Display file details --}}
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
                <div class="col-xs-6">{{$application->contact_no}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Scheme </strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->scheme->name}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Department</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->department->name}}</div>
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
            <div class='row'>
                <div class="col-xs-3"><strong>Created by</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->creator->username}}</div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Status</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-3"><span class="label bg-{{$application->getStatus->status_colour }}">{{$application->getStatus->status_text}}</span>
				@if($application->reason_status)
					<br/>{{$application->reason_status}}
				@endif
                @if($application->further_action)
                    <br/><strong>further action</strong>:{{$application->further_action}}
                @endif
                @if($application->status === '4')
                    <br/><strong>Meeting Serial number</strong>:<a href="{{route('view-meeting',['id'=>$application->getLatestStatistic()->meeting->id])}}" target="_blank">{{$application->getLatestStatistic()->meeting->serial_no}}</a>
                @endif
				</div>
                <div>
                @if($application->grant_status>0  )
                    <a href="{{route('cancel-grant',['id'=>$application->id])}}" class="btn btn-success cancel btn-xs" id="cancel-grant">Cancel grant</a>
                @endif
                </div>
            </div>         
            @if ($application->amount_granted!=='0.00')
                <div class=row>
                    <div class="col-xs-3"><strong>Amount Granted</strong></div>
                    <div class="col-xs-1"><strong>:</strong></div>
                    <div class="col-xs-6">₹{{$application->amount_granted}}</div>
                </div>
            @endif
            @if ($application->granted_date!==null)
                <div class=row>
                    <div class="col-xs-3"><strong>Granted Date</strong></div>
                    <div class="col-xs-1"><strong>:</strong></div>
                    <div class="col-xs-6">{{date('d-m-Y',strtotime($application->granted_date))}}</div>
                </div>
            @endif
            @if($application->no_of_installments!==1)
            <div class='row'>
                <div class="col-xs-3"><strong>Number of installments</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->no_of_installments}}
            @if ($application->no_of_installments>1)
                <a href="{{ route('edit-installments',['id'=>$application->id])}}"><i class="fa fa-fw fa-pencil"></i></a>
            @endif
                </div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Number of installments Pending</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">{{$application->no_of_installments-($application->getInstallmentsCount(5)+$application->getInstallmentsCount(6))}}
            </div>
            </div>
            <div class='row'>
                <div class="col-xs-3"><strong>Amount Pending</strong></div>
                <div class="col-xs-1"><strong>:</strong></div>
                <div class="col-xs-6">₹{{$application->getAmountPending()}}
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

	</div>
</div>
</section>
</div>
@stop
@section('footer')
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
        //amount- number of installment calculation for loan setup modal.
        $('#totalAmount').change(function(){
            $('#amount_per_installment').val("");
            $('#no_of_installments').val("");
        });
        $('#amount_per_installment').change(function(){
            var amount = $('#amount_per_installment').val();
            var lastInstallment = $('#last_installment_amount').val();
            if(lastInstallment){
                var noOfInstallments = ($('#totalAmount').val() - $('#last_installment_amount').val())/amount;
                noOfInstallments++;
            }
            else{
                var noOfInstallments = $('#totalAmount').val()/amount;
            }    
            $('#no_of_installments').val(noOfInstallments);
        });
        $('#no_of_installments').change(function(){
            var noOfInstallments = $('#no_of_installments').val();
            var lastInstallment = $('#last_installment_amount').val();
            if(lastInstallment){
                var amount = ($('#totalAmount').val()-lastInstallment)/(noOfInstallments-1);
            }
            else{
                var amount = $('#totalAmount').val()/noOfInstallments;
            }                
            $('#amount_per_installment').val(amount);
        });
        $('#last_installment_amount').change(function(){
            var noOfInstallments = $('#no_of_installments').val();
            var amount = $('#amount_per_installment').val();
            var lastInstallment = $('#last_installment_amount').val();
            if(lastInstallment){
                if(noOfInstallments){
                noOfInstallments = ($('#totalAmount').val()-lastInstallment)/amount;
                noOfInstallments++;
                $('#no_of_installments').val(noOfInstallments);
                }
            }
            else{
                if(noOfInstallments){
                noOfInstallments = $('#totalAmount').val()/amount;
                $('#no_of_installments').val(noOfInstallments);
                }
            }
        })
        //end of amount- number of installment calculation for loan setup modal.

		//display modal if there are errors
		if($("#hasError").html()==1){
			$("#statusChangeModal").modal("show");
		}

        if($("#hasErrorloan").html()==1){
            $('#loan-setup-modal').modal("show");
        }

        //display statistics modal after deletion
        if($("#showStatistics").val()==1){
            $('#statisticsModal').modal("show");
        }
        //Confirm deletion
        $(".deleteLink").click(function(){
            return confirm('Are you sure you wish to delete?');
        });
		//change form inputs according to status selected
		$("#new_status").change(function(){
            $(".error-msg").empty();
			$("#reasonLabel").empty();
            $("#amountLabel").empty();
            $("#noOfInstallmentsLabel").empty();
            $("#amountPerInstallmentLabel").empty();
			$("#meeting_idLabel").empty();
			$('input[name=reason], textarea').addClass("hidden");
            $('input[name=reason], textarea').val("");
            $("#statusChangeAmount").addClass("hidden");
            $("#statusChangeAmount").val("");
            $("#noOfInstallments").addClass('hidden');
            $("#noOfInstallments").val("");
            $("#amountPerInstallment").addClass('hidden');
            $("#amountPerInstallment").val("");
            $("#meeting_id").addClass('hidden');
            $("#meeting_id").val("");
			var status = $("#new_status").val();
			switch(status)
            {
                case '2'://verified
                case '3'://interview
                case '8'://condition fulfilled
                case '10'://waiting for documents
                case '11'://Documents Received
                case '13'://Completed
                case '15'://Pending
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Additonal information :</label>");
                    break;
                case '4'://meeting
                    $("#meeting_id").removeClass("hidden");
                    $("#meeting_idLabel").append("<label>Select Meeting:</label>");
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Additonal information :</label>");
                    break;
                case '5'://rejected
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Reason for rejection :</label>");
                    break;
                case '6'://granted
                case '9'://granted as loan
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Additonal information :</label>");
                    $("#noOfInstallments").removeClass("hidden");
                    $("#noOfInstallmentsLabel").append("<label>Number of Installments :</label>");
                    $("#amountPerInstallment").removeClass("hidden");
                    $("#amountPerInstallmentLabel").append("<label>Amount per Installment :</label>");
                    break;
                case '7'://granted with condition
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Specify Condition :</label>");
                    $("#noOfInstallments").removeClass("hidden");
                    $("#noOfInstallmentsLabel").append("<label>No of Installments :</label>");
                    $("#amountPerInstallment").removeClass("hidden");
                    $("#amountPerInstallmentLabel").append("<label>Amount per Installment :</label>");
                    break;
                case '14'://custom status
                    $('input[name=reason], textarea').removeClass("hidden");
                    $("#reasonLabel").append("<label>Write Status :</label>");
            }
		});//--end of new_status change
        $('#amountPerInstallment').change(function(){
            $('#amountLabel').empty();
            var perInstallment = 0;
            if($('#amountPerInstallment').val())
                perInstallment = $('#amountPerInstallment').val();
            var noOfInstallments = 1;
            if($('#noOfInstallments').val())
                noOfInstallments = $('#noOfInstallments').val();
            var total = noOfInstallments*perInstallment;
            $('#amountLabel').append("<label>Total Amount:</label>"+total);
        });
        $('#noOfInstallments').change(function(){
            $('#amountLabel').empty();
            var noOfInstallments = 1;
            if($('#noOfInstallments').val())
                noOfInstallments = $('#noOfInstallments').val();
            var perInstallment = 0;
            if($('#amountPerInstallment').val())
                perInstallment = $('#amountPerInstallment').val();
            var total = noOfInstallments*perInstallment;
            $('#amountLabel').append("<label>Total Amount:</label>"+total);
        });
	});
</script>
@stop