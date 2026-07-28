@extends('layouts.dashboard')
@section('title', 'Admin | Year Settings')

@section('content_header')
    <h1>Year Settings</h1>
@stop
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
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@elseif(Session::has('fail'))
    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
@endif
<div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Settings</h3>
        </div>
        <div class="box-body">
          <form id="addform" action="{{ route('admin-post-year-settings') }}" method="post">
					<div class="row">
                    <div  class="col-lg-3">
                    <div class="form-group">
                      <label>Year (Period)</label>
 												<input type="text" id="year_period" name="year_period" class="form-control" placeholder="New year period" value="{{isset($curYear->yearperiod)? $curYear->yearperiod : old('year_period')}}">
										  </div>
                    </div>

                  <div  class="col-lg-3">
                        <div class="form-group has-feedback {{ ($errors->has('appli_limit')) ? 'has-error' : '' }}">
                              <label class="control-label">Applications Limit:</label>
                            @if($errors->has('appli_limit'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>  {{ $errors->first('appli_limit') }}</label>
                            @endif
														<input type="number" id="appli_limit" name="appli_limit" class="form-control" placeholder="Limit Number" value="{{isset($curYear->applimit) ? $curYear->applimit : old('appli_limit')}}" >
                          </div>
                  </div>
                    <div  class="col-lg-3">

                      <div class="form-group has-feedback {{ ($errors->has('entry_enable')) ? 'has-error' : '' }}">
                        @if($errors->has('entry_enable'))
                          <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('entry_enable') }}</label>
                        @endif
                        <label class="control-label">Application-Entry:</label>
													<div class="radio">
															<label>
																{!! Form::radio('entry_enable', '1', isset($curYear->entryenable) ? ($curYear->entryenable==1) : true ) !!} Enabled
															</label>&nbsp;&nbsp;&nbsp;&nbsp;
															<label>
																{!! Form::radio('entry_enable', '0', isset($curYear->entryenable) ? ($curYear->entryenable==0) : false) !!} Disabled
															</label>
														</div>
                        </div>
                    </div>
										<div class="col-lg-3">
											<div class="form-group">
                            <a  class="addnew btn btn-success">Add New</a><br>
														<label for="inputEmail3" class=" control-label"></label>
														<span style="display:block"></span>
														<button class="btn btn-primary" style="vertical-align:center">Update</button>            
											</div>
										</div>
										<input type="hidden" name="id" value="{{ isset($curYear->id)? $curYear->id :''}}" id="yearid">

</div>

                  {!! csrf_field() !!}

          </form>
        </div>
      </div>

      </div>
      </div>
<div class="row">
    <div class="col-lg-12">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title">  Years</h2>
          
        </div><!-- /.box-header -->

        <div class="box-body table-responsive " style="min-height:350px;over-flow:hidden">
          <table class="table datatable" id="result-table">
           <thead>
            <tr>
              <th>Year Period</th>
              <th>Applcations Limit</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
             @foreach($yearSets as $cyear)
             <tr>
               <td>{{$cyear->yearperiod}}</td>
               <td>{{ $cyear->applimit }} </td>
               <td>
               <a href="{{ route('admin_year_delete',['id'=>$cyear->id]) }}" class="delete-year btn btn-danger">Delete</a> 
               </td>
             </tr>
             @endforeach
             </tbody>
          </table>
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
		$('#result-table').dataTable({
    order: [ [0, 'desc'] ]
    });

    $('#year_period').inputmask("9999-99");  //static mask

    // delete year
          $('.delete-year').on('click', function(e){
              e.preventDefault();
              url = $(this).attr('href');
              swal({
              title: "Are you sure?",
              text: "Delete a Year results to delete all Applications of that year!",
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

      $('.addnew').on('click', function(e){
          e.preventDefault();
          $('#year_period').val("");
          $('#appli_limit').val("");
          $('#yearid').val("");       
      });
	});
</script>
@stop