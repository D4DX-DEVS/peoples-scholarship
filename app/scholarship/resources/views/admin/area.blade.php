@extends('layouts.dashboard')

@section('title', 'Admin | Areas')

@section('content_header')
    <h1>Areas</h1>
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
          <h3 class="box-title">Add Area</h3>
        </div>
        <div class="box-body">
          <form action="{{ route('admin-post-add-area') }}" method="post">
          <div class="row">
                    <div class="col-xs-5">
                    <div class="form-group">
                      <label>Name</label>
                        <input class="form-control" type="text" name="area">
                      </div>
                    </div>
                    

            

                    <div class="col-xs-4">
                      <div class="form-group">
                        <label>District</label>
                        {!!  Form::select('district', $districts , null ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'district',
                                  'required',
                                ]);
                            !!}
                      </div>
                    </div>


                    <div class="col-xs-3">
                      <div class="form-group">
                        <label></label>
                      <button class="btn btn-block btn-primary">Add</button>
                      </div>
                    </div>

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
          <h2 class="box-title"> Areas</h2>
          
        </div><!-- /.box-header -->

        <div class="box-body table-responsive " style="min-height:350px;over-flow:hidden">
          <table class="table " id="result-table">
           <thead>
            <tr>
              <th>Area</th>
              <th>District</th>
              <th>Area President</th>
              <th>Mobile</th>
              <th>Admin</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
             @foreach($results as $result)
             <tr>
               <td>{{ $result->area }} </td>
               <td>{{ $result->district->district }} </td>
               <td>{{ isset($result->areaauth) ? $result->areaauth->area_president :''}} </td>
               <td>{{ isset($result->areaauth) ? $result->areaauth->president_mobile : ''}} </td>
               <td><a href="{{ route('admin_area_admin',['areaid'=>$result->id,'districtid'=>$result->district_id]) }}" class="btn btn-block btn-primary">Edit</a> </td>
               <td><a href="{{ route('admin_area_delete',['id'=>$result->id]) }}" class="delete btn btn-block btn-danger">Delete</a> </td>
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
   <script type="text/javascript">
  $(document).ready(function(){
$('#result-table').dataTable();
    

              $('#result-table').on('click','.delete', function(e){
              e.preventDefault();
              url = $(this).attr('href');
              swal({
              title: "Are you sure?",
              text: "Please make sure your actions!",
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
  });
  </script>

  @stop