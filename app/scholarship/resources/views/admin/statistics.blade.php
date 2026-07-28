@extends('layouts.dashboard')
<style type="text/css">
 @media print {
  .noprint {display:none;}
  #print-statistics{
    display: none;
  }
 
 }
</style> 
@section('title', 'Admin | Statistics')

@section('content_header')
    <h1>Statistics</h1>
@stop

@section('content')
  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif
  <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Options</h3>
        </div>
        <div class="box-body noprint">
          <form class="form-horizontal" action="{{route('post-statistics')}}" method="post">
          <div class="row">
             <div class="invoice-info">
           <div class="col-md-6 col-md-offset-3">
              <div class="form-group">
                    <label>Date Range:</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" name="date" value="@if(isset($date1)){{date('d/m/Y',strtotime($date1))}}-{{date('d/m/Y',strtotime($date2))}}@endif" class="form-control pull-right" id="reservation" placeholder="Choose a Date Range">
                    </div><!-- /.input group -->
                </div>
           </div>
           </div>
          </div><!-- /.row -->
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group has-feedback row {{ ($errors->has('district')) ? 'has-error' : ''}}">
                        @if($errors->has('district')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('district') }}</label> @endif
                      <div class="col-md-10">
                        <select class="form-control" name="district[]" id="district" multiple>
                        <option value="">All Districts</option>
                          @foreach ($districts as $district)
                            <option @if(isset($district_id) && $district_id==$district->id) selected @endif value="{{$district->id}}">{{$district->district}}</option>
                          @endforeach
                        </select>
                      </div>
                </div>
                
                </div>
                <div class="col-md-4">
                  <div class="form-group has-feedback row {{ ($errors->has('area')) ? 'has-error' : ''}}">
                        @if($errors->has('area')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('area') }}</label> @endif
                      <div class="col-md-10">
                        <select class="form-control" name="area[]" multiple id="area">
                        <option value="" disabled selected hidden>All Areas</option>
                          
                        </select>
                      </div>
                </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group has-feedback row {{ ($errors->has('category')) ? 'has-error' : ''}}">
                        @if($errors->has('category')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('category') }}</label> @endif
                      <div class="col-md-10">
                        <select class="form-control" name="category" id="category">
                        <option value="">All categorys</option>
                          @foreach ($categories as $category)
                            <option @if(isset($cat_id) && $cat_id==$category->id) selected @endif value="{{$category->id}}">{{$category->catname}}</option>
                          @endforeach
                        </select>
                      </div>
                </div>
                </div>
              </div>
              <div>
                    <div class="pull-right">
                      <button class="btn btn-success">Get Statistics</button>
                    </div>
              </div>
              {!! csrf_field() !!}
          </form>
        </div>
        {{-- Statistics Table --}}
        <div class="box-body table-responsive" id="statistic-table">
    <div id="tfilter"></div>
    @if ($x_axis==='district' && $y_axis === 'category')
      <table class="table table-hover table-bordered" style="font-size:12px;">
      <thead>
      <tr>
        <th>Districts</th>
        <th>Files Passed /Delivered</th>
        <th>Amount Passed /Delivered</th>
      </tr>
      </thead>
      <tbody>
        @foreach ($districts as $district)
          <tr>
            <th>{{$district->district}}</th>
            @if (isset($date1) && $date1!==null)
            <td>{{$district->getPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$district->getDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>{{$district->getPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$district->getDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>{{$district->getPassedNumber()}}/{{$district->getDeliveredNumber()}}</td>
            <td>{{$district->getPassedAmount()}}/{{$district->getDeliveredAmount()}}</td>
            @endif
          </tr>
        @endforeach
        <tr>
            <th>Total</th>

            @if (isset($date1) && $date1!==null)
            <td>{{$district->getAllPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$district->getAllDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>{{$district->getAllPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$district->getAllDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>{{$district->getAllPassedNumber()}}/{{$district->getAllDeliveredNumber()}}</td>
            <td>{{$district->getAllPassedAmount()}}/{{$district->getAllDeliveredAmount()}}</td>
            @endif
          </tr>
      </tbody>
    </table>
    @elseif(($x_axis==='area' && $y_axis === 'category'))
     <table class="table table-hover table-bordered" style="font-size:12px;">
      <thead>
      <tr>
        <th>Areas</th>
        <th>Files Passed /Delivered</th>
        <th>Amount Passed /Delivered</th>
      </tr>
      </thead>
      <tbody>
        @foreach($areas as $area)
          <tr>
            <th>{{$area->area}}</th>
            @if (isset($date1) && $date1!==null)
            <td>
            {{$area->getPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$area->getDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>
            {{$area->getPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$area->getDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>
            {{$area->getPassedNumber()}}
            /{{$area->getDeliveredNumber()}}
            </td>
            <td>
            {{$area->getPassedAmount()}}
            /{{$area->getDeliveredAmount()}}
            </td>
            @endif
          </tr>
        @endforeach
        <tr>
            <th>Total</th>
            @if (isset($date1) && $date1!==null)
            <td>{{$areas[0]->district->getPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$areas[0]->district->getDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>{{$areas[0]->district->getPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$areas[0]->district->getDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>{{$areas[0]->district->getPassedNumber()}}/{{$areas[0]->district->getDeliveredNumber()}}</td>
            <td>{{$areas[0]->district->getPassedAmount()}}/{{$areas[0]->district->getDeliveredAmount()}}</td>
            @endif
          </tr>
      </tbody>
    </table>
    @elseif(($x_axis==='unit' && $y_axis === 'category'))
      <table class="table table-hover table-bordered" style="font-size:12px;">
      <thead>
      <tr>
        <th>Units</th>
        <th>Files Passed /Delivered</th>
        <th>Amount Passed /Delivered</th>
      </tr>
      </thead>
      <tbody>
        @foreach($units as $unit)
          <tr>
            <th>{{$unit->unit}}</th>
            @if (isset($date1) && $date1!==null)
            <td>
            {{$unit->getPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$unit->getDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>
            {{$unit->getPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$unit->getDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>
            {{$unit->getPassedNumber()}}
            /{{$unit->getDeliveredNumber()}}
            </td>
            <td>
            {{$unit->getPassedAmount()}}
            /{{$unit->getDeliveredAmount()}}
            </td>
            @endif
          </tr>
        @endforeach
          <tr>
            <th>Total</th>
            @if (isset($date1) && $date1!==null)
            <td>{{$units[0]->area->getPassedNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$units[0]->area->getDeliveredNumber(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            <td>{{$units[0]->area->getPassedAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            /{{$units[0]->area->getDeliveredAmount(isset($cat_id)?$cat_id:"all",$date1,$date2)}}
            </td>
            @else
            <td>{{$units[0]->area->getPassedNumber()}}/{{$units[0]->area->getDeliveredNumber()}}</td>
            <td>{{$units[0]->area->getPassedAmount()}}/{{$units[0]->area->getDeliveredAmount()}}</td>
            @endif
          </tr>
      </tbody>
    </table>
    @endif
  </div>
  <div class="box-footer">
    <button class="btn btn-success pull-right" id="print-statistics">Print</button>
  </div>
</div> <!-- /.box-->

@stop
@section('js')
@parent
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
    $(document).ready(function(){
      //Date range picker
         $('#reservation').daterangepicker(
          {
              format: "DD/MM/YYYY"
          });

         //print table
         $('#print-statistics').click(function(){
         window.print();    
        });
        //  $('#print-statistics').click(function(){
        //   $('#statistic-table').print();
        //   return(false);
        //  })

         /*get area list based on district*/
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
 });
  </script>
@stop