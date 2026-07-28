@extends('layouts.dashboard')

@section('title', 'Admin | Edit Meeting')

@section('content')
    @if ( $errors->count() > 0 )
      <div class="callout callout-info">
        <h4>Tip!</h4>
          @foreach( $errors->all() as $message )
            <p>{{ $message }}</p>
          @endforeach
               
           
      </div>
    @endif
     <!--  success or failure message -->
      @if(Session::has('success'))
          <div class="alert alert-success">{{ Session::get('success') }}</div>
      @elseif(Session::has('fail'))
          <div class="alert alert-danger">{{ Session::get('fail') }}</div>
      @endif
    
      <!-- Default box -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Edit Meeting</h3>          
        </div>
       <form role="form" method="post" class="form-horizontal" action="{{route('post-edit-meeting',['id'=>$meeting->id])}}">
        <div class="box-body">
         <div class="container">
             <div class="form-group ">
               <label class="col-md-1" for="serial_no">Serial number</label>
               <div class="col-md-8 {{ ($errors->has('serial_no')) ? 'has-error' : '' }} "><input type="text" name="serial_no" value="{{old('serial_no',$meeting->serial_no)}}" class="form-control" id="serial_no" placeholder="Meeting serial no"> @if($errors->has('serial_no')) <label class="col-md-8 control-label error-msg" for="serial_no">{{ $errors->first('serial_no') }}</label> @endif</div>
             </div>
             <input type="hidden" id="_token" value="{{ csrf_token() }}">
             <input type="hidden" name="id" value="{{ $meeting->id }}">
               {!! csrf_field() !!}
             
           <div class="form-group ">
               <label class="col-md-1" for="meeting_date">Meeting Date</label>
               <div class="col-md-8 {{ ($errors->has('meeting_date')) ? 'has-error' : '' }} "><input type="text" name="meeting_date" value="{{old('meeting_date',date('d-m-Y', strtotime($meeting->date)))}}" class="form-control" id="datepicker" placeholder="Meeting date"> @if($errors->has('meeting_date')) <label class="col-md-8 control-label error-msg" for="meeting_date">{{ $errors->first('meeting_date') }}</label> @endif</div>
             </div>
             <div class="form-group ">
               <label class="col-md-1" for="time">time</label>
               <div class="col-md-8 {{ ($errors->has('time')) ? 'has-error' : '' }} "><input type="text" name="time" value="{{old('time',$meeting->time)}}" class="form-control" id="time" placeholder="Meeting time"> @if($errors->has('time')) <label class="col-md-8 control-label error-msg" for="time">{{ $errors->first('time') }}</label> @endif</div>
             </div>
             <div class="form-group">
               <label class="col-md-1" for="type">Meeting type</label>
               <div class="col-md-2">{{$meeting->meeting_type}}</div><div class="col-md-4"> <a href="#" class="btn btn-xs btn-warning" title="Edit" id="type-change"><i class="fa fa-pencil"></i></a></div>
             </div>
             <div class="form-group {{ ($errors->has('type_other')) ? '' : 'hidden'}}" id="type-change-div">
               <label class="col-md-1"></label>
               <div class="col-md-4 {{ ($errors->has('type')) ? 'has-error' : '' }} ">
               <select class="form-control" name="type_new" id="meeting-type">
                        <option value='0'>Select meeing type</option>
                        <option @if(old('type_new')=='executive') selected @endif value="executive">executive</option>
                        <option @if(old('type_new')=='bz trust') selected @endif value="bz trust">bz trust</option>
                        <option @if(old('type_new')=='peoples trust') selected @endif value="peoples trust">peoples trust</option>
                        <option @if(old('type_new')=='other') selected @endif value="other">other</option>
               </select>
                @if($errors->has('type')) <label class="col-md-8 control-label error-msg" for="type">{{ $errors->first('type') }}</label> @endif</div>
                <div id="type-div" class="col-md-4 form-group has-feedback {{ ($errors->has('type_other')) ? 'has-error' : 'hidden'}}">
                      <div class="col-md-2"><label>Specify</label></div>
                      <div class="col-md-1"><strong>:</strong></div>
                      <div class="col-md-6"><input class="form-control" type="text" name="type_other"></div>
                      @if($errors->has('type_other')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('type_other') }}</label> @endif
                      </div>
             </div>
             
             <div class="form-group">
                <label class="col-md-1">Participants /Remarks</label>
                <div class="col-md-8"><textarea value="" class="form-control" placeholder="Remarks" name="remarks">{{ old('remarks',$meeting->remarks) }}</textarea></div>
               
             </div>
             <div class="form-group">
                <div class="col-md-5">
                 </div>
                <div class="col-md-1">
                </div>
             </div>
         </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
        <div class="pull-right">
          <button type="submit" class="btn btn-success">Submit</button>
          <a href="{{route('view-meeting',['id'=>$meeting->id])}}" class="btn btn-success cancel">Cancel</a>
        </div>
        </div>
        <!-- /.box-footer-->
     </form>
      </div>
      <!-- /.box -->
  @endsection

@section('js')
@parent
<script src="{{ asset ('pluggins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>
  <script type="text/javascript">
   $(document).ready(function(){
/*----------display input field to specify new category-------------*/
     $('#category').change(function(){ 
       if($(this).val() == 'Other')
       {
        $("#specify").css("display", "block");
       }
       else
       {
          $("#specify").css("display", "none");
       }

     });
    /*------------- Date picker------------------*/
     $( function() {
      
       $( "#datepicker" ).datepicker({ format: "dd-mm-yyyy",  todayHighlight: true,});
       /* $("#datepicker").datepicker("setDate", new Date('dd/mm/yyyy'));*/
        $(document).off('.datepicker.data-api');
       
      } );
   /* ------------datepicker end--------------------*/

   });/*end of document ready*/

   /*----meeting-type selection---*/

   $('#type-change').click(function(){
      $('#type-change-div').removeClass('hidden');
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
  </script> 
@stop

