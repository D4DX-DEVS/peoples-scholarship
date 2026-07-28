@extends('adminlte::page')

@section('title', 'Scholarship | Admin')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row" >
    <div class="col-lg-4">
         <div class="box box-warning">
                <div class="box-header with-border">            
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div><!-- /.box-tools -->
                  <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total applications status wise</span>
                  <span class="info-box-number" style="font-size:29px"> {{ $applic_all }}</span>
                  <span class="progress-description">
                   without incomplete and cancelled
                  </span>
                </div><!-- /.info-box-content -->
              </div>
              </div><!-- /.box-header -->
          <div class="box-body">
          <div class="row">
              <div class="col-md-8">
                <div class="chart-responsive">
                  <canvas id="pieChart" height="180" ></canvas>
                </div><!-- ./chart-responsive -->
              </div><!-- /.col -->
              <div class="col-md-4">
                <ul class="chart-legend clearfix">
                  <li><i class="fa fa-circle-o text-primary"></i> Granted</li>
                  <li><i class="fa fa-circle-o text-info"></i> Varified</li>
                  <li><i class="fa fa-circle-o text-green"></i> Pending</li>
                  <li><i class="fa fa-circle-o text-red"></i> Meeting</li>
                  <li><i class="fa fa-circle-o text-maroon"></i> Waiting</li>
                  <li><i class="fa fa-circle-o text-gray"></i> Rejected</li>
                </ul>
              </div><!-- /.col -->
            </div><!-- /.row -->
           <ul class="nav nav-stacked">
              <li><a href="#">No. of Granted applications<span class="pull-right badge bg-blue">{{ $granted_all}}</span></a></li>
              <li><a href="#">No. of Verified applications <span class="pull-right badge bg-aqua">{{ $approved_all }}</span></a></li>
              <li><a href="#">No. of Pending applications <span class="pull-right badge bg-green">{{ $pending_all }}</span></a></li>
              <li><a href="#">Applications in meeting <span class="pull-right badge bg-red">{{ $meeting_all }}</span></a></li>
              <li><a href="#">Applications in waiting/ not varified <span class="pull-right badge bg-maroon">{{ $waiting_all }}</span></a></li>
              <li><a href="#">Rejected applications <span class="pull-right badge bg-grey">{{ $rejected_all }}</span></a></li>
            </ul>
          </div><!-- /.box-body -->
        </div>
    </div>
    <div class="col-lg-8">
         <div class="box box-warning">
          <div class="box-header with-border">
            <h4 class="box-title">Search</h4>
            <div class="box-tools pull-right">
            </div>    
          </div>
          <form class="form-vertical" action="{{route('application-search')}}" method="post">
            {!! csrf_field() !!}
            <div class="box-body">
              <div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="form-group has-feedback {{ ($errors->has('district')) ? 'has-error' : ''}}">
                      @if($errors->has('district')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('district') }}</label> @endif
                      <select class="form-control" name="district" id="district"> 
                        <option value="0">District</option>
                        @foreach ($districts as $district)
                          <option value="{{$district->id}}">{{$district->district}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>  
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="form-group has-feedback {{ ($errors->has('area')) ? 'has-error' : ''}}">
                      @if($errors->has('area')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('area') }}</label> @endif
                      <select class="form-control" name="area" id="area"> 
                        <option value="0">Area</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <div class="form-group has-feedback {{ ($errors->has('unit')) ? 'has-error' : ''}}">
                      @if($errors->has('unit')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('unit') }}</label> @endif
                      <select class="form-control" name="unit" id="unit"> 
                        <option value="0">Unit</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                      <div class="form-group has-feedback {{ ($errors->has('app_number')) ? 'has-error' : ''}}">
                        @if($errors->has('app_number')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('app_number') }}</label> @endif
                        <input type="text" name="app_number" value="{{old('app_number')}}" class="form-control" placeholder="Enter application number">
                      </div>
                    </div>
                </div>    
                <div class="col-md-4">
                    <div class="form-group">
                      <div class="form-group has-feedback {{ ($errors->has('app_name')) ? 'has-error' : ''}}">
                        @if($errors->has('app_name')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('app_name') }}</label> @endif
                        <input type="text" name="app_name" value="{{old('app_name')}}" class="form-control" placeholder="Enter applicant name">
                      </div>
                    </div>
                </div>   
                <div class="col-md-4">
                  <input type="submit" name="submit" class="btn btn-success btn-sm pull-right" value="Search">
                </div> 
              </div>
              </div>
            </div>
            </form>
        </div>
        <div class="col-md-8">
          <div class="box">
            <div class="box-header with-border">
              <div class="box-tools pull-right">    
              <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>              
              </div><!-- /.box-tools -->
              <h4 class="box-title">New Applications</h4>
            </div> 
            <div class="box-body">
                <table class="table table-responsive table-hover">
                  <thead>
                    <th>App. number</th>
                    <th>Applicant name</th>
                    <th>District</th>
                  </thead>
                  <tbody>
                    @foreach ($new_applications as $new_app)
                      <tr>
                        <td><a href="{{route('view-application',['id'=>$new_app->id])}}" target="_blank">{{$new_app->refno}}</a></td>
                        <td>{{$new_app->applicant_name}}</td>
                        <td>{{isset($new_app->district)?$new_app->district->district:''}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
                <div class="box-footer">
                  <a href="/admin/applications/registered" class="btn btn-info btn-xs pull-right">View All</a>
                </div>       
          </div>
        </div>
    </div>

</div>
@stop
@section('js')
  @parent
  <script src="{{ asset('pluggins/chartjs/Chart.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('pluggins/morris/morris.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('pluggins/knob/jquery.knob.js') }}" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
  $(".knob").knob();

   /* Morris.js Charts */

           //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvasApps = $("#pieChart").get(0).getContext("2d");
        var pieChartApps = new Chart(pieChartCanvasApps);
        var PieDataApps = [
          {
            value: {{ $granted_all }},
            color: "#3c8dbc",
            highlight: "#3c8dbc",
            label: "Granted"
          },
          {
            value: {{ $approved_all }},
            color: "#3c8dbc",
            highlight: "#3c8dbc",
            label: "Varified"
          },
          {
            value: {{ $pending_all }},
            color: "#00a65a",
            highlight: "#00a65a",
            label: "Pending"
          },
          {
            value: {{ $meeting_all }},
            color: "#f56954",
            highlight: "#f56954",
            label: "Meeting"
          },
          {
            value: {{ $waiting_all }},
            color: "#D81B60",
            highlight: "#D81B60",
            label: "Not varified/waiting"
          },
          {
            value: {{ $rejected_all }},
            color: "#d2d6de",
            highlight: "#d2d6de",
            label: "Rejected"
          }
        ];
        var pieOptionsApps = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 2,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 50, // This is 0 for Pie charts
          //Number - Amount of animation steps
          animationSteps: 100,
          //String - Animation easing effect
          animationEasing: "easeOutBounce",
          //Boolean - Whether we animate the rotation of the Doughnut
          animateRotate: true,
          //Boolean - Whether we animate scaling the Doughnut from the centre
          animateScale: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true,
          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: true,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        pieChartApps.Doughnut(PieDataApps, pieOptionsApps);


      // Application editi modal scripts

      $(".data").click(function(){
        var id = $(this).attr('id');
        var name = $(this).html();
        $("#modal-id").html(id);
        $("#modal-name").html(name);
      });

      $('#edit').click(function(){
        var data = "bz/results/full/edit/" + $("#modal-id").html();
        location.assign(data);
      });

      $('#view').click(function(){
        var data = "bz/results/full/view/" + $("#modal-id").html();
        location.assign(data);
      });
    });

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
   //           $("#area").val("{{ isset($person->area) ? $person->area :'' }}");
   //           $("#unit").val("{{ isset($person->unit) ? $person->unit :'' }}");
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
                          list.options[list.options.length] = new Option(text, key);
                      }); 
                    },
                    complete:function(){
    //                  $("#area").val("{{ isset($person->area) ? $person->area :'' }}");
     //                 $("#unit").val("{{ isset($person->unit) ? $person->unit :'' }}");
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
              $("#area").prepend("<option value='' selected disabled>--Area--</option>").val('');
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
              $("#unit").prepend("<option value='' selected disabled>--Unit--</option>").val('');
              var list = $('#unit')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, key);
              });
            }
        });
      });
    // Ajax Manipulation ends here
    </script>
@stop