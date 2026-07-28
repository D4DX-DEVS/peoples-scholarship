@extends('layouts.dashboard')
@section('adminlte_css')
  @parent
  <style type="text/css">
@media print
{
.noprint {display:none;}
.dataTables_filter, .dataTables_info, .dataTables_length, .dataTables_paginate { display: none; }
}
</style>
@stop
@section('title', 'Admin | Applications - Meeting')

@section('content')
@if ( $errors->count() > 0 )
      <div class="callout callout-danger">
        <h4>Error!</h4>
          @foreach( $errors->all() as $message )
            <p>{{ $message }}</p>
          @endforeach
               
           
      </div>
@endif  
@if(Session::has('success'))
    <div class="alert alert-success noprint">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger noprint">{{ Session::get('fail') }}</div>
@endif
	<div class="box">
		<div class="box-header">
              <h3 class="box-title">Applications in Meeting "{{$meeting->serial_no}}"</h3>
            </div>    
      <form id="pending_form" action="{{route('move-applications-meeting',['meeting_id'=>$meeting->id])}}" method="post">
      {!!csrf_field()!!}
        <div class="box-body table-responsive">
          <table class="table table-bordered table-hover" id="result-table">
            <thead>
              <th>Application number</th>
              <th>Applicant name</th>
              <th>Address</th>
              <th>Category</th>
              <th>Unit</th>
              <th>Area</th>
              <th>District</th>
              <th>___________Actions__________</th>
            </thead>
            <tbody>
              @foreach ($applications as $application)
                <tr>
                  <td>
                  @if(in_array($application->status,['4']))
                  <input type="checkbox" name="file_selected[]" class="file_checkbox" value="{{$application->id}}">
                  @endif
                  {{$application->refno}}
                  </td>
                  <td>{{$application->person->personname}}</td>
                  <td>{{$application->person->address}}</td>
                  <td>{{$application->category->catname}}</td>
                  <td>{{$application->unit->unit}}</td>
                  <td>{{$application->area->area}}</td>
                  <td>{{$application->district->district}}</td>
                  <td>
                  <a href="{{ route('admin-app-edit', ['appli_id'=> $application->id, 'pers_id'=> $application->persid ] ) }}" title="Edit" class="btn btn-warning btn-xs noprint"><i class="fa fa-pencil"></i></a> &nbsp;
                  <a href="{{ route('remove-application',['meeting_id'=>$meeting->id,'appli_id'=>$application->id]) }}" title="Remove" class="btn btn-xs btn-warning remove-btn noprint"><i class="fa fa-trash"></i></a>
              </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="box-footer noprint">
          <div class="pull-right">

          @if (now() < $meeting_included_date)
            <a href="{{route('get-meeting-applications',['meeting_id'=>$meeting->id])}}" class="btn btn-sm btn-success">Add applications</a>
          @endif 
            <a href="{{route('view-meeting',['id'=>$meeting->id])}}" class="btn btn-sm btn-success cancel">Cancel</a>
            </div>
            <div id="selected_actions" class="hidden">
            <input type="submit" id="pending_btn" class="btn btn-success" value="move to meeting">
            <select name="to_meeting">
            <option value="" selected disabled>Select a meeting</option>
            @foreach ($meetings as $meeting)
              <option value="{{$meeting->id}}">{{$meeting->serial_no}}</option>
            @endforeach
            </select>
          </div>
        </div>
        </form>
    </div>
@stop
@section('js')
@parent
<script type="text/javascript">
    $(document).ready(function(){
        $('.file_checkbox').change(function(){
          if(this.checked){
            $('#selected_actions').removeClass('hidden');
          }
          else if ($(".file_checkbox:checked").length == 0)
            {
              $('#selected_actions').addClass('hidden');
            }
        });
        $('#result-table').dataTable({
          "dom" : "<'row'<'col-md-3'l><'col-md-6'B><'col-md-3 pull-right'f>>" +"t"+
          "<<'col-md-5'i><'col-md-6 pull-right'p>>",
          "buttons": [{
              "extend":'print',
              "exportOptions":{
                "columns":':visible',
                }    },
                {
              "extend":'excel',
              "exportOptions":{
                "columns":':visible',
                }    },
                "colvis"],
               
        });
        $('.remove-btn').click(function(){
            return confirm("Remove from meeting?");
        })
        /* get Department list*/
        $('#category').ready(function(){
            $.ajax({
            url: "{{ route('post-ajax-district') }}",
            type:'POST',
             success: function(data){
           //     var list = $('#category')[0]; // HTMLSelectElement
               $.each(data, function(key, value) {
                            $('#category').append('<option value="'+ key +'">'+ value +'</option>');
                        });
             }
         });
        });

    });
</script>
@stop