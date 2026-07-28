@extends('layouts.appfront')

@section('title', 'Peoples Foundation | Scholarship')

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
      <form role="form" method="post" action="{{route('post-application-start')}}">
      <div style="margin-left:180px;">
         <h3>Instructions</h3>
        <ul>
        <li><b>Verify Name, Mobile No. and Aadhar No. before proceeding<b></li>
        <li><b>Fill all required data in the appropriate fields<b></li>
        <li><b>A passport size photo (scanned image) need to be uploaded<b></li>
        <li><b>Save application form and take Print Out<b></li>
        </ul>
           <div class="label status bg-red" style="margin-left:40px;font-size:16px;">While filling form do not press 'Back button' or 'Refresh button' !! </div>

      </div>
                <div class="row">
                  <div class="col-md-8 col-md-offset-2">
                  <br><br>
                  <table class="table table-bordered">
                        <tbody>

                        <tr>
                          <th>പേര്:</th>
                          <td class="{{ ($errors->has('name')) ? 'has-error' : '' }}">
                            @if($errors->has('name'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('name') }}</label>
                            @endif
                            <input type="text" name="name" value="" class="form-control" placeholder="Name"></td>
                        </tr>         
                        <tr>
                          <th>മൊബൈല്‍ നമ്പര്‍:</th>
                          <td class="{{ ($errors->has('mobile')) ? 'has-error' : '' }}">
                             @if($errors->has('mobile'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('mobile') }}</label>
                            @endif
                            <input type="text"  name="mobile" id="mobile" value="" class="form-control" placeholder="Mobile number" ></td>
                        </tr>
                        <tr>
                          <th>ആധാർ കാര്‍ഡ് നമ്പര്‍:</th>
                          <td class="{{ ($errors->has('aadhar')) ? 'has-error' : '' }}">
                            @if($errors->has('aadhar'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('aadhar') }}</label>
                            @endif
                            <input type="text" name="aadhar" id="aadhar" value="" class="form-control" placeholder="Aadhar card number" ></td>
                        </tr>
                    </tbody>
                </table>
                </div>
              </div>
              <div class="row">
                  <div class="col-md-8 col-md-offset-2 text-right">
                    {!! csrf_field() !!}
                    <button class="btn bg-olive btn-flat margin">Proceed</button>
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



    // Ajax Manipulation ends here

      //Mobile Mask  (999) 999-9999
      $('#mobile').inputmask("9999999999");  //static mask
      $('#aadhar').inputmask("999999999999");  //static mask
      
     });

    </script>
@stop
