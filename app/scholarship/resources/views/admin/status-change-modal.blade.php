<div class="modal fade" id="statusChangeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Change Status</h4>
      </div>
        <form action="{{route('change-file-status',['id'=>$file->id])}}" method="post" id="statusChangeForm">
          {{ csrf_field() }}
      <div class="modal-body" id="statusChangeBody">
          <div @if($user->isFieldcoordinator()) style="visibility: hidden;" @endif class="form-group  {{ ($errors->has('new_status')) ? 'has-error' : '' }}">
          @if($errors->has('new_status'))
            <p class="hidden" id="hasError">1</p>
          @endif
          
      				@if($errors->has('new_status')) 
                <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('new_status') }}</label> 
              @endif
	      			<label for="new_status">Status:</label>
      				<select class="form-control" name='new_status'  id="new_status">
      					<option value="" disabled selected>Select Status</option>  
                @if(!$user->isAccountsAdmin()) {{-- avoid these status if accountadmin  --}}
                    @if ($file->grant_status===0)
                        <option value="2" @if(old('new_status',$file->status)=='2') selected @endif @if($file->status=='2') '' @endif>Verification Needed</option>
                    @endif
                  @if ($file->grant_status===0)
                    <option value="3" @if(old('new_status',$file->status)=='3') selected @endif @if($file->status=='3') '' @endif>Interview</option>
                  @endif  
                  
                  @if ($file->grant_status===0)
                        <option value="4" @if(old('new_status',$file->status)=='4') selected @endif @if($file->status=='4') '' @endif>Meeting</option>
                      @if ($user->isOfficeAdmin() || $user->isOfficeSuper())
                        <option value="5" @if(old('new_status',$file->status)=='5') selected @endif @if($file->status=='5') '' @endif>Rejected</option>
                        <option value="6" @if(old('new_status',$file->status)=='6') selected @endif @if($file->status=='6') '' @endif>Granted</option>
                        <option value="7" @if(old('new_status',$file->status)=='7') selected @endif @if($file->status=='7') '' @endif>Granted with Condition</option>
                        <option value="9" @if(old('new_status',$file->status)=='9') selected @endif @if($file->status=='9') '' @endif>Granted as Loan</option>
                      @endif
                  @endif
                @endif  
                @if ($user->isAccountsAdmin())
                  <option value="10" @if(old('new_status',$file->status)=='10') selected @endif >waiting for documents</option>
                @endif
                @if($file->status===7)
                  <option value="8" @if(old('new_status',$file->status)=='8') selected @endif @if($file->status=='8') '' @endif>Condition fulfilled</option>
                @endif
                @if ($file->status===10)
                  <option value="11" @if(old('new_status',$file->status)=='11') selected @endif @if($file->status=='11') '' @endif>Documents Received</option>
                @endif
                @if ($file->status===11 && $user->isAccountsAdmin())
                  <option value="12" @if(old('new_status',$file->status)=='12') selected @endif @if($file->status=='12') '' @endif>Documents Received by Accounts Admin</option>
                @endif
                @if ($file->grant_status===0)
        					<option value="14" @if(old('new_status',$file->status)=='14') selected @endif @if($file->status=='14') '' @endif>Custom Status</option>
                  @if(!$user->isAccountsAdmin())
                    <option value="15" @if(old('new_status',$file->status)=='15') selected @endif @if($file->status=='15') '' @endif>Pending</option>
                  @endif
                @endif
      				</select>
      			</div>
            <div class="form-group {{ ($errors->has('date')) ? 'has-error' : ''}}">
              @if($errors->has('date'))
                <p class="hidden" id="hasError">1</p>
              @endif
              @if($errors->has('date')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('date') }}</label> 
              @endif
              <div id="dateLabel"><label>Date:</label></div>
              <input type="text" disabled name="date" id="date-input" value="{{date('Y-m-d H:i:s')}}" class="form-control">
            </div>
            <div class="form-group {{ ($errors->has('noOfInstallments')) ? 'has-error' : ''}}">
              @if($errors->has('noOfInstallments'))
                <p class="hidden" id="hasError">1</p>
              @endif
              @if($errors->has('noOfInstallments')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('noOfInstallments') }}</label> 
              @endif
              <div id="noOfInstallmentsLabel"></div>
              <input type="number" name="noOfInstallments" id="noOfInstallments" value="{{old('noOfInstallments')}}" class="form-control {{ ($errors->has('noOfInstallments')) ? '' : 'hidden'}}">
            </div>
            <div class="form-group {{ ($errors->has('amountPerInstallment')) ? 'has-error' : ''}}">
              @if($errors->has('amountPerInstallment'))
                <p class="hidden" id="hasError">1</p>
              @endif
              @if($errors->has('amountPerInstallment')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('amountPerInstallment') }}</label> 
              @endif
              <div id="amountPerInstallmentLabel"></div>
              <input type="number" step=".01" name="amountPerInstallment" id="amountPerInstallment" value="{{old('amountPerInstallment')}}" class="form-control {{ ($errors->has('amountPerInstallment')) ? '' : 'hidden'}}">
            </div>
            <div class="form-group {{ ($errors->has('meeting_id')) ? 'has-error' : ''}}">
              @if($errors->has('meeting_id'))
                <p class="hidden" id="hasError">1</p>
              @endif
              @if($errors->has('meeting_id')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('meeting_id') }}</label> 
              @endif
              <div id="meeting_idLabel"></div>
              <select name="meeting_id" id="meeting_id" class="form-control {{ ($errors->has('meeting_id')) ? '' : 'hidden'}}">
                <option value="">Select Serial Number</option>
                @foreach ($meetings as $meeting)
                  <option value="{{$meeting->id}}">{{$meeting->serial_no}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group {{ ($errors->has('reason')) ? 'has-error' : ''}}">
              @if($errors->has('reason'))
                <p class="hidden" id="hasError">1</p>
              @endif
              @if($errors->has('reason')) <label class="control-label error-msg" for="inputError" ><i class="fa fa-times-circle-o"></i>{{ $errors->first('reason') }}</label> 
              @endif
              <div id="reasonLabel"></div>
              <textarea type="text" name="reason" id="statusChangeReason" placeholder="ALT+M for malayalam transliteration" class="form-control {{ ($errors->has('reason')) ? '' : 'hidden'}}">{{old('reason')}}</textarea>
            </div>
            <div class="form-group">
              <label >Further Action:</label>
              <textarea type="text" name="faction" id="faction" placeholder="Further Action" class="form-control">{{$file->further_action}}</textarea>
            </div>

      </div>
      <div class="modal-footer">
        <div class="pull-right">
        <input type="submit" class="btn btn-success" name="sumbit" value="Save new status">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
      </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->