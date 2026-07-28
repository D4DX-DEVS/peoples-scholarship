<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>.</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
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
</head>
<body style="margin: 0; padding: 0; line-height: 22px; color: #666" onload="{{ (Route::currentRouteName() == 'user-application-print') ? 'window.print()' : '' }}">
	<div class="wraper">
		<div class="container ">
			<div class="row" style="border-bottom: 1px solid #ccc;height:120px;">
				<div class="col-md-6" style="float:left;">
					<img src="{{ asset('images/logo-print.png') }}" style="margin: 5px auto; display: block;">
				</div>
				<div class="col-md-6" style="width:450px;float:right;">
					<div style="font-size:19px; font-weight:bold; display: block;">HIGHER EDUCATION SCHOLARSHIP  {{ $yearsetting->yearperiod ?? null }}</div>
					<br><span style="font-size: 18px; display: block; padding-bottom: 5px;">Calicut Charitable Trust Building, Mavoor Road, <br>
																							Kozhikkode-673004, Kerala, India <br>
																							Tel: 0495-2729500 | www.peoplesfoundation.org</span>
				</div>
			</div>
			<div class="row ">
			<br>
			  	<table class="col-md-12" style="width:100%; ">
				    <tr>
						<td><b>Reg. No: {{ $refno }}</b></td>
						<td>&nbsp;</td>
						<td valign="" rowspan="4" >
							<div class="pull-right" style="text-align:right; height:130px;margin-right:20px;">
								<img src="{{ ($person->photourl != '') ? ('/storage/uploads/'.$person->photourl) : asset('images/uploads/nopic.jpg')  }}" height="130px">		
							</div>
						</td>
					</tr>
					<tr>
						<td style="padding: 5px 0" ><b><u>Candidate Details</u></b></th>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Name of the Candidate: </td>
						<td><b>{{ $person->personname }}<b></td>
					</tr>
					<tr>
						<td>Gender: </td>
						<td>{{ $person->gender }}</td>
					</tr>
					<tr>
						<td>Age, Date of birth:</td>
						 <td colspan="2"> {{ $person->age }}, {{ $person->dob }}</td>
					</tr>
					<tr>
						<td valign="" style="width: 50%; ">
							<div style="width: 50%; display:block;float:left;">Address: </div>		
						</td>
						<td colspan="2">
							<div style="width: 50%; display:inline-block;float:left;">
							{!! nl2br($person->address) !!},<br>
							{{ $district }} (D) <br>
							PIN:{{ $person->pin }}		
							</div>
						</td>
					</tr>
					<tr>
						<td>Contact Number:</td>
						<td colspan="2">{{ $person->mobile }}</td>
					</tr>
					<tr>
						<td>E-mail ID:</td>
						<td colspan="2">{{ $person->email }}</td>
					</tr>
					<tr>
						<td>Signature of Candidate: </td>
						<td colspan="2">&nbsp;<br></td>
					</tr>
					<tr>
						<td><u><b>Scholarship application category: </b></u></td>
						<td colspan="2">{{ $category }}</td>
					</tr>
					<tr>
						<td>Course:</td>
						<td colspan="2">{{ $course }}</td>
					</tr>
					<tr>
						<td>Institution:</td>
						<td colspan="2">{{ $application->institution }}</td>
					</tr>
					</table>
					<table class="col-md-12" style="width:100%; ">
					<tr>
						<td>University: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Admission mode: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Year/semester of study: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Duration of the course: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" ><b><u>Fee Structure (Tuition fee, Examination fee, University fee etc)</u></b> </td>
					</tr>
					<tr>
						<td>Course (per year/semester): </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Donation /Establishment : </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Study materials: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Hostel (Food and Accommodation) Monthly: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Whether day scholar, travelling & <br>food expenses(monthly): </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Project/Field work: </td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><b>Total:</b></td>
						<td>&nbsp;<br></td>
					</tr>
				<!-- awd -->
			 	</table><div><i>for further procedures contact :-</i>
				 </div>
				 <table class="col-md-12" style="width:100%;border:solid 1px ">
			  		<tr>
						<th style="padding: 5px 3px; width:33%;" >Halqa :{{ $unit->unit }} </th>
						<th style="padding: 5px 3px; width:33%; border-left:solid 1px;border-right:solid 1px;" ><span>Area: {{ $area->area }} </b></span></th>
						<th style="padding: 5px 3px; width:34%" ><u>Area Coodinator</u></th>
					</tr>
					<tr>
						<td style="padding: 5px 3px;">Nazim: {{ $unit->presiname }}</td>
						<td style="padding: 5px 3px; border-left:solid 1px;border-right:solid 1px;">President: {{ $arealeaders->area_president }}</td>
						<td style="padding: 5px 3px;">Name: {{ $arealeaders->area_cordinator }}</td>
					</tr>
					<tr>
						<td style="padding: 5px 3px;">Contact Number: {{ $unit->presinumber }} </td>
						<td style="padding: 5px 3px; width:33%; border-left:solid 1px;border-right:solid 1px;">Contact Number: {{ $arealeaders->president_mobile }}</td>
						<td style="padding: 5px 3px;">Contact Number: {{ $arealeaders->cordinator_mobile }}</td>
					</tr>
				</table>
				<div class="printdiv" style="page-break-before: always;"><img src="{{ asset('images/sk-new-form-2.jpg')  }}" width="100%" height="auto"></div>
				<div class="printdiv" ><img src="{{ asset('images/sk-new-form-3.jpg')  }}" width="98%" height="auto"></div>
				<div class="printdiv" ><img src="{{ asset('images/sk-new-form-4.jpg')  }}" width="95%" height="auto"></div>
			</div>
		</div>		
	</div>

	<div style="text-align:center" class="noprint">
		<div class="container" style="padding-top:0;width:100%">
			<div class="row" style="text-align:center">
				<button id="printApp">Print</button>
			</div>
			<br><hr style="border-color:#ccc">
			<div class="row">
				<div class="col-md-12 text-center">
						Copyright &copy;  www.peoplesfoundation.org<br>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	$(function(){
		$('#printApp').click(function(){
			$('.printdiv').show();
			$('.noprint').hide();
			window.print();
		})
	})
</script>
</body>
</html>