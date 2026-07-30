<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\User;
use Auth;
use App\Meeting;
use App\Application;
use App\Category;
use App\Course;
use App\Status;
use App\Statistic;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MeetingController extends Controller
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

    /**
     * meeting homepage
     */
    public function getMeetings(){
        $meetings = Meeting::orderBy('date','desc')->paginate(10);
        return view('admin.meetings.meetingslist')->with('meetings',$meetings);
    }

    /**
     * Add a new meeting
     */
    public function createMeeting(Request $request){
        $this->validate($request,['serial_no'=>'required|unique:meetings','date'=>'required','type'=>'required','time'=>'required']);
        $meeting_type = $request->type;
        if($meeting_type === "other"){
            $this->validate($request,['type_other'=>'required']);
            $meeting_type = $request->type_other;
        }
        $meeting = new Meeting;
        $meeting->serial_no = $request->serial_no;
        $meeting->date = date('Y-m-d H:i:s',strtotime($request->date));
        $meeting->meeting_type = $meeting_type;
        $meeting->time = $request->time;
        $meeting->remarks=$request->remarks;
        if($meeting->save()){
            return redirect()->route('view-meeting',$meeting->id)->with("success","Meeting Successfully Added");
        }
        else{
            return redirect()->back()->with('fail','Could not save meeting.');
        }
    }

    /**
     * get a meeting to view
     */
    public function viewMeeting($id)
    {
        $meeting = Meeting::find($id);
        $statistics = $meeting->statistics;
        $noofApplications =0;
        $applications = array();
        $rejected_applications = array();
        $granted_applications = array();
        $pending_applications = array();
        $granted_total = 0;
       
        foreach ($statistics as $statistic) {

                $application = Application::find($statistic->appl_id);
                // A statistics row can outlive the application it points at
                // (deleting an application leaves its meeting rows behind).
                // The guard below only wrapped the switch, so the lookup
                // further down still dereferenced the missing record.
                if(!$application){
                    continue;
                }
                if($application){
                    switch ($application->status) {
                        case '5':
                            array_push($rejected_applications, $application);
                            break;
                        case '6':
                        case '8':
                            array_push($granted_applications,$application);
                            $granted_total +=$application->amount_granted;
                            break;
                        case '7':
                            array_push($pending_applications,$application);
                            break;
                        default:
                            # code...
                            break;
                    }
                }  
                // The old whereHas filtered on the related application's
                // status, and that application is $application itself, so
                // the condition is decidable here without a join.
                $lastMeetingStatistic = $application->status == 4
                    ? Statistic::where('appl_id',$application->id)
                        ->with('Application')
                        ->orderBy('id','desc')->first()
                    : null;
            if(($application->status==4) && ($lastMeetingStatistic && $lastMeetingStatistic->meeting_id == $id) && (!in_array($application, $applications))){
            $noofApplications++;
            array_push($applications, $application);
            }
        }
        return view('admin.meetings.view-meeting')->with('meeting',$meeting)->with('noofApplications',$noofApplications)
                    ->with('granted_applications',$granted_applications)->with('rejected_applications',$rejected_applications)
                    ->with('pending_applications',$pending_applications)->with('granted_total',$granted_total)
                    ->with('apps',$applications);
    }

 
     /**
     * get meeting to edit
     */
    public function editMeeting($id){
        $meeting = Meeting::find($id);
        return view('admin.meetings.edit-meeting')->with('meeting',$meeting);
    }

    /**
     * save changes to meeting
     */
    public function postEditMeeting($id,Request $request){
        $this->validate($request,['serial_no'=>'required','meeting_date'=>'required','time'=>'required']);
        $meeting = Meeting::find($id);
        if($meeting->serial_no!== $request->serial_no){
            $this->validate($request,['serial_no'=>'unique:meetings']);
            $meeting->serial_no = $request->serial_no;
        }
        $meeting->date = date('Y-m-d H:i:s',strtotime($request->meeting_date));
        $meeting->time = $request->time;
        if($request->type_new!=='0'){
            if($request->type_new === 'other'){
                $this->validate($request,['type_other'=>'required']);
                $meeting->meeting_type = $request->type_other;
            }
            else{
                $meeting->meeting_type = $request->type_new;
            }
        }
        $meeting->remarks = $request->remarks;
        if($meeting->save()){
            return redirect()->route('view-meeting',$meeting->id)->with("success","Meeting Edited Successfully");
        }
        else{
            return redirect()->back()->with('fail','Could not save changes');
        }
    }

    //view files in a meeting
    public function getApplications($id){
        $meeting =Meeting::find($id);
        $statistics = $meeting->statistics;
        $meetings = Meeting::where('id','<>',$id)->get();
        $applications = array();
        $meeting_included_date = date('Y-m-d H:i:s', strtotime($meeting->date . ' +1 day'));
        foreach ($statistics as $statistic) {
            $application = Application::find($statistic->appl_id);
            // Skip rows left behind by a deleted application.
            if(!$application){
                continue;
            }
            // The old whereHas filtered on the related application's status,
            // and that application is $application itself, so the condition
            // is decidable here without a join.
            $lastMeetingStatistic = $application->status == 4
                ? Statistic::where('appl_id',$application->id)
                    ->with('Application')
                    ->orderBy('id','desc')->first()
                : null;
            if(($application->status==4) && ($lastMeetingStatistic && $lastMeetingStatistic->meeting_id == $id) &&(!in_array($application, $applications))){
             array_push($applications, $application);
            }
         }
         return view('admin.meetings.meeting-apps')->with('applications',$applications)->with('meeting',$meeting)->with('meetings',$meetings)->with('meeting_included_date',$meeting_included_date);
    }

    /**
     * Delete meeting
     */
    public function deleteMeeting($id){
        $meeting = Meeting::find($id);

        if($meeting->delete()){
            Statistic::where('meeting_id', $id)->delete();
            return redirect()->back()->with('success','Successfully Deleted Meeting');
        }
        else{
            return redirect()->back()->with('fail','Failed to save changes');
        }
    }

    /**
     * get list of files according to department,category selected
     */
    public function getApplicationsList(Request $request,$meeting_id){
        $meeting = Meeting::find($meeting_id);
        $applications = Application::whereIn('status',['2','3','7'])->orderBy('id','desc')->get();
        return view('admin.meetings.add-applications')->with('applications',$applications)->with('meeting',$meeting);
    }

    /**
     * add files selected to this meeting
     */
    public function addApplications(Request $request, $meeting_id){
        $this->validate($request,['applications_selected'=>'required']);
        $meeting = Meeting::find($meeting_id);
        $applications_selected = $request->applications_selected;
        foreach ($applications_selected as $application) {
            $applicationtoAdd = Application::find($application);
            $applicationtoAdd->app_track .= "\n"."Added to Meeting ".$meeting->serial_no.".";
            $applicationtoAdd->reason_status = "Added to Meeting ".$meeting->serial_no.".";
            $statistic = new Statistic;
            $statistic->appl_id = $application;
            $statistic->meeting_id = $meeting_id;
            $statistic->date = date('Y-m-d H:i:s');
            $statistic->save();
            $applicationtoAdd->status=4;
            $applicationtoAdd->save();
        }
        return redirect()->route('meeting-applications',['id'=>$meeting->id])->with('success','Successfully Added Applications');
    }

    /**
     * Remove selected file from meeting
     */
    public function removeApplication($meeting_id,$application_id){
        $application = Application::find($application_id);
        $meeting = Meeting::find($meeting_id);
            $application->status = '3';
            $application->reason_status = "Removed from meeting".$meeting->serial_no.".";
            $application->app_track .= "\nRemoved from meeting".$meeting->serial_no.".";
            if($application->save())
            {
                $old_statistic = Statistic::where('appl_id',$application_id)->where('meeting_id',$meeting_id)->orderBy('id','desc')->first();
                $old_statistic->delete();
            }    
        return redirect()->back()->with('success','Successfully Removed file from meeting.');
    }

    /**
     * move files from current meeting to selected meeting. save status change
     */
    public function moveApplicationsMeeting(Request $request, $meeting_id){
        $this->validate($request,['to_meeting'=>'required','file_selected'=>'required']);
        $applications_selected = $request->file_selected;
        $from_meeting = Meeting::find($meeting_id);
        $to_meeting = Meeting::find($request->to_meeting);
   //     echo "from ".$from_meeting->serial_no." to ".$to_meeting->serial_no;
        foreach ($applications_selected as $application_id) {
            $applicationtoMove = Application::find($application_id);
            $applicationtoMove->app_track .= "\nApplication moved from meeting ".$from_meeting->serial_no." to ".$to_meeting->serial_no;
            $old_statistic = Statistic::where('appl_id',$application_id)->where('meeting_id',$meeting_id)->first();
            $statistic = new Statistic();
            $statistic->appl_id = $application_id;
            $statistic->meeting_id = $to_meeting->id;
            $statistic->date = date('Y-m-d H:i:s');
            $statistic->save();
            $applicationtoMove->save();
        }
        return redirect()->back()->with('success','Successfully moved files to meeing '.$to_meeting->serial_number);
    }

    public function viewApplication($id)
    {
        $application = Application::find($id);
        if($application==null){
            return redirect()->route('admin-dashboard')->with('fail','Application does not exist!!');
        }
        $meetings = Meeting::all();
        $statistics = Statistic::where('appl_id',$id)->orderBy('id','desc')->get();
        return view('admin.meetings.view-application')->with('application',$application)->with('statistics',$statistics)->with('meetings',$meetings)->with('attachments',$attachments);
    }

}
