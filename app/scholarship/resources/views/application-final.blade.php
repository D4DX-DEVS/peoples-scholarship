@extends('layouts.appfront')

@section('title', 'Peoples Foundation | Scholarship')
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
</style>
@stop
@section('content_header')
  <h1 style="line-height:60px" class="text-center">
    HIGHER EDUCATION SCHOLARSHIP  {{ $yearsetting->yearperiod ?? null }}
  </h1>
@stop

@section('content')
      @if(Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
      @elseif(Session::has('fail'))
          <div class="alert alert-danger">{{ Session::get('fail') }}</div>
      @endif
         <!-- Default box -->
         <form role="form" method="post" enctype="multipart/form-data" action="{{route('post-application-final')}}">
         <div class="row">
  <div class="col-md-4 col-md-offset-4 text-center {{ ($errors->has('profile_pic')) ? 'has-error' : '' }}">

      @if($errors->has('profile_pic'))
        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('profile_pic') }}</label>
      @endif
      <input type="hidden" name="persid" value="{{ $persid }}">
      <input type="hidden" name="applid" value="{{ $applid }}">
      <div id="file-uploader">
        <div id="file_img" style="background-image: url({{ ($person->photourl != '') ? route('getFile', ['filename' => $person->photourl]) : asset('images/uploads/nopic.jpg')  }})" ></div>
        <input type="file" id="file-btn" name="profile_pic" accept="image/*" value="{{ ($person->photourl != '') ? route('getFile', ['filename' => $person->photourl]) : '' }}"/>
      </div>
        <label class="btn  btn-flat btn-success" style="width: 210px; padding: 10px 0 ; margin:0 auto 10px auto;">UPLOAD PHOTO</label> <br>
        <span><strong>(Size: 150 x 150, Max: 500KB)</strong></span>
  </div>
