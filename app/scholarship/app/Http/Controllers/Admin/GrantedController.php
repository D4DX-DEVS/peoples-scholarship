<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\User;
use Auth;
use App\Application;
use App\Installment;
use App\Status;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class GrantedController extends Controller
{
    /**
     * The Authenticated User implementation.
     *
     * @var Auth
     */
    protected $user;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth::user();
        view()->share('user', $this->user);
    }

    public function getApplicationsGranted(){
         $applications = Application::where('grant_status','>','0')->orderBy('id','desc')->get();
         return view('admin.granted.granted-list')->with('applications',$applications);
    }

    public function getInstallments($id)
    {
        $application=Application::find($id);
        $installments = Installment::where('appl_id',$id)->orderBy('installment_number')->get();
        return view('admin.granted.installments')->with('installments',$installments)->with('application',$application);
    }

        /**
     * add a new installment
     */
    public function addInstallment(Request $request,$id)
    {
        $this->validate($request,['due_date_new'=>'required','amount_new'=>'required']);
        $application = Application::find($id);
        $installment = $application->installments->last();
        $new_installment = new Installment;
        $new_installment->installment_number = (isset($installment)?$installment->installment_number:0)+1;
        $new_installment->due_date = date('Y-m-d H:i:s',strtotime($request->due_date_new));
        $new_installment->amount = $request->amount_new;
        $new_installment->appl_id=$id;
        if(isset($installment) && $installment->status>=3)
        {
            $new_installment->status = 2;
        }
        else
        {
            $new_installment->status = 1;
        }
        $new_installment->reason = isset($request->reason_new)?$request->reason_new:'';
        $application->amount_granted += $request->amount_new;
        $application->no_of_installments+=1;
        if($new_installment->save() && $application->save())
        {
            // save new status in statistics..
            return redirect()->back()->with('success','Successfully Added a new installment.');
        }
        else
        {
            return redirect()->back()->with('fail','Something went wrong. Could not save the changes');
        }
    }

    public function postInstallments(Request $request , $id)
    {
        $this->validate($request,['due_date'=>'required','amount'=>'required|numeric|min:1']);
        $installment = Installment::find($id);
        $installment->due_date = date('Y-m-d H:i:s',strtotime($request->due_date));
        $installment->amount = $request->amount;
        $installment->status = $request->status;
        $installment->reason = $request->reason;
        $installment->save();
        $application = Application::find($installment->appl_id);
        $installments = $application->installments;
        if($request->status==1 || $request->status==2){
            $application->grant_status=8;
        }
        elseif($request->status==3){
            $application->grant_status=7;
            $application->app_track .="\nInsatllment ".$installment->installment_number." ".$application->getGrantStatus($application->grant_status)['statusText'] ." On ".date('d-m-Y');
        }
        elseif($request->status==4) {
            $next_installment = 0;
            foreach ($installments as $key => $value) {
                if($value->status<4 && $value->id!==$installment->id){
                    $next_installment = $installments[$key];
                    break;
                }
            }
            if($next_installment)
            {
               $next_installment->status=2;
               $next_installment->save();
               $application->grant_status=8;
            }
            $application->delivered_date=date('Y-m-d H:i:s');
        }
        if($application->installments->last()->id == $id && $request->status==4)
        {
            $application->grant_status=9;
            $application->status=8;
            $application->delivered_date=date('Y-m-d H:i:s');
            $application->app_track .="\n".$application->getGrantStatus($application->grant_status)['statusText'] ." On ".date('d-m-Y');
        }
        $total_amount=0.00;
        $no_of_installments = 0;
        foreach ($installments as $key) {
                $no_of_installments++;
                $total_amount+=$key->amount;
        }
        $application->no_of_installments = $no_of_installments;
        $application->amount_granted = $total_amount;

        if($application->save())
        {
            return redirect()->back()->with('success','Changes saved successfully.');
        }
        else
        {
            return redirect()->back()->with('fail','changes could not be saved');
        }
    }

       /**
     * Delete an installment
     * change the installment number (of remaining installments) according to the number of installment being deleted - change total amount and no of installments in corresponding application
     * change status according to status of deleted installment
     * if deleted installment makes application status complete, 
     * last installment cannot be deleted.. atleast one must remain for granted applications-- can be edited
     */

    public function deleteInstallment($id)
    {
        $toDelete = Installment::find($id);
        $application = Application::find($toDelete->appl_id);
        $no_of_installments = 0;
        $total_amount = 0.00;
        $installments = $application->installments;
        //must maintain atlease one installment for granted files
        if(sizeof($installments)<=1)
        {
            return redirect()->back()->with('fail','Cannot delete. Must keep atleast one installment');
        }
        $next_installment = 0;
        foreach ($installments as $key => $value) {
            if($value->status<4 && $value->id!==$toDelete->id){
                $next_installment = $installments[$key];
                break;
            }
        }
        if($toDelete->status == 2)
        {
         if($next_installment)
         {
            $next_installment->status=2;
            $next_installment->save();
         }
         else if($application->status!==8)
         {
            $application->status = 8;
            $application->reason_status = "Last pending installment deleted.";
            $application->save();
         }   
        }
        else{
            if(!$next_installment){
                $application->status = 8;
                $application->reason_status = "Last pending installment deleted.";
                $application->save();
            }
        }
        foreach ($installments as $installment) 
        {
                //subtract by 1 the installment numbers of all installments after $toDelete 
            if($installment->installment_number>$toDelete->installment_number)
            {
                $installment->installment_number-- ;
            }
            //get the new total amount and no of installments
            if($installment->id !== $toDelete->id)
            {
                $total_amount += $installment->amount;
                $no_of_installments++;
            }
                $installment->save();
        }
        $application->amount_granted = $total_amount;
        $application->no_of_installments = $no_of_installments;
        $application->save();
        if($toDelete->delete())
        {
            return redirect()->back()->with('success','Successfully Deleted installment.');
        }
        else
        {
            return redirect()->back()->with('fail','Failed to delete');
        }
    }

    public function getDuesThisMonth()
    {
       $toDate = date('Y-m-d H:i:s',strtotime("+1 months"));
       $installments = Installment::whereNotNull('due_date')->where('status','<','4')->where('due_date','<',$toDate)->get();
       return view('admin.granted.installments-due')->with('installments',$installments);
    }
}
