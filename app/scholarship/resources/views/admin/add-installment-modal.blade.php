<div class="modal fade" id="addInstallmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Installment</h4>
      </div>
        <form action="{{ route('add-new-installment',['id'=>$application->id])}}" method="post">
          {!! csrf_field() !!}
      <div class="modal-body">
      <div class="form-group {{ ($errors->has('due_date_new')) ? 'has-error' : ''}}">
              @if($errors->has('due_date_new')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('due_date_new') }}</label> @endif
              @if($errors->has('due_date_new'))
                <p class="hidden" id="hasError">1</p>
              @endif
            <div id=""><label>Due Date:</label></div>
            <input type="text" name="due_date_new" value="{{old('due_date_new')}}" class="form-control date-input">
      </div>
      <div class="form-group {{ ($errors->has('amount_new')) ? 'has-error' : ''}}">
              @if($errors->has('amount_new')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('amount_new') }}</label> @endif
              @if($errors->has('amount_new'))
                <p class="hidden" id="hasError">1</p>
              @endif
            <div id=""><label>Amount:</label></div>
            <input type="number" step=".01" name="amount_new" value="{{old('amount_new')}}" class="form-control">
      </div>
      <div class="form-group {{ ($errors->has('reason_new')) ? 'has-error' : ''}}">
              @if($errors->has('reason_new')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('reason_new') }}</label> @endif
              @if($errors->has('reaon_new'))
                <p class="hidden" id="hasError">1</p>
              @endif
            <div id=""><label>Reason:</label></div>
            <input type="text" name="reason_new" value="{{old('reason_new')}}" class="form-control">
      </div>      
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-success" name="sumbit" value="Save">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->