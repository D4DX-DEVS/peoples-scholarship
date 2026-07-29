@extends('layouts.dashboard')

@section('title', 'Admin | Application')

@section('content_header')
    <h1>Application : Edit</h1>
@stop

@section('adminlte_css')
  @parent
  <link rel="stylesheet" href="{{ URL::asset('pluggins/sweet-alert/lib/sweet-alert.css')}}">
  <style type="text/css">
    #file-uploader{
      margin: 5px auto;
      width: 210px;
      height: 210px;
      position: relative;
      overflow: hidden;
      direction: ltr;
      border: solid 5px #ccc;
    }

       #file_img{
        margin: 0 auto;
        padding: 0;
        width: 200px;
        height: 100%;
        background-size:cover;
        position: absolute;
        background-position: center;
        background-repeat: no-repeat;
        top:0;
        left: 0;
      }

      #file-btn{
        position: absolute;
        top:0px;
        background: red;
        height: 210px;
        width: 210px;
        z-index: 10;
        margin: 0px auto;
        padding: 0px;
        cursor: pointer;
        opacity: 0;
      }
      @font-face {
          font-family: 'Material Icons';
          font-style: normal;
          font-weight: 400;
          src: url(https://fonts.gstatic.com/s/materialicons/v41/flUhRq6tzZclQEJ-Vdg-IuiaDsNcIhQ8tQ.woff2) format('woff2');
        }

        .material-icons {
          font-family: 'Material Icons';
          font-weight: normal;
          font-style: normal;
          font-size: 24px;
          line-height: 1;
          letter-spacing: normal;
          text-transform: none;
          display: inline-block;
          white-space: nowrap;
          word-wrap: normal;
          direction: ltr;
          -webkit-font-feature-settings: 'liga';
          -webkit-font-smoothing: antialiased;
        }
        .info-box-3 {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            height: 100px;
            display: flex;
            cursor: default;
            background-color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }
      .info-box-3 .icon {
          position: absolute;
          left: 3px;
          bottom: 2px;
          text-align: center;
      }
      .info-box-3 .icon i {
          color: #fff;
          font-size: 50px;
          line-height: 100px;
      }
      .info-box-3 .content {
          display: inline-block;
          padding: 7px 16px;
          margin-left:45px;
      }
      .info-box-3 .content .text {
          font-size: 13px;
          font-weight:bold;
          color: #FFF;
          margin-top: 3px;
      }
</style>
@stop

@section('content')
@include("admin.grant-modal")
@include('admin.reject-modal')
@include('admin.rejection-reason-modal')

      @if(Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
      @elseif(Session::has('fail'))
          <div class="alert alert-danger">{{ Session::get('fail') }}</div>
      @endif
         <!-- Default box -->
         <form role="form" method="post" action="{{route('admin-post-edit-app')}}">
         <div class="row">
  <div class="col-md-4 col-md-offset-4 text-center {{ ($errors->has('profile_pic')) ? 'has-error' : '' }}">

      @if($errors->has('profile_pic'))
        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('profile_pic') }}</label>
      @endif
      <input type="hidden" name="persid" value="{{ $person->id }}">
      <input type="hidden" name="applid" value="{{ $application->id }}">
      <div id="file-uploader">
        <div id="file_img" style="background-image: url('{{ $person->photo_cdn_url ?? asset('images/uploads/nopic.jpg') }}')" ></div>
  </div>
