@extends('layouts.dashboard')

@section('title', 'Admin | Units')

@section('content_header')
    <h1>Unit Details</h1>
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
          <h3 class="box-title">Unit update</h3>
        </div>
        <div class="box-body">
          <form action="{{ route('admin-post-edit-unit') }}"  method="post">
    
            <input type="hidden" name="id" value="{{ $result->id }}" id="unitid">
            <div class="row">
                    <div class="col-md-4">
                    <div class="form-group">
                      <label>Unit Name</label>
                        <input class="form-control" type="text" name="unit" id="unitadd" value="{{ $result->unit }}">
                      </div>
                    </div>

                    <div class="col-md-4">
                              <div class="form-group has-feedback {{ ($errors->has('district')) ? 'has-error' : '' }}">
                              <label class="control-label col-sm-3">District:</label>
                            @if($errors->has('district'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>  {{ $errors->first('district') }}</label>
                            @endif
                            
                            <!-- new -->
                            {!!  Form::select('district',  $districts , $result->district_id  ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'district',
                                  'required',
                                ]);
                            !!}  
                            
                           <!-- new end -->
                          </div>
                  </div>
                    <div class="col-md-4">

                      <div class="form-group has-feedback {{ ($errors->has('area')) ? 'has-error' : '' }}">
                        @if($errors->has('area'))
                          <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('area') }}</label>
                        @endif
                        <label class="control-label col-sm-3">Area:</label>
                        {!!  Form::select('area', 
                                      ['null' => '--select--',] 
                                      , $result->area_id,
                                      [
                                        'class' => 'form-control', 
                                        'id'=>'area', 
                                        'required',
                                      ]); 
                        !!}
              
                        </div>
                    </div>
                    <div class="col-md-4" id="unit-container" style="display:none">
                    <div class="form-group has-feedback {{ ($errors->has('unit')) ? 'has-error' : '' }}">
                      @if($errors->has('unit'))
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('unit') }}</label>
                      @endif
                      <label class="control-label col-sm-3">Unit:</label>
                      <div class="col-sm-9">
                      {!!  Form::select('unit_dump', 
                                    ['All' => 'All',] +
                                     [],'all',
                                    [
                                      'class' => 'form-control', 
                                      'name'=>'unit_dump', 
                                      'id'=>'unit', 
                                    ]); 
                      !!}
                      <span class="glyphicon glyphicon-screenshot form-control-feedback"></span>
                      </div>
                    </div>
                    </div>
</div>    
        <div class="row">
                             <div class="col-md-4">
                            <div class="form-group">
                              <label>President Name</label>
                                <input class="form-control" type="text" value="{{ $result->presiname }}" name="name" required>
                              </div>
                            </div>
                            <div class="col-md-4">
                            <div class="form-group">
                              <label>Contact Number</label>
                                <input class="form-control" type="text" value="{{ $result->presinumber }}" name="number" id="presinumber" required>
                              </div>
                            </div>
                        <div class="col-md-4">
                              <div class="form-group">
                                <label></label>
                              <button class="btn btn-block btn-primary">Update</button>
                              </div>
                            </div>
                    </div>
                          {!! csrf_field() !!}
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

     // Ajax Manipulation
      // When load
      $("#unit-container").hide();
      $('#presinumber').inputmask("9999999999");  //static mask

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
              $("#area").prepend("<option value='' disabled>Area</option>").val('');
              $("#unit").prepend("<option value='' disabled>Unit</option>").val('');
              var list = $('#area')[0]; // HTMLSelectElement
              $.each(result, function(key, text) { 
                  list.options[list.options.length] = new Option(text, key);
              }); 
            },
            complete: function(){
              $("#area").val("{{ $result->area_id }}");
              $("#unit").val("{{ $result->id }}");
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
                       $("#unit").prepend("<option value='' disabled>Unit</option>").val('');
                      var list = $('#unit')[0]; // HTMLSelectElement
                      $.each(result, function(key, text) { 
                          list.options[list.options.length] = new Option(text, text);
                      }); 
                    },
                    complete:function(){
                      $("#area").val("{{ $result->area_id }}");
                      $("#unit").val("{{ $result->id }}");
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
                  list.options[list.options.length] = new Option(text, text);
              });
            }
        });
      });
  
    // Ajax Manipulation ends here
  });
</script>
  @stop