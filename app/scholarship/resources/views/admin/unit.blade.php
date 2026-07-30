@extends('layouts.dashboard')

@section('title', 'Admin | Units')

@section('content_header')
    <h1>Units</h1>
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
          <h3 class="box-title">Add Unit</h3>
        </div>
        <div class="box-body">
          <form id="addform" action="{{ route('admin-post-add-unit') }}" method="post">
          <div class="row">
                    <div class="col-md-4">
                    <div class="form-group">
                      <label>Unit Name</label>
                        <input class="form-control" type="text" name="unit" id="unitadd">
                      </div>
                    </div>

                    <div class="col-md-4">
                              <div class="form-group has-feedback {{ ($errors->has('district')) ? 'has-error' : '' }}">
                              <label class="control-label col-sm-3">District:</label>
                            @if($errors->has('district'))
                              <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>  {{ $errors->first('district') }}</label>
                            @endif
                            
                            <!-- new -->
                            {!!  Form::select('district',  $districts , null ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'district',
                                  'required',
                                ]);
                            !!}  
                            
                           <!-- new end -->
                          </div>
                  </div>
                    <div class="col-md-4">

                      <div class="form-group has-feedback {{ ($errors->has('area')) ? 'has-error' : '' }}">
                        @if($errors->has('area'))
                          <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('area') }}</label>
                        @endif
                        <label class="control-label col-sm-3">Area:</label>
                        {!!  Form::select('area',
                                 [], null ,
                                [
                                  'class' => 'form-control',
                                  'id'=>'area',
                                  'required',
                                ]);
                            !!}                     
                        </div>
                    </div>
</div>
<div class="row">
                    <div class="col-md-3" id="unit-container">
                    <div class="form-group has-feedback {{ ($errors->has('unit')) ? 'has-error' : '' }}">
                      @if($errors->has('unit'))
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {{ $errors->first('unit') }}</label>
                      @endif
                      <label class="control-label col-sm-3">Unit:</label>
                      <div class="col-sm-9">
                      {!!  Form::select('unit_dump', 
                                    ['All' => 'All',] +
                                     [],'all',
                                    [
                                      'class' => 'form-control', 
                                      'name'=>'unit_dump', 
                                      'id'=>'unit', 
                                    ]); 
                      !!}               
                      </div>
                    </div>
                    </div>


                    <div class="col-md-4">
                    <div class="form-group">
                      <label>President Name</label>
                        <input class="form-control" type="text" name="name" id="presiname" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                      <label>President Mobile</label>
                        <input class="form-control" type="text" name="number" id="presinumber" required>
                      </div>
                    </div>
                    <div class="col-md-4">
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
          <h2 class="box-title">  Units</h2>
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
          <table class="table datatable" id="result-table">
           <thead>
            <tr>
              <th>Unit</th>
              <th>Area</th>
              <th>District</th>
              <th>President Name</th>
              <th>Mobile</th>
              <th>Action</th>
            </tr>
            </thead>
             <tbody>
             {{-- Rows are loaded ten at a time from unit-data. --}}
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

    // Units are paged on the server: this table used to render all 1528 rows
    // and run a query per relation per row, which a remote database made
    // unusable.
    function unitFilters(d) {
      d.district = $('#filter-district').val();
    }

    var unitTable = $('#result-table').DataTable({
      processing: true,
      serverSide: true,
      deferRender: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
      ajax: { url: "{{ route('unit-data') }}", data: unitFilters },
      order: [[0, 'asc']],
      columnDefs: [{ targets: [5], orderable: false, searchable: false }]
    });

    $('#filter-district').on('change', function () { unitTable.ajax.reload(); });

    // Exports are rebuilt server-side, mirroring the table's own state.
    function unitExportUrl(format) {
      var order = unitTable.order()[0] || [0, 'asc'];
      return "{{ route('unit-export') }}?" + $.param({
        format: format,
        district: $('#filter-district').val() || '',
        search: unitTable.search() || '',
        order: [{ column: order[0], dir: order[1] }]
      });
    }
    $('#print-all').on('click', function (e) { e.preventDefault(); window.open(unitExportUrl('print'), '_blank'); });
    $('#export-csv').on('click', function (e) { e.preventDefault(); window.location = unitExportUrl('csv'); });

    $('#addform').submit(function() {
    var unitadd = $("#unitadd").val();
    if (unitadd == '') {
        $("#unitadd").focus();
        return false;
    }
    if ($('#unit option:contains('+ unitadd +')').length) {
        swal("Ooops!", "Unit already in list!", "error");
        return false
    }

    });
    $('#result-table').dataTable();

    $("#unit-container").hide();
      $('#presinumber').inputmask("9999999999");  //static mask
      // Ajax Manipulation
      // When load
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
              $("#area").val("");
              $("#unit").val("");
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
                          list.options[list.options.length] = new Option(text, text);
                      }); 
                    },
                    complete:function(){
                      $("#area").val("");
                      $("#unit").val("");
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
              $("#area").prepend("<option value='' selected disabled>--ഏരിയ--</option>").val('');
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
              $("#unit").prepend("<option value='' selected disabled>--യൂണിറ്റ്--</option>").val('');
              var list = $('#unit')[0]; // HTMLSelectElement
              $.each(result, function(key, text) {
                  list.options[list.options.length] = new Option(text, text);
              });
            }
        });
      });
    // Ajax Manipulation ends here
              $('a.delete').click(function(e){
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