</div>
                <div class="row">
                  <div class="col-md-8 col-md-offset-2">
                  <table class="table table-bordered">
                        <tbody>
                        <tr>
                          <th>Ref. No:</th>
                          <td><input type="text" name="refno" value="{{ $refno }}" class="form-control" placeholder="ID" readonly></td>
                        </tr>
                        <tr>
                          <th>പേര്:</th>
                          <td class="{{ ($errors->has('name')) ? 'has-error' : '' }}">
                            @if($errors->has('name'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('name') }}</label>
                            @endif
                            <input type="text" name="name" value="{{ $person->personname }}" class="form-control"  readonly></td>
                        </tr>
                        <tr>
                          <th>മൊബൈല്‍ നമ്പര്‍:</th>
                          <td class="{{ ($errors->has('mobile')) ? 'has-error' : '' }}">
                             @if($errors->has('mobile'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('mobile') }}</label>
                            @endif
                            <input type="text"  name="mobile" id="mobile" value="{{ $person->mobile }}" class="form-control" readonly  ></td>
                        </tr>
                        <tr>
                          <th>ആധാർ കാര്‍ഡ് നമ്പര്‍:</th>
                          <td class="{{ ($errors->has('aadhar')) ? 'has-error' : '' }}">
                            @if($errors->has('aadhar_card'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('aadhar_card') }}</label>
                            @endif
                            <input type="text" name="aadhar" id="aadhar" value="{{ $person->aadhar }}" class="form-control" readonly ></td>
                        </tr>
                        <tr>
                          <th>പുരുഷന്‍ / സ്ത്രീ :</th>
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
                          <th>വയസ്സ്:</th>
                          <td class="{{ ($errors->has('age')) ? 'has-error' : '' }}">
                             @if($errors->has('age'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('age') }}</label>
                            @endif
                            <input type="number" name="age" value="{{ isset($person->age) ? $person->age : '' }}" maxlength="3" class="form-control" placeholder="Age"  min="15" max="40" required></td>
                        </tr>
                        <tr>
                          <th>ജനന തിയ്യതി:</th>
                          <td class="{{ ($errors->has('dob')) ? 'has-error' : '' }}">
                             @if($errors->has('dob'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('dob') }}</label>
                            @endif
                            <input type="date" name="dob" id="" value="{{ isset($person->dob) ? $person->dob : '' }}" maxlength="10" class="form-control" placeholder="dd/mm/yyyy" required></td>
                        </tr>
                        <tr>
                          <th>മേല്‍വിലാസം:</th>
                          <td class="{{ ($errors->has('address')) ? 'has-error' : '' }}">
                             @if($errors->has('address'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('address') }}</label>
                            @endif
                            <textarea name="address" class="form-control" rows="3" placeholder="Address" required >{{ isset($person->address) ? $person->address : 'Address' }}</textarea></td>
                        </tr>
                        <tr>
                          <th>പിന്‍കോഡ്:</th>
                          <td class="{{ ($errors->has('pin')) ? 'has-error' : '' }}">
                             @if($errors->has('pin'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('pin') }}</label>
                            @endif
                            <input type="text" name="pin" id="pin" value="{{ isset($person->pin) ? $person->pin : '' }}" class="form-control" placeholder="Pincode" required></td>
                        </tr>

                        <tr>
                          <th>ഇ-മെയിൽ:</th>
                          <td class="{{ ($errors->has('email')) ? 'has-error' : '' }}">
                              @if($errors->has('email'))
                                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('email') }}</label>
                              @endif
                              <input type="email" class="form-control" name="email" placeholder="Email" value="{{ isset($person->email) ? $person->email : '' }}" />
                          </td>
                        </tr>
                        <tr>
                          <th>ജില്ല:</th>
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
                          <th>ഏരിയ :</th>
                          <td class="{{ ($errors->has('area')) ? 'has-error' : '' }}">
                            @if($errors->has('area'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('area') }}</label>
                            @endif
                            {!!  Form::select('area',
                                ['ഏരിയ' => 'ഏരിയ',] +
                                 [], $person->area ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'area',
                                  'required',
                                ]);
                            !!}
                        </tr>
                        <tr>
                          <th>യൂണിറ്റ്:</th>
                          <td class="{{ ($errors->has('unit')) ? 'has-error' : '' }}">
                            @if($errors->has('unit'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('unit') }}</label>
                            @endif
                            {!!  Form::select('unit',
                                ['യൂണിറ്റ്' => 'യൂണിറ്റ്',] +
                                 [], $person->unit ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'unit',
                                  'required',
                                ]);
                            !!}
                        </tr>
                        <tr>
                          <th>പിതാവ്/ഗാർഡിയൻ :</th>
                          <td class="{{ ($errors->has('guardian')) ? 'has-error' : '' }}">
                            @if($errors->has('guardian'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('guardian') }}</label>
                            @endif
                            <input type="text" name="guardian" value="{{ isset($person->guardian) ? $person->guardian : '' }}" class="form-control" placeholder="Father / Guardian" required ></td>
                        </tr>
                        <tr>
                          <th>ഫോൺ നമ്പര്‍:</th>
                          <td class="{{ ($errors->has('phone')) ? 'has-error' : '' }}">
                             @if($errors->has('phone'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('phone') }}</label>
                            @endif
                            <input type="text"  name="phone" id="phone" value="{{ isset($person->phone2) ? $person->phone2 : '' }}" class="form-control" placeholder="Phone number" ></td>
                        </tr>
                        <tr>
                          <th>സ്കോളർഷിപ്  കാറ്റഗറി:</th>
                          <td class="{{ ($errors->has('category')) ? 'has-error' : '' }}">
                            @if($errors->has('category'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('category') }}</label>
                            @endif
                               {!!  Form::select('category', [null=>'--Please Select--'] + $categories , false ,
                                [
                                  'class' => 'form-control',
                                  'required',
                                  'id'=>'category',
                                ]);
                            !!}
                          </td>
                        </tr>
                        <tr>
                          <th>കോഴ്സ്:</th>
                          <td class="{{ ($errors->has('course')) ? 'has-error' : '' }}">
                            @if($errors->has('course'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('course') }}</label>
                            @endif
                               {!!  Form::select('course', [null=>'--Course--'] , false ,
                                [
                                  'class' => 'form-control',
                                  'required',
                                  'id'=>'course',
                                ]);
                            !!}
                              @if($errors->has('course_other'))
                                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('education_other') }}</label>
                              @endif
                              <input type="text" name="course_other" value=""  id="course_other" class="form-control" placeholder="മറ്റുള്ളവ ഇവിടെ വിശദമാക്കുക"></td>
                          </td>
                        </tr>
                        <tr>
                          <th>പഠിക്കുന്ന സ്ഥാപനം:</th>
                          <td class="{{ ($errors->has('institution')) ? 'has-error' : '' }}">
                            @if($errors->has('institution'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('institution') }}</label>
                            @endif
                            <input type="text" name="institution" value="" class="form-control" placeholder="Where you study" required ></td>
                        </tr>
                    </tbody>
                </table>
                </div>
              </div>
              <div class="row">
                  <div class="col-md-8 col-md-offset-2 text-right">
                    <a class="btn bg-orange btn-flat margin">Cancel</a>
                    {!! csrf_field() !!}
                    <button class="btn bg-olive btn-flat margin">Save Data</button>
                  </div>
              </div>
              </form>
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
      $('select').each(function(){
        $('option',this).first().attr('disabled','disabled');;
      });
      $('#course_other').addClass('hidden');
      $('#course_other').val("");

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

      $('#category').change(function(){
        $.ajax({
            url: "{{ route('post-ajax-category') }}",
            type:'POST',
            data:{
              category:$(this).val(),
              _token: "{{ csrf_token() }}"
            },
            success: function(result){
              $('#course')[0].options.length = 0;
              $("#course").prepend("<option value='' selected disabled>--കോഴ്സ്--</option>").val('');
              var list = $('#course')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, key);
              });
              $("#course").append("<option value='10000'>മറ്റുള്ളവ(വ്യക്തമാക്കുക)</option>").val('');
            }
        });
        $('#course_other').addClass('hidden');
        $('#course_other').val("");
      });

       // Playing with ohters box
       $('#course').change(function(){
        var selectedValue = $(this).val();
        var lastValue = $('option',this).last().val();

        if(selectedValue == lastValue)
        {
            $('#course_other').removeClass('hidden');
             $('#course_other').focus();
         }
        else
        {
            $('#course_other').addClass('hidden');
            $('#course_other').val("");
         }
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

    </script>
  <script src="{{ asset('pluggins/sweet-alert/lib/sweet-alert.js') }}" type="text/javascript"></script>
  <script>
    $(document).ready(function()
    {
      $('#file-btn').change(function()
      {
        var extensions = ['jpg', 'png', 'gif', 'jpeg'];
        var extension = $(this).val().split('.').pop();
        var result = jQuery.inArray(extension, extensions);
        if(result > -1)
        {
          if(this.files && this.files[0] )
          {
            var fr = new FileReader();
            fr.onload = function(arg)
            {
                $('#file_img').css('background-image', 'url('+arg.target.result+')');
            };
              fr.readAsDataURL(this.files[0]);
          }
        }
        else
        {
          swal("Ooops!", "Please Select an Image!", "error");
        }
      });
    });
</script>
@stop