</div>


            <div class="row" >
            @if(isset($previousAppDet) && $previousAppDet != null )
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-3 bg-purple">
                        <div class="icon">
                        <i class="material-icons">history</i>
                        </div>
                        <div class="content">
                            <div class="text">Previous Application</div>
                            <div class="text">Appl.No.: {{ $previousAppDet->refno }}</div>
                            <div class="text">Granted Date.: {{ date('d-m-Y', strtotime($previousAppDet->granted_date)) }}</div>
                            <div class="text">Amount : {{ $previousAppDet->amount_granted }} </div>
                         </div>
                    </div>
                </div>
                @endif
                @if($application->getStatus->status_text != 'Registered')
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-3 bg-green">
                        <div class="icon">
                        <i class="material-icons">brightness_low</i>
                        </div>
                        <div class="content">
                            <div class="text">Status: {{ $application->getStatus->status_text }}</div>
                            <div class="text">{{$last_action}}</div>
                            @if($application->getStatus->status_text === 'Granted' && $installments_due>0)
                            <div class="text">Installments Due: {{ $installments_due }}
                              <span><a href="{{ route('edit-installments',['id'=>$application->id])}}"><i class="fa fa-fw fa-pencil" style="color: orange;"></i></a></span>
                              @endif
                            </div>
                         </div>
                    </div>
                </div>
                @endif
            </div>

                <div class="row">
                  <div class="col-md-8 col-md-offset-2">
                  <table class="table table-bordered">
                        <tbody>
                        <tr>
                          <th>Ref. No:</th>
                          <td><input type="text" name="refno" value="{{ $application->refno }}" class="form-control" placeholder="ID" readonly></td>
                        </tr>
                        <tr>
                          <th>Applicant Name:</th>
                          <td class="{{ ($errors->has('name')) ? 'has-error' : '' }}">
                            @if($errors->has('name'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('name') }}</label>
                            @endif
                            <input type="text" name="name" value="{{ $person->personname }}" class="form-control"  readonly></td>
                        </tr>
                        <tr>
                          <th>Application Status:</th>
                          <td>
                          @if($application->getStatus->status_text == 'Granted')
                                <span class="label bg-green">Granted : Rs.{{ $application->amount_granted }} </span>
                                @elseif($application->getStatus->status_text == 'Rejected')
                                <a href="#" class="get-reason label bg-red"  data-toggle="modal"  data-target="#RejectionModel"  data-content="{!! nl2br($application->reason_status) !!}" data-whatever="@mdo">Rejected: View</a>
                                @else
                          <div data-toggle="modal" class="btn-group">
                                    <button type="button" class="btn btn-info ">
                                      {{ $application->getStatus->status_text }}
                                    </button>
                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                      <span class="caret"></span>
                                      <span class="sr-only">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu status-buttons" role="menu">
                                    @if($application->getStatus->status_text == 'Registered' || $application->getStatus->status_text == 'Incomplete')
                                    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $application->id, 'status'=> 'Incomplete' ] ) }}">Incomplete</a></li>
                                    <li><a class="change-status-button" href="{{ route('admin-app-delete', ['appli_id'=> $application->id, 'pers_id'=> $application->persid ] ) }}">Delete</a></li>
                                    @elseif($application->getStatus->status_text == 'Verified' )
                                    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $application->id, 'status'=> 'Interview' ] ) }}">Interview</a></li>
                                    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}" data-target="#rejectModal">Reject</a></li>
                                    @elseif($application->getStatus->status_text == 'Interview' )
                                    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}" data-target="#rejectModal">Reject</a></li>
                                    @elseif($application->getStatus->status_text == 'Meeting' )
                                    <li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}" data-target="#grantModal">Grant</a></li>
                                    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $application->id, 'status'=> 'Pending' ] ) }}">Pending</a></li>
                                    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}" data-target="#rejectModal">Reject</a></li>
                                    @elseif($application->getStatus->status_text == 'Pending' )
                                    <li><a href="#" data-toggle="modal" class="grant-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}"  data-target="#grantModal">Grant</a></li>
                                    <li><a href="#" data-toggle="modal" class="reject-modal" data-ref-no="{{ $application->refno }}" data-person-name="{{ $application->person->personname }}" data-appli-id="{{ $application->id }}"  data-target="#rejectModal">Reject</a></li>
                                    @elseif($application->getStatus->status_text == 'Granded' )
                                    <li><a class="change-status-button" href="{{ route('admin-set-app-status', ['appli_id'=> $application->id, 'status'=> 'Interview' ] ) }}">Completed</a></li>
                                    @endif
                                    </ul>
                                  </div>
                                  @endif
                          </td>
                        </tr>
                        <tr>
                          <th>Mobile Number:</th>
                          <td class="{{ ($errors->has('mobile')) ? 'has-error' : '' }}">
                             @if($errors->has('mobile'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('mobile') }}</label>
                            @endif
                            <input type="text"  name="mobile" id="mobile" value="{{ $person->mobile }}" class="form-control" readonly  ></td>
                        </tr>
                        <tr>
                          <th>Aadhar Card Number:</th>
                          <td class="{{ ($errors->has('aadhar')) ? 'has-error' : '' }}">
                            @if($errors->has('aadhar_card'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('aadhar_card') }}</label>
                            @endif
                            <input type="text" name="aadhar" id="aadhar" value="{{ $person->aadhar }}" class="form-control" readonly ></td>
                        </tr>
                        <tr>
                          <th>Male / Female :</th>
                          <td class="{{ ($errors->has('gender')) ? 'has-error' : '' }}">
                              @if($errors->has('gender'))
                                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('gender') }}</label>
                              @endif
                            <div class="radio">
                            <label>
                              {!! Form::radio('gender', 'Male', isset($person->dob) ? ($person->gender=='Male') : true ) !!} പുരുഷന്‍
                            </label>
                          </div>
                          <div class="radio">
                            <label>
                              {!! Form::radio('gender', 'Female', isset($person->dob) ? ($person->gender=='Female') : false) !!} സ്ത്രീ
                            </label>
                          </div>
                        </td>
                        </tr>
                        <tr>
                          <th>Age:</th>
                          <td class="{{ ($errors->has('age')) ? 'has-error' : '' }}">
                             @if($errors->has('age'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('age') }}</label>
                            @endif
                            <input type="number" name="age" value="{{ isset($person->age) ? $person->age : '' }}" maxlength="3" class="form-control" placeholder="Age"  min="15" max="40" required></td>
                        </tr>
                        <tr>
                          <th>Date of Birth:</th>
                          <td class="{{ ($errors->has('dob')) ? 'has-error' : '' }}">
                             @if($errors->has('dob'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('dob') }}</label>
                            @endif
                            <input type="text" name="dob" id="dob" value="{{ isset($person->dob) ? $person->dob : '' }}" maxlength="10" class="form-control" placeholder="dd/mm/yyyy" required></td>
                        </tr>
                        <tr>
                          <th>Address:</th>
                          <td class="{{ ($errors->has('address')) ? 'has-error' : '' }}">
                             @if($errors->has('address'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('address') }}</label>
                            @endif
                            <textarea name="address" class="form-control" rows="3" placeholder="Address" required >{{ isset($person->address) ? $person->address : 'Address' }}</textarea></td>
                        </tr>
                        <tr>
                          <th>PIN:</th>
                          <td class="{{ ($errors->has('pin')) ? 'has-error' : '' }}">
                             @if($errors->has('pin'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('pin') }}</label>
                            @endif
                            <input type="text" name="pin" id="pin" value="{{ isset($person->pin) ? $person->pin : '' }}" class="form-control" placeholder="Pincode" required></td>
                        </tr>

                        <tr>
                          <th>Email:</th>
                          <td class="{{ ($errors->has('email')) ? 'has-error' : '' }}">
                              @if($errors->has('email'))
                                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('email') }}</label>
                              @endif
                              <input type="email" class="form-control" name="email" placeholder="Email" value="{{ isset($person->email) ? $person->email : '' }}" />
                          </td>
                        </tr>
                        <tr>
                          <th>District:</th>
                          <td class="{{ ($errors->has('district')) ? 'has-error' : '' }}">
                            @if($errors->has('district'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('district') }}</label>
                            @endif
                            {!!  Form::select('district', [null=>'--തിരഞ്ഞെടുക്കു--'] + $districts , $person->district ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'district',
                                  'required',
                                ]);
                            !!}
                          </td>
                        </tr>
                        <tr>
                          <th>Area :</th>
                          <td class="{{ ($errors->has('area')) ? 'has-error' : '' }}">
                            @if($errors->has('area'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('area') }}</label>
                            @endif
                            {!!  Form::select('area',
                                ['Area' => 'Area',] +
                                 [], $person->area ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'area',
                                  'required',
                                ]);
                            !!}
                        </tr>
                        <tr>
                          <th>Unit:</th>
                          <td class="{{ ($errors->has('unit')) ? 'has-error' : '' }}">
                            @if($errors->has('unit'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('unit') }}</label>
                            @endif
                            {!!  Form::select('unit',
                                ['Unit' => 'Unit',] +
                                 [], $person->unit ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'unit',
                                  'required',
                                ]);
                            !!}
                        </tr>
                        <tr>
                          <th>Father/Guardian :</th>
                          <td class="{{ ($errors->has('guardian')) ? 'has-error' : '' }}">
                            @if($errors->has('guardian'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('guardian') }}</label>
                            @endif
                            <input type="text" name="guardian" value="{{ isset($person->guardian) ? $person->guardian : '' }}" class="form-control" placeholder="Father / Guardian" required ></td>
                        </tr>
                        <tr>
                          <th>Phone Number:</th>
                          <td class="{{ ($errors->has('phone')) ? 'has-error' : '' }}">
                             @if($errors->has('phone'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('phone') }}</label>
                            @endif
                            <input type="text"  name="phone" id="phone" value="{{ isset($person->phone2) ? $person->phone2 : '' }}" class="form-control" placeholder="Phone number" ></td>
                        </tr>
                        <tr>
                          <th>Scholarship Category:</th>
                          <td class="{{ ($errors->has('category')) ? 'has-error' : '' }}">
                            @if($errors->has('category'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('category') }}</label>
                            @endif
                            {{ $category }}
                          </td>
                        </tr>
                        <tr>
                          <th>Course:</th>
                          <td class="{{ ($errors->has('course')) ? 'has-error' : '' }}">
                            @if($errors->has('course'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('course') }}</label>
                            @endif
                            {{ $course }}
                           </td>
                        </tr>
                        <tr>
                          <th>Institution:</th>
                          <td class="{{ ($errors->has('institution')) ? 'has-error' : '' }}">
                            @if($errors->has('institution'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('institution') }}</label>
                            @endif
                            <input type="text" name="institution" value="{{ isset($application->institution) ? $application->institution : '' }}" class="form-control" placeholder="Where you study" required ></td>
                        </tr>
                    </tbody>
                </table>
                </div>
              </div>
       <div class="row">
        <div id="add_row" class="col-md-8 col-md-offset-2 text-center">
        {!! csrf_field() !!}
          <br>
          @if($application->status<=3 )
						<a href="{{ route('print-application',['id'=> $application->id])}}" target="_blank" class="btn btn-flat bg-primary save text-center "> &nbsp; <i class="fa fa-fw fa-arrow-circle-left"></i>Print &nbsp; <i class="fa fa-print"></i> </a> &nbsp;&nbsp;&nbsp;
			  	@endif
          <a href="{{ URL::previous() }}" class="btn btn-flat bg-olive save text-center "> &nbsp; <i class="fa fa-fw fa-arrow-circle-left"></i>BACK &nbsp; </a> &nbsp;
          <button class="btn btn-flat bg-olive  text-center "> &nbsp; Save<i class="fa fa-fw  fa-check"></i> &nbsp; </button>
          @if (in_array($application->getStatus->status_text, ['Registered','Incomplete']))
           <a href="#" data-toggle="modal" data-target="#approveModal" onload="hideSave()" data-whatever="@mdo" class="btn btn-flat bg-blue" id="approve-btn">Approve</a>
          @endif
        </div>
      	</div>
    </form>
  <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="approveModalLabel"><label id="modal-id"> Admin Approve {{ $application->refno }}</label> :{{ $person->personname }} <label style="inline:block" id="modal-name"></label></h4>
        </div>
        <div class="modal-body">
     		 <form role="form" action="{{ route('admin-approve-app') }}" method="post" id="form-approve">
          <div class="row">
            <div class="col-md-12">
               <div class="form-group">
                <input type="text" class="hidden" value="{{ $application->id }}" name="appli_id" hidden>
                <input type="text" class="hidden" value="{{ $person->personname }}" name="appli_name" hidden>
              </div>

             <div class="form-group">
                <label for="recipient-name" class="control-label">Approval Comments:</label> <br/>
                <div class="{{ ($errors->has('status_reason')) ? 'has-error' : '' }}">
                @if($errors->has('status_reason'))
                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('status_reason') }}</label>
                @endif
                <textarea value="" class="form-control" placeholder="Approval Comments" name="status_reason">{{ old('status_reason') }}</textarea>
              </div>
              </div>
            </div>
          </div>

      <div class="modal-footer">
        <a type="button" class="btn btn-flat btn-danger" data-dismiss="modal">Close</a>
        {!! csrf_field() !!}
        <button type="button" onClick="form.submit()" role="submit" class="btn btn-flat btn-success" id="approve">Approve</button>
      </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('js')
  @parent
  <script src="{{ asset('iCheck/icheck.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('pluggins/input-mask/jquery.inputmask.js') }}" type="text/javascript"></script>
  <script src="{{ asset('pluggins/input-mask/jquery.inputmask.date.extensions.js') }}" type="text/javascript"></script>
  <script src="{{ asset('pluggins/input-mask/jquery.inputmask.extensions.js') }}" type="text/javascript"></script>
    <script>
    $(document).ready(function(){

      // Disabling first Element in all select in this forms
      function hideSave(){
      $(".save").hide();
    }
     // $('#approveModal').modal('show');
     if($('#district').val() != null)
      {
        $.ajax({
            url: "{{ route('post-ajax-district') }}",
            type:'POST',
            data:{
              district:$("#district").val(),
              _token: "{{ csrf_token() }}"
            },
            success: function(result){
              $('#area')[0].options.length = 0;
              $('#unit')[0].options.length = 0;
              $("#area").prepend("<option value='' disabled>ഏരിയ</option>").val('');
              $("#unit").prepend("<option value='' disabled>യൂണിറ്റ്</option>").val('');
              var list = $('#area')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, key);
              });
            },
            complete: function(){
              $("#area").val("{{ isset($person->area) ? $person->area :'' }}");
              $("#unit").val("{{ isset($person->unit) ? $person->unit :'' }}");
              if($('#area').val() != null)
              {
                  $.ajax({
                    url: "{{ route('post-ajax-area') }}",
                    type:'POST',
                    data:{
                      area:$('#area').val(),
                      _token: "{{ csrf_token() }}"
                    },
                    success: function(result){
                      $('#unit')[0].options.length = 0;
                       $("#unit").prepend("<option value='' disabled>യൂണിറ്റ്</option>").val('');
                      var list = $('#unit')[0]; // HTMLSelectElement
                      $.each(result, function(key, text) {
                          list.options[list.options.length] = new Option(text, key);
                      });
                    },
                    complete:function(){
                      $("#area").val("{{ isset($person->area) ? $person->area :'' }}");
                      $("#unit").val("{{ isset($person->unit) ? $person->unit :'' }}");
                    }
                });
              }
            }
        });
      }
      $('#district').change(function(){
        $.ajax({
            url: "{{ route('post-ajax-district') }}",
            type:'POST',
            data:{
              district:$(this).val(),
              _token: "{{ csrf_token() }}"
            },
            success: function(result){
              $('#area')[0].options.length = 0;
              $("#area").prepend("<option value='' selected disabled>--ഏരിയ--</option>").val('');
              var list = $('#area')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, key);
              });
            }
        });
      });

      $('#area').change(function(){
        $.ajax({
            url: "{{ route('post-ajax-area') }}",
            type:'POST',
            data:{
              area:$(this).val(),
              _token: "{{ csrf_token() }}"
            },
            success: function(result){
              $('#unit')[0].options.length = 0;
              $("#unit").prepend("<option value='' selected disabled>--യൂണിറ്റ്--</option>").val('');
              var list = $('#unit')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, key);
              });
            }
        });
      });
    // Ajax Manipulation ends here

      //Mobile Mask  (999) 999-9999
      $('#mobile').inputmask("9999999999");  //static mask
      $('#phone').inputmask("9999999999");  //static mask
      $('#aadhar_card').inputmask("9999 9999 9999");  //static mask

      // Pin Mask
      $('#pin').inputmask("999-999");
      //Datemask dd/mm/yyyy
      $("#dob").inputmask("dd-mm-yyyy", {"placeholder": "dd-mm-yyyy"});
    });

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
		$("#result-table").dataTable({
    order: [ [0, 'desc'] ]
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
  <script src="{{ asset('pluggins/sweet-alert/lib/sweet-alert.js') }}" type="text/javascript"></script>
@stop
