<div class="modal fade" id="grantModal" tabindex="-1" role="dialog" aria-labelledby="grantModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="grantModalLabel">Grant Application <label id="grant-modal-refno"></label> : <label style="inline:block" id="grant-modal-person"></label></h4>
      </div>
      <div class="modal-body">
      <form role="form" action="{{ route('admin-grant-app') }}" method="post" id="form-grant">
          <div class="row">
            <div class="col-md-12">
                <input type="text" class="hidden"  id="app_appli_id" name="id" value="dd" hidden>
              <div class="form-group">
                <label for="granded_amount" class="control-label">Amount to be granted:</label> <br/>
                  <div class="{{ $errors->has('granted_amount') ? 'has-error' : '' }}">
                  @if($errors->has('granted_amount'))
                      <label class="control-label" for="inputError">
                        <i class="fa fa-times-circle-o"></i> {{ $errors->first('granted_amount') }}
                      </label>
                    @endif
                    <input type="number" class="form-control" placeholder="Enter amount in digit" required="" name="granted_amount">
                </div>
              </div>
              <div class="form-group">
                <label for="noofinstallments" class="control-label">Number of Installments:</label> <br/>
                  <div class="{{ $errors->has('noofinstallments') ? 'has-error' : '' }}">
                  @if($errors->has('noofinstallments'))
                      <label class="control-label" for="inputError">
                        <i class="fa fa-times-circle-o"></i> {{ $errors->first('noofinstallments') }}
                      </label>
                    @endif
                    <input type="number" class="form-control" placeholder="No.of.Installments" required="" min="1" max="5" name="noofinstallments" value="1">
                </div>
              </div>
            </div>
      </div>
      <div class="modal-footer">
      {!! csrf_field() !!}
        <button type="button" class="btn btn-flat btn-danger" id="grant-close" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-flat btn-warning" id="grant-amount">Grant Application</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>