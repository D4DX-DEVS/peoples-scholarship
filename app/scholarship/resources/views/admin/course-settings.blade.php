@extends('layouts.dashboard')
@section('title', 'Admin | Course Settings')

@section('content_header')
    <h1>Course Settings</h1>
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

<div class="box">
<div class="box-header">
	<h3 class="box-title">Settings</h3>
</div>
	<div class="box-body">
		<!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Categories</a></li>
              <li><a href="#tab_2" data-toggle="tab">Courses</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
             	<div class="box">
             	<div class="box-header">
             	<h3 class="box-title">Categories</h3>
             	</div>
              	<form action="{{route('add-new-category')}}" id="addcategory" method="post">
             	<div class="box-body">
	             <div class="col-md-4">
	              <ul class="list-unstyled">
	              	@foreach ($categories as $category)
	              		<li>{{ $category->catname }}</li>
	              	@endforeach
		          	</ul>
            	</div>
            	<div class="col-md-4">
              	{!!csrf_field()!!}
              		<div class="form-group">
	                <label for="name" class="control-label">New category</label> <br/>
	                <div class="{{ ($errors->has('catname')) ? 'has-error' : '' }}">
	                @if($errors->has('catname'))
	                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('catname') }}</label>
	                @endif
	                <input type="text" name="catname" class="form-control" placeholder="New category Name" value="{{old('catname')}}">
	              </div>
	              </div>
              	 </div>
              	 </div>
              	 <div class="box-footer">
	              <input type="submit" form="addcategory" value="Add category" class="btn btn-success pull-right">
              	 </div>
              	</form>
              	</div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <div class="box">
                <div class="box-header">
                	<h3 class="box-title">Courses </h3>
                	</div>
		              <form action="{{route('add-new-course')}}" id="addCourse" method="post">
		              <div class="box-body">
		              <div class="col-md-4">
		              {!!csrf_field()!!}
		              <div class="{{$errors->has('category')? 'has-error' : '' }}">
												@if($errors->has('category'))
		                      <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('category') }}</label>
		                    @endif
		                    <select class="form-control" name="category" id="category">
		                    		<option value="">Select category</option>
		                    	@foreach ($categories as $category)
		                    		<option value="{{ $category->id }}">{{ $category->catname }}</option>
		                    	@endforeach
		                    </select>
									</div>
									<div class="form-group">
			                <label for="course-name" class="control-label">New Course</label> <br/>
			                <div class="{{ ($errors->has('course_name')) ? 'has-error' : '' }}">
			                @if($errors->has('course-name'))
			                  <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('course_name') }}</label>
			                @endif
			                <input type="text" name="course_name" class="form-control" placeholder="New Course Name" value="{{old('course_name')}}">
			              </div>
			              </div>
	            	</div>
	            		<div class="col-md-4 col-md-offset-1">
	            			<div>
							<ul class="list-unstyled" name="Course" id="Course">
							</ul>
						</div>
	            		</div>
	            		</div>
	                <div class="box-footer">
		              <input type="submit" form="addCourse" value="Add Course" class="btn btn-success pull-right">
	                </div>
        		</form>
              </div>
              <!-- /.tab-pane -->
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
	</div>
</div>
<div class="row">
    <div class="col-lg-12">
      <div class="box">
        <div class="box-header">
          <h2 class="box-title">Courses</h2>
          
        </div><!-- /.box-header -->

        <div class="box-body table-responsive " style="min-height:350px;over-flow:hidden">
          <table class="table " id="result-table">
           <thead>
            <tr>
              <th>Course</th>
							<th>Enabled (&#10004;)</th>
              <th>Category</th>
              <th>Admin</th>
            </tr>
            </thead>
             <tbody>
             @foreach($courses as $course)
             <tr>
               <td>{{ $course->coursename }} </td>
							 <td>{!! ($course->course_enabled==1) ? '<span>&#10004;</span>':'' !!} </td>
               <td>{{ $course->category->catname }} </td>
               <td><a href="{{ route('settings_course_admin',['courseid'=>$course->id]) }}" class="btn btn-block btn-primary">Admin</a> </td>
              </tr>
             @endforeach
             </tbody>
          </table>
        </div>
      </div>
    </div>

@stop
@section('js')
  @parent
<script type="text/javascript">
	$(document).ready(function(){
		$('#result-table').dataTable();
		/* get Course list on category change*/
  		$('#category').change(function(){
  			$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
	        });
	         $.ajax({
             url: "{{ route('get-ajax-category') }}",
             type:'POST',
             data:{
             category:$(this).val()
             },
             success: function(data){
               $('#course').empty();
               // $("#course").prepend("<option value='' selected disabled hidden >Select Course</option>").val('');
                // var list = $('#course')[0]; // HTMLSelectElement
                $('#course').append('<b>Courses:</b>');
               $.each(data, function(key, value) {
                            $('#course').append('<li>'+ value +'</li>');
                        });
             }
         });
  		});
	});
</script>
@stop