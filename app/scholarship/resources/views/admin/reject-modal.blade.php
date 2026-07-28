<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="rejectModalLabel">Reject Application <label id="reject-modal-refno"></label> : <label style="inline:block" id="reject-modal-person"></label></h4>
      </div>
      <div class="modal-body">
      <form role="form" action="{{ route('admin-reject-app') }}" method="post" id="form-reject">
          <div class="row">
            <div class="col-md-12">
                <input type="text" class="hidden"  id="r_app_appli_id" name="id" value="" hidden>
              <div class="form-group">
                <label for="recipient-name" class="control-label">Reason for rejection</label> <br/>
                <div class="{{ $errors->has('area_admin_extras') ? 'has-error' : '' }}">
                @if($errors->has('reason_rejection'))
                    <label class="control-label" for="inputError">
                      <i class="fa fa-times-circle-o"></i> {{ $errors->first('reason_rejection') }}
                    </label>
                  @endif
                <textarea value="" class="form-control" required="" placeholder="Enter the reason for rejection" name="reason_rejection">{{ old('reason_rejection') }}</textarea>
              </div>
              </div>
            </div>
      </div>
      <div class="modal-footer">
      {!! csrf_field() !!}
        <button type="button" class="btn btn-flat btn-danger" id="reject-close" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-flat btn-warning" id="reject-amount">Reject Application</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>
