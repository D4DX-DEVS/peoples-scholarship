@extends('layouts.dashboard')

@section('title', 'Admin | Meetings Listing')

@section('content_header')
    <h1>Meetings</h1>
@stop

@section('content')
  
@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif

  <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Add New Meeting</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          </div>
        </div>
        <form action="{{route('add-meeting')}}" method="post">
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
                <div class="form-group has-feedback row {{ ($errors->has('serial_no')) ? 'has-error' : ''}}">
                        @if($errors->has('serial_no')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('serial_no') }}</label> @endif
                      <div class="col-md-3"><label>Serial number</label></div>
                      <div class="col-md-6"><input type="text" name="serial_no" placeholder="Enter serial number" value="{{old('serial_no')}}" class="form-control"></div>
                </div>
                <div class="form-group row {{ ($errors->has('date')|| $errors->has('time')) ? 'has-error' : ''}}">
                        @if($errors->has('date')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('date') }}</label> @endif
                      <div class="col-md-3"><label>Date & Time</label></div>
                      <div class="col-md-4"><input type="text" name="date" id="date-input" placeholder="Select Date" value="{{old('date')}}" class="form-control">
                      </div>
                      @if($errors->has('time')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('time') }}</label> @endif
                      <div class="col-md-2"><input time="text" name="time" value="{{old('time')}}" placeholder="Time" class="form-control">
                      </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group has-feedback row {{ ($errors->has('type')) ? 'has-error' : ''}}">
                        @if($errors->has('type')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('type') }}</label> @endif
                      <div class="col-md-2"><label>Type</label></div>
                      <div class="col-md-1"><strong>:</strong></div>
                      <div class="col-md-6"><select class="form-control" name="type" id="meeting-type">
                        <option @if(old('type')=='executive') selected @endif value="executive">executive</option>
                        <option @if(old('type')=='bz trust') selected @endif value="bz trust">bz trust</option>
                        <option @if(old('type')=='peoples trust') selected @endif value="peoples trust">peoples trust</option>
                        <option @if(old('type')=='other') selected @endif value="other">other</option>
                      </select>
                    </div>
                </div>
                      <div id="type-div" class="row form-group has-feedback {{ ($errors->has('type_other')) ? 'has-error' : 'hidden'}}">
                      @if($errors->has('type_other')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('type_other') }}</label> @endif
                      <div class="col-md-2"><label>Specify</label></div>
                      <div class="col-md-1"><strong>:</strong></div>
                      <div class="col-md-6"><input class="form-control" type="text" name="type_other"></div>
                      </div>
              </div>
              <div class="col-md-6">
                <div class="form-group has-feedback row {{ ($errors->has('remarks')) ? 'has-error' : ''}}">
                        @if($errors->has('remarks')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('remarks') }}</label> @endif
                      <div class="col-md-3"><label>Participants & Remarks</label></div>
                      <div class="col-md-6"><textarea value="" class="form-control" placeholder="Remarks" name="remarks">{{ old('remarks') }}</textarea></div>
                </div>
              </div>
            </div>
                  {!! csrf_field() !!}

          </div>
          <div class="box-footer">
                      <button class="btn btn-success pull-right" >Add</button>
          </div>
          </form>
        </div>

        {{-- meetings details - table --}}
      
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Meetings</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
              </div>
            </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
              <thead>
                <th>Serial Number</th>
                <th>Date</th>
                <th>Type</th>
                <th>Time</th>
                <th>Actions</th>
              </thead>
              <tbody>
              <div class="container">
                @foreach($meetings as $meeting)
                    <tr>
                    <td><div><a href="{{route('view-meeting',['id'=>$meeting->id])}}" title="View">{{$meeting->serial_no}}</a></div></td>
                      <td>{{date('d-m-Y', strtotime($meeting->date))}}</td>
                      <td>{{$meeting->meeting_type}}</td>
                      <td>{{$meeting->time}}</td>
                      <td><div>
                      <a href="{{route('edit-meeting',['id'=>$meeting->id])}}"><button title="Edit" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></button></a>
                      <a href="{{ route('delete-meeting',['id'=>$meeting->id])}}"><button title="Delete" class="btn btn-danger btn-xs delete-btn"><i class="fa fa-trash"></i></button></a></div></td>
                    </tr>
                @endforeach
                </div>
              </tbody>
              </table>
              {!! $meetings->render() !!}
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
@stop
@section('js')
@parent
<script src="{{ asset ('pluggins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      $('.delete-btn').click(function(){
        return confirm("Are you sure you wish to delete this meeting?");
      })
      $('#date-input').datepicker({
      format:'dd-mm-yyyy'
    });
      if($('#meeting-type').val()==='other'){
        $('#type-div').removeClass('hidden');
      }
      $('#meeting-type').change(function(){
        if($('#meeting-type').val()==="other"){
          $('#type-div').removeClass('hidden');
        }
        else{
          $('#type-div').addClass('hidden');
        }
      });
    })
  </script>
@stop