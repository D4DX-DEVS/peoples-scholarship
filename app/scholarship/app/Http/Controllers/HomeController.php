<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Form;
use App\Yearsetting;
use App\District;
use App\Areaauth;
use App\Category;
use App\Application;
use App\Person;
use App\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Storage;
use Redirect, Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        return view('welcome')->with('yearsetting',$yearsetting);
    }

    public function addApplication()
    {
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        if ($yearsetting->entryenable==0 || $yearsetting->applimit <= $yearsetting->getApplicationsCount() )
        {
            return Redirect::back()->with('fail','Application Entry is currently blocked!!');
        }
        session()->forget('person_id');
        session()->forget('appli_id');
        if($yearsetting->entryenable && $yearsetting->applimit > $yearsetting->getApplicationsCount())
            return view('application-start')->with('yearsetting',$yearsetting);
        else
            return view('welcome')->with('yearsetting',$yearsetting);
    }

    public function checkNewUser(Request $request)
    {
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        if ($yearsetting->entryenable==0 || $yearsetting->applimit <= $yearsetting->getApplicationsCount() )
        {
            return Redirect::back()->with('fail','Application Entry is currently blocked!!');
        }
        $yearid = $yearsetting->id;
        $messages=array('mobile.unique_mobile_and_year'=>'This Mobile No. Already Exist ! Check Mobile No.',
                        'aadhar.unique_aadhar_and_year'=>'This Aadhar No. Already Exist ! Check Aadhar No.');
        $rules=array('mobile' => "required|numeric|uniqueMobileAndYear:{$yearid}",
        'aadhar'=>"required|numeric|uniqueAadharAndYear:{$yearid}",
        'name'=>'required');

        $validator=Validator::make($request->all(),$rules,$messages);
       if($validator->fails())
           {
               return Redirect::back()->withErrors($validator)->withInput($request->All());
           }
           else
           {
                $person=Person::where('aadhar', $request->aadhar)->orWhere('mobile', $request->mobile)->first();
                if(!is_null($person))
                {
                    $time1 = time(); // now
                    $time2 = strtotime($person->applied_date);

                    $days = floor(($time1 - $time2) / (60*60*24) );

                    if(intval($days) < 365)
                    {
                        return Redirect::back()->with('fail','Disqualified! You already applied within one year!!');
                    }
                }
                if ( is_null($person) )
                {
                    $person= new Person;
                    $person->personname=$request->name;
                    $person->mobile = $request->mobile;
                    $person->aadhar = $request->aadhar;
                    $person->save();
                }
                $application=new Application;
                $application->year_id = $yearid;
                $application->applicant_name = $request->name;
                $application->persid = $person->id;
                $application->status = 1; // Registered
                session(['person_id' => $person->id]);
                if($application->save())
                {
                    session(['appli_id' => $application->id]);
                    return redirect()->route('get-application-final');
                }
                else
                {
                    return Redirect::back()->with('fail','Something went wrong Try Again');
                }
            }

    }

    public function getPersonApplication()
    {
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        if ($yearsetting->entryenable==0 || $yearsetting->applimit < $yearsetting->getApplicationsCount() )
        {
            return Redirect::back()->with('fail','Application Entry is currently blocked!!');
        }
        $categories=Category::pluck('catname', 'id')->toArray();
        $districts=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $refno = Application::makeRefno($yearsetting, session('appli_id'));
        $person=Person::find(session('person_id'));
        return view('application-final',compact('person'))->with('yearsetting',$yearsetting)->with('categories',$categories)
                                        ->with('districts',$districts)
                                        ->with('applid',session('appli_id'))
                                        ->with('persid',session('person_id'))
                                        ->with('refno',$refno);

    }

    public function savePersonApplication(PersonRequest $request)
    {
       $validator=Validator::make($request->all(),$request->rules(),$request->messages());
       if($validator->fails())
           {
               return Redirect::back()->withErrors($validator)->withInput($request->All());
           }
           else
           {
        $person = Person::find($request->persid);
        $person->personname = $request->name;
        $person->gender = $request->gender;
        $person->age = $request->age;
        $person->dob = $request->dob;
        $person->address = $request->address;
        $person->pin = $request->pin;
        $person->mobile = $request->mobile;
        $person->district = $request->district;
        $person->area = $request->area;
        $person->unit = $request->unit;
        $person->aadhar = $request->aadhar;
        $person->email = $request->email;
        $person->guardian = $request->guardian;
        $person->phone2 = $request->phone;
        $person->applied_date = date('Y-m-d H:i:s');
        if(!empty($request->file('profile_pic')))
        {
            if($person->photourl!='' && $person->photourl!=null)
            {
                Storage::disk('spaces')->delete("uploads/{$person->photourl}"); // remove previous image
            }
            $profilePic=$request->file('profile_pic');
            $ext = pathinfo($profilePic->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename = $request->refno . ".".$ext;
            Storage::disk('spaces')->putFileAs('uploads', $profilePic, $filename, 'public');
            $person->photourl=$filename;
        }

        $application = Application::find(session('appli_id'));
        $application->refno = $request->refno;
        $application->cat_id = $request->category;
        $application->course_id = $request->course;
        if($request->course==10000) $application->course_other = $request->course_other;
        $application->institution = $request->institution;
        $application->district_id = $request->district;
        $application->area_id = $request->area;
        $application->unit_id = $request->unit;

        if($person->save() && $application->save())
        {
            session(['person_id' => $person->id]);
            session(['appli_id' => $application->id]);
            return redirect()->route('get-application-print');

        }
      }
    }

    public function getApplicationPrint()
    {
        $yearsetting=Yearsetting::orderBy('id', 'desc')->first();
        $district=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $refno = Application::makeRefno($yearsetting, session('appli_id'));
        $person=Person::find(session('person_id'));
        $application = Application::find(session('appli_id'));
        $category=$application->category->catname;
        $course=isset($application->course) ? $application->course->coursename : $application->course_other;
        $district=$application->district->district;
        $area=$application->area;
        $unit=$application->unit;
        $arealeaders=Areaauth::where('areaid', '=', $area->id )->first();
        session()->flush();
        return view('application-print',compact('person','application'))->with('yearsetting',$yearsetting)
                                        ->with('district',$district)->with('area',$area)->with('unit',$unit)->with('arealeaders',$arealeaders)
                                        ->with('category',$category)->with('course',$course)->with('refno',$refno);

    }

}
