@extends('layouts.dashboard')

@section('title', 'Admin | Category & courses')

@section('content_header')
    <h1>Category / Course</h1>
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
          <h3 class="box-title">Edit Category / Course</h3>
        </div>
      
        <div class="box-body">

          <form action="{{ route('settings-post-course-admin') }}"  method="post">
          <div class="row">
             <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Category</label>
                        <input class="form-control" id="inputEmail3" name="catname" value="{{ $result->category->catname }}"  type="text">
                        <input type="hidden" value="{{ $result->category->id }}" name="catid">
                    </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label">Course</label>
                        <input class="form-control" id="inputEmail3" name="coursename" value="{{ $result->coursename }}"  type="text">
                    </div>
              </div>
              <div class="col-lg-4">
              @if($errors->has('course_enabled'))
                    <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('course_enabled') }}</label>
                              @endif
                            <div class="radio">
                            <label>
                              {!! Form::radio('course_enabled', '1', isset($result->course_enabled) ? ($result->course_enabled==1) : true ) !!} Enabled
                            </label>
                          </div>
                          <div class="radio">
                            <label>
                              {!! Form::radio('course_enabled', '0', isset($result->course_enabled) ? ($result->course_enabled==0) : false) !!} Disabled
                            </label>
                          </div>
              </div>
                <div class="col-lg-4">
                <div class="form-group">
                      <label for="inputEmail3" class=" control-label"></label>
                      <span style="display:block"></span>
                        <button class="btn btn-primary" style="vertical-align:center">Update</button>            
                    </div>
              </div>              
              <div class="col-lg-4">
                <div class="form-group">
                <a href="{{ route('admin_category_delete',['id'=>$result->category->id]) }}" class="delete-cat btn btn-block btn-danger">Delete Category</a>                     </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                <a href="{{ route('admin_course_delete',['id'=>$result->id]) }}" class="delete-course btn btn-block btn-danger">Delete Course</a>                     </div>
              </div>




              <input type="hidden"  value="{{ $result->id }}" name="courseid">
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
    <script type="text/javascript">
  $(document).ready(function(){
    $('.delete-course').on('click', function(e){
              e.preventDefault();
              url = $(this).attr('href');
              swal({
              title: "Are you sure?",
              text: "Sure to Delete Course !",
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

          ////

         $('.delete-cat').on('click', function(e){
              e.preventDefault();
              url = $(this).attr('href');
              swal({
              title: "Are you sure?",
              text: "Delete a Category results to delete all related Courses!",
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