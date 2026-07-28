@extends('layouts.dashboard')

@section('title', 'Admin | Area Leaders')

@section('content_header')
    <h1>Area Details</h1>
@stop

@section('content')

  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Area Leaders</h3>
        </div>
      
        <div class="box-body">

          <form action="{{ route('admin-post-add-area-admin') }}"  method="post">
          <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Area</label>
                        <input class="form-control" id="inputEmail3" name="areaname" value="{{ $result->area }}"  type="text">
                    </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Area coordinator</label>
         
                        <input class="form-control"  required="required" id="inputEmail3" name="area_cordinator" value="{{ isset($leaders->area_cordinator)? $leaders->area_cordinator :'' }}"  type="text">
             
                    </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Coordinator Mobile</label>
         
                        <input class="form-control" type="mobile" name="cordinator_mobile" id="coordmobile" value="{{ isset($leaders->cordinator_mobile)? $leaders->cordinator_mobile :'' }}" required >
                </div>
              </div>
          
             <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">District</label>
                        <input class="form-control" id="inputEmail3"  value="{{ $result->district->district }}" disabled="" type="text">
                        <input type="hidden" value="{{ $result->district->id }}" name="districtid">
                    </div>
              </div>

              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Area President</label>
         
                        <input class="form-control" id="inputEmail3" name="area_president" value="{{ isset($leaders->area_president)? $leaders->area_president :'' }}"  type="text" required>
             
                    </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">President Mobile</label>
         
                        <input class="form-control" id="presimobile" name="president_mobile" value="{{ isset($leaders->president_mobile)? $leaders->president_mobile :'' }}"  type="mobile" required>
             
                    </div>
              </div>

                <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label"></label>
                      <span style="display:block"></span>
                        <button class="btn btn-primary" style="vertical-align:center">Update</button>            
                    </div>
              </div>
              <input type="hidden"  value="{{ $result->id }}" name="areaid">
                  {!! csrf_field() !!}
          </div>
          </form>
        </div>
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
   <script type="text/javascript">
  $(document).ready(function(){
          //Mobile Mask  (999) 999-9999
      $('#coordmobile').inputmask("9999999999");  //static mask
      $('#presimobile').inputmask("9999999999");  //static mask
  });
  </script>

  @stop