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
          <div class="pull-right">
            <a href="#" id="print-all" class="btn btn-default btn-sm" target="_blank">Print all</a>
            <a href="#" id="export-csv" class="btn btn-default btn-sm">Excel</a>
          </div>
        </div><!-- /.box-header -->

        <div class="box-body table-responsive " style="min-height:350px;over-flow:hidden">
          <div id="tfilter">
            <select id="filter-district" class="form-c">
              <option value="">Filter by district</option>
              @foreach($districts as $districtName)<option value="{{ $districtName }}">{{ $districtName }}</option>@endforeach
            </select>
          </div>
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
             {{-- Rows are loaded ten at a time from area-data. --}}
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
    // Paged on the server so the page no longer loads every area, each of
    // which cost a query for its district and another for its office holders.
    function areaFilters(d) {
      d.district = $('#filter-district').val();
    }

    var areaTable = $('#result-table').DataTable({
      processing: true,
      serverSide: true,
      deferRender: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      ajax: { url: "{{ route('area-data') }}", data: areaFilters },
      order: [[1, 'asc']],
      columnDefs: [{ targets: [4, 5], orderable: false, searchable: false }]
    });

    $('#filter-district').on('change', function () { areaTable.ajax.reload(); });

    function areaExportUrl(format) {
      var order = areaTable.order()[0] || [1, 'asc'];
      return "{{ route('area-export') }}?" + $.param({
        format: format,
        district: $('#filter-district').val() || '',
        search: areaTable.search() || '',
        order: [{ column: order[0], dir: order[1] }]
      });
    }
    $('#print-all').on('click', function (e) { e.preventDefault(); window.open(areaExportUrl('print'), '_blank'); });
    $('#export-csv').on('click', function (e) { e.preventDefault(); window.location = areaExportUrl('csv'); });


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