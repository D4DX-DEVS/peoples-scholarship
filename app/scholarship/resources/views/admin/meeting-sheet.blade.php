<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>.</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="{{ asset('/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  	<style type="text/css">
  	@import url(http://fonts.googleapis.com/earlyaccess/notosansmalayalam.css);
  	@import url(https://fonts.googleapis.com/css?family=Noto+Sans);
  	td,th{
  		font-family: 'Noto Sans Malayalam', 'Noto Sans';
  		padding: 2px 2px 2px 5px;
  	}
   	.wraper {
  		width:210mm; 
  		margin:0 auto;
  		/*height: 297mm;
  		background-color: lavender;*/
  	}
  	.container {
  		width: 210mm!important;
  	}
  	.blank {
        border-style: dashed;
        border-color: #ccc;
        width: 100%;
        padding-top: 10px;
    }
    .office td{
        padding-bottom: 20px;
    }
 table , table td, table th, table tr, table tbody, table thead{
	text-align: left;
}
  	</style>


<style type="text/css">
 @media print {
  .print-btn{
    display: none;
  }
 
 }
 
.tg  {border-collapse:collapse;border-spacing:0;margin:0px auto; margin-top: 20px;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:11px 14px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
td.noborder, th.noborder {font-family:Arial, sans-serif;font-size:14px;padding:11px 14px;border-color:#ffffff;border-width:1px 1px 0 1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:11px 14px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-baqh{text-align:center;vertical-align:top}
.tg .tg-ygzf{font-weight:bold;font-size:15px;text-align:center;vertical-align:top}
.tg .tg-yw4l{vertical-align:top}
.th { text-transform: uppercase;font-weight: bold;}
</style>
</head>
<body>
<table class="tg">
  <tr>
    <th class="noborder tg-ygzf" style="font-size: 20px; text-transform: uppercase;" colspan="6"> Meeting Sheet <br></th>
  </tr>
  <tr style="border-top:1px;">
    <td class="noborder tg-yw4l th" colspan="2"> Department :</td>
    <td class="noborder tg-yw4l"> </td>
    <td class="noborder tg-yw4l" ></td>
    <td class="noborder tg-yw4l th">Scheme :</td>
    <td class="noborder tg-yw4l"> Scholarship </td>
  </tr>
    <tr>
      <td class="noborder tg-yw4l th" colspan="2">Meeting No.& Date :</td>
      <td class="noborder tg-yw4l" colspan="2">{{$current_statistic->meeting->serial_no}},   {{date('d-M-Y',strtotime($current_statistic->meeting->date))}}</td>
      <td class="noborder tg-yw4l th">App. Number :</td>
      <td class="noborder tg-yw4l">{{$application->refno}}</td>
    </tr>
  <tr style="height:0px;" >
    <td class="noborder" colspan="6" style="height:0px;"></td>
  </tr>
  <tr>
    <td class="tg-yw4l th" colspan="2">Applicant Name</td>
    <td class="tg-yw4l" colspan="3">{{$application->applicant_name}}</td>
    <td class="tg-yw4l" ></td>
  </tr>
  <tr>
    <td class="tg-yw4l th" colspan="2" rowspan="2">Address</td>
    <td class="tg-yw4l"   rowspan="2" colspan="2" >{!!nl2br($application->person->address)!!}</td>
    <td class="tg-yw4l"> </td>
    <td class="tg-yw4l th" >Mobile no</td>

  </tr>
  <tr>     

      <td class="tg-yw4l"> </td>      
      <td class="tg-yw4l">{{$application->person->mobile}}</td>
  </tr>
 @if ($application->grant_status > '0')
  <tr><th class="tg-ygzf th" colspan="2">Granted Amount </th>
  <td class="tg-yw4l" colspan="4">₹{{$application->amount_granted}}</td></tr>
  @if ($application->no_of_installments>=1)
  <tr>
    <td colspan="2" class="tg-ygzf">Installment no.</td>
    <td class="tg-ygzf">Amount Allotted</td>
    <td class="tg-ygzf">Sign of sec.</td>
    <td class="tg-ygzf">Sign of accounts<br> Manager</td>
    <td class="tg-ygzf">Payment<br> Details</td>
    @foreach ($application->installments as $installment)
      <tr>
        <th  rowspan="2" class="tg-ygzf">{{$installment->installment_number}}</th>
        <td >Due Date</td>
        <td rowspan="2">₹{{$installment->amount}}</td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
      </tr>
      <tr>     
        <td>{{date('d-m-Y',strtotime($installment->due_date))}}</td>
      </tr>
    @endforeach
    </tr>
    @else
      <tr>
        <td class="tg-yw4l th">Signature of Secretary</td>
        <td class="tg-yw4l" colspan="2" width="200px"></td>
        <td class="tg-yw4l th"> Approval of accounts Manager</td>
        <td class="tg-yw4l" width="200px"></td>
      </tr>
      <tr>
        <td class="tg-yw4l th">Payment Details</td>
        <td class="tg-yw4l" colspan="4"></td>
      </tr>
  @endif
  @elseif($application->status === 15)
    <tr>
  <th class="tg-ygzf" colspan="2">Pending Reason</th>
  <td class="tg-yw4l" colspan="3">{{$applcation->reason_status}}</td>
    </tr>
  @elseif($application->status === 5)
    <tr>
  <th class="tg-ygzf" colspan="2">Rejected Reason</th>
  <td class="tg-yw4l" colspan="3">{{$applcation->reason_status}}</td>
    </tr>
 @endif
</table>
<div class="col-md-12 print"><button class="btn btn-success print-btn btn-lg" style="margin:20px 48% 20px 48%;">Print</button></div>

<script type="text/javascript">
  $(document).ready(function(){
     $('.print-btn').click(function(){
      window.print();
       
        });

  })
</script>

</body>
</html>