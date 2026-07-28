<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator, Input, Redirect, Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use Session;
use Route;
use Hash;

use App\Area;
use App\Areaauth;
use App\Unit;
use App\District;
use App\Person;
use Mail;

use App\Application;
use App\Yearsetting;

class AdminController extends Controller
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
   //     $this->middleware('admin');
        $this->user = Auth::user();
        view()->share('user', $this->user);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $page_title = "Dashboard"; 
        $districts = District::all();       
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        
        $applic_all=Application::getAppCount(0,$yearsetting->id);
        $granted_all=Application::getAppCount(6,$yearsetting->id);
        $approved_all=Application::getAppCount(2,$yearsetting->id);
        $pending_all=Application::getAppCount(7,$yearsetting->id);
        $meeting_all=Application::getAppCount(4,$yearsetting->id);
        $waiting_all=Application::getAppCount(1,$yearsetting->id);
        $rejected_all=Application::getAppCount(5,$yearsetting->id);

        $new_applications= Application::where('status',1)->orderBy('id','desc')->take(5)->get();
        return view('admin.dashboard')->with('page_title', $page_title )->with('districts',$districts)->with('new_applications',$new_applications)
                                      ->with('yearsetting', $yearsetting)->with('applic_all', $applic_all)->with('granted_all', $granted_all)->with('approved_all', $approved_all)
                                      ->with('pending_all', $pending_all)->with('meeting_all', $meeting_all)->with('waiting_all', $waiting_all)->with('rejected_all', $rejected_all)  ; 
    }
  
    public function getAreaResults(){
        $districts=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $area = Area::orderBy('district_id', 'asc')->get();
        return view('admin.area')->with('results',$area)->with('districts',$districts);
    }

    public function getUnitResults(){
        $districts=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $unit = Unit::all();
        return view('admin.unit')->with('results',$unit)->with('districts',$districts);

    }

    public function getAreaAdmin($areaid,$distid){
        $curArea = Area::find($areaid) ;
        $areaAuths = Areaauth::where('areaid',$areaid)->first() ;
        return view('admin.area-admin')->with('result',$curArea)->with('leaders', $areaAuths);
    }

    public function addAreaAdmin(Request $request){
        if($this->validate($request,['areaname' => 'required','area_cordinator' => 'required',
                                  'cordinator_mobile' => 'required','area_president' => 'required','president_mobile' => 'required'])) 
          {
           
            $curarea =  Area::find($request->areaid);
            if($request->areaname != $curarea->area )
            {
                $curarea->area=$request->areaname;
                $curarea->save();
            }                                
            $areaauth = Areaauth::where('areaid',$request->areaid)->first() ? Areaauth::where('areaid',$request->areaid)->first() : new Areaauth ;
            $areaauth->area_cordinator = $request->area_cordinator;
            $areaauth->cordinator_mobile = $request->cordinator_mobile;

            $areaauth->area_president = $request->area_president;
            $areaauth->president_mobile = $request->president_mobile;
 
            $areaauth->areaid = $request->areaid;
            $areaauth->districtid = $request->districtid;
 
                                                 
          /*new end*/
            if ($areaauth->save()) {
                return redirect()->back()->with('success','Area details successfully added');
            }
         }
        return redirect()->back()->with('fail','Something went wrong. Please try again');

    }
    public function addArea(Request $request){
        $this->validate($request,['area' => 'required']);
        $area = new Area;
        $area->area = $request->area;
        $area->district_id = $request->district;
        if ($area->save()) {
            return redirect()->back()->with('success','Area successfully added, please assign President & coordinator for this area ');
        }

        return redirect()->back()->with('fail','Something went wrong. Please try again');

    }

    public function addUnit(Request $request){
       if( $this->validate($request,['area' => 'required','unit'=>'required','district'=>'required',
                                     'name' => 'required','number' => 'required']))
        {
            $unit = new Unit;
            $unit->area_id = $request->area;
            $unit->unit = $request->unit;
            $unit->district_id = $request->district;
            $unit->presiname = $request->name;
            $unit->presinumber = $request->number;
        /* new*/
            if ($unit->save()) {
            $unitt= Unit::where('unit',$request->unit)->first();
            
            /* new end*/
            
                return redirect()->back()->with('success','Unit successfully added');
            }
       }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
    }
  
    public function removeArea($id){
        $area = Area::find($id);
        $check=Areaauth::where('areaid',$area->id)->first();
        
        if ($area->delete()) {
            if(count($check)>0)
            {
                $check->delete();
            }
            return redirect()->back()->with('success','Area successfully deleted, including leaders info for this area');
            
        }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
        
    }

    public function getEditUnit($id){
        $districts=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $unit = Unit::find($id);
        return view('admin.edit-unit')->with('result',$unit)->with('districts',$districts);
    }
    public function postEditUnit(Request $request){
        if( $this->validate($request,['id'=>'required','unit'=>'required','area' => 'required','district'=>'required',
        'name' => 'required','number' => 'required']))

       {
        $unit = Unit::find($request->id);
       
        if ($request->unit !== $unit->unit OR $request->district !== $unit->district_id OR $request->area !== $unit->area_id ) {
            $unit->district_id = $request->district;
            $unit->area_id = $request->area;

            $persons = Person::where('unit','=',$unit->id);
            $persons->update(array('area'=>$request->area, 'district'=>$request->district));
        }

        $unit->unit = $request->unit;
        $unit->presiname = $request->name; /*president name*/
        $unit->presinumber = $request->number; /*president mobile*/


        if ($unit->save()) {
            return redirect()->back()->with('success','Unit successfully updated');
        }
       }
       return redirect()->back()->with('fail','Something went wrong. Please try again');
    }

    public function removeUnit($id){
        $unit = Unit::find($id);
        if ($unit->delete()) {
            return redirect()->back()->with('success','Unit successfully deleted, including leader details for this unit');
            
        }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
    }

      /////////// change Password
  public function viewChangePassword(){
    return view('admin.changepassword');
    }
    
  public function changePassword(Request $request){

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
        // The passwords matches
        return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
        //Current password and new password are same
        return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $validatedData = $request->validate([
        'current-password' => 'required',
        'new-password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success","Password changed successfully !");       
  }
}
