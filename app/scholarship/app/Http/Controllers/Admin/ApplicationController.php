<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator, Redirect, Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Category;
use App\Course;
use App\Application;
use App\Person;
use App\Status;
use App\Yearsetting;
use App\District;
use App\Area;
use App\Unit;
use App\Areaauth;
use App\Meeting;
use App\Installment;
use App\Statistic;
use App\Support\ExportsTables;
use App\Support\FilterOptions;

use Route;

class ApplicationController extends Controller
{
    use ExportsTables;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth::user();
        view()->share('user', $this->user);
    }

    public function getListing($status=null)
    {
        $status_text = $status;
        $yearsetting = Yearsetting::orderBy('id', 'desc')->first();

        if ($status !== null) {
            $status_text = ucfirst($status);
        }

        // Rows are fetched by listingData() ten at a time; only the filter
        // options are needed up front. They cover every page of results, not
        // just the first, but are limited to values that actually appear on
        // an application — see FilterOptions.
        $scope = $this->listingScope($status);

        return view('admin.applications.listing')
            ->with('yearsetting', $yearsetting)
            ->with('status', $status_text)
            ->with('filterUnits', FilterOptions::forRelated('applications', 'unit_id', Unit::class, 'unit', $scope))
            ->with('filterAreas', FilterOptions::forRelated('applications', 'area_id', Area::class, 'area', $scope))
            ->with('filterDistricts', FilterOptions::forRelated('applications', 'district_id', District::class, 'district', $scope));
    }

    /**
     * Column layout of the applications listing, shared by the table
     * endpoint and the exports so they always agree.
     */
    private function listingColumns(): array
    {
        return [
            0 => ['path' => 'refno',             'title' => 'ID'],
            1 => ['path' => 'person.personname', 'title' => 'Name'],
            2 => ['path' => 'created_at',        'title' => 'Date'],
            3 => ['path' => 'person.mobile',     'title' => 'Phone'],
            4 => ['path' => 'unit.unit',         'title' => 'Unit'],
            5 => ['path' => 'area.area',         'title' => 'Area'],
            6 => ['path' => 'district.district', 'title' => 'District'],
            7 => ['path' => 'course.coursename', 'title' => 'Course'],
            8 => ['path' => null, 'searchable' => false, 'orderable' => false, 'title' => 'Action'],
        ];
    }

    /**
     * The base filter for this listing: all applications, or one status.
     */
    private function listingScope(?string $status): array
    {
        if ($status === null || $status === '') {
            return [];
        }

        $matched = Status::where('status_text', ucfirst($status))->first();

        // An unknown status must return nothing rather than everything.
        return ['status' => $matched ? (int) $matched->id : -1];
    }

    private function listingQuery(?string $status): \App\Support\DataTableQuery
    {
        $baseMatch = $this->listingScope($status);

        return new \App\Support\DataTableQuery(
            collection: 'applications',
            lookups: [
                'person'   => ['from' => 'persons',  'localField' => 'persid'],
                'unit'     => ['from' => 'unit',     'localField' => 'unit_id'],
                'area'     => ['from' => 'area',     'localField' => 'area_id'],
                'district' => ['from' => 'district', 'localField' => 'district_id'],
                'course'   => ['from' => 'courses',  'localField' => 'course_id'],
            ],
            columns: $this->listingColumns(),
            baseMatch: $baseMatch,
            filters: [
                'unit'     => 'unit.unit',
                'area'     => 'area.area',
                'district' => 'district.district',
            ],
        );
    }

    /**
     * Load the given ids as models, in that order, with the relations the
     * rows render eager loaded.
     */
    private function listingModels(array $ids)
    {
        $models = Application::with(['person', 'unit', 'district', 'area', 'course', 'getStatus'])
            ->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn ($id) => $models->get($id))->filter()->values();
    }

    /**
     * Rows for the applications table (DataTables server-side endpoint).
     */
    public function listingData(Request $request)
    {
        $page = $this->listingQuery($request->input('status'))->page($request);

        $data = $this->listingModels($page['ids'])->map(fn ($applicant) => [
            '<div><a href="'.route('view-application', ['id' => $applicant->id]).'" title="View Application">'
                .e($applicant->refno).'</a></div>',
            e(optional($applicant->person)->personname),
            e($applicant->created_at),
            e(optional($applicant->person)->mobile),
            e(optional($applicant->unit)->unit),
            e(optional($applicant->area)->area),
            e(optional($applicant->district)->district),
            e(optional($applicant->course)->coursename ?: $applicant->course_other),
            view('admin.applications._action', compact('applicant'))->render(),
        ]);

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $page['total'],
            'recordsFiltered' => $page['filtered'],
            'data'            => $data,
        ]);
    }

    /**
     * Print or download every row matching the current search and filters.
     *
     * The table only ever holds ten rows in the browser, so "Print all" and
     * the spreadsheet export cannot come from the page; they are rebuilt
     * here against the same filters the table is showing.
     */
    public function exportListing(Request $request)
    {
        // length -1 tells the query to skip the page limit.
        $request->merge(['start' => 0, 'length' => -1]);

        $rows = $this->listingModels($this->listingQuery($request->input('status'))->page($request)['ids'])
            ->map(fn ($a) => [
                $a->refno,
                optional($a->person)->personname,
                $a->created_at,
                optional($a->person)->mobile,
                optional($a->unit)->unit,
                optional($a->area)->area,
                optional($a->district)->district,
                optional($a->course)->coursename ?: $a->course_other,
            ]);

        $title = 'Applications'.($request->input('status') ? ' — '.ucfirst($request->input('status')) : '');

        return $this->exportResponse($request, $title, 'applications.csv', $this->listingColumns(), $rows);
    }

    public function getAppEdit($appli_id, $pers_id)
    {
        $application = Application::find($appli_id);
        $yearsetting = Yearsetting::where('id', $application->year_id);
        $personAppliCount=Application::where('persid',$pers_id)->count();
        $previousAppDet[0] =null;
        if($personAppliCount>1){
           $previousAppDet = Application::where('persid',$pers_id)->orderBy('created_at', 'desc')->offset(1)->limit(1)->get();      
        }
        $person = Person::find($pers_id);
        $category=$application->category->catname;
        $course=isset($application->course) ? $application->course->coursename : $application->course_other;
        $districts=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $meetings = Meeting::orderBy('id', 'desc')->get();
        $track_action= explode("\n", $application->app_track);
        $last_action="";
        $installments_due=0;
        if(count($track_action)>0) $last_action=end($track_action);
        if($application->getStatus->status_text == 'Granted')
        {
            $installments_due=$application->getCountInstallmentsDue();
        }
        if($application->getStatus->status_text != 'Completed')
        {
            return view('admin.applications.edit-application')->with('application', $application)
                                                              ->with('person', $person)->with('yearsetting', $yearsetting)
                                                              ->with('districts',$districts)->with('previousAppDet', $previousAppDet[0])
                                        ->with('category',$category)->with('course',$course)->with('meetings',$meetings)
                                        ->with('last_action',$last_action)->with('installments_due',$installments_due);
        }
        else{
            abort(403);
        }
    }

    public function postEditApplication(Request $request)
    {
        $person = Person::find($request->persid);
        $person->personname = $request->name;
        $person->gender = $request->gender;
        $person->age = $request->age;
        $person->dob = $request->dob;
        $person->address = $request->address;
        $person->pin = $request->pin;
        $person->email = $request->email;
        $person->district = $request->district;
        $person->area = $request->area;
        $person->unit = $request->unit;
        $person->guardian = $request->guardian;
        $person->phone2 = $request->phone;
        $application = Application::find($request->applid);
        $application->institution = $request->institution;
        $application->district_id = $request->district;
        $application->area_id = $request->area;
        $application->unit_id = $request->unit;
        if($person->save() && $application->save())
        {
            return redirect()->back()->with('success', 'Application successfully updated');
        }else{
            return redirect()->back()->with('fail', 'Something Wrong try again.');
        }
    }

    public function deleteApp($appli_id, $pers_id)
    {

        $application = Application::find($appli_id);
        $personAppliCount=Application::where('persid',$pers_id)->count();
        if (!$application) {       
                return redirect()->back()->with('fail', 'Application not deleted. Some error occured');          
        }
        if($personAppliCount==1){
            $person = Person::find($pers_id);
            $person->delete();          
        }
        $application->delete();
        return redirect()->back()->with('success', 'Application successfully deleted'); 
    }

    public function approveApplication(Request $request)
    {
        $application = Application::find($request->appli_id);
        $application->status = 2; //verified
        $application->app_track = (isset($request->status_reason)? $request->status_reason ."\n"."Approved on ".date('d-m-Y', strtotime("now"))  : "Approved on ".date('d-m-Y', strtotime("now")));
        $application->reason_status = (isset($request->status_reason)? $request->status_reason:'');        
        if($application->save())
        {
            return redirect()->route('get-applications-index')->with('success', 'You are Successfully approved Application of '.$request->appli_name);
        }else{
            return redirect()->route('get-applications-index')->with('fail', 'Something Wrong try again.');
        }
    }

    //awd application status changes
    public function appChangeStatus( $appli_id, $in_status){
        $new_status = Status::where('status_text', $in_status)->first();
        $application = Application::find($appli_id);
        $application->status = $new_status->id;
        if($application->save()){
                return true;
        }
        return false;
    }

    public function setAppStatus($appli_id, $status){
        if($this->appChangeStatus($appli_id, $status)){
            return redirect()->back()->with('success', 'Application successfully moved to '.$status);
        }else{
            return redirect()->back()->with('fail', 'Something Wrong try again.');
        }
    }

 
    //Granting
    public function postGrantApp(Request $request){
        $this->validate($request,['granted_amount'=>'required','noofinstallments'=>'required']);
       
        $application = Application::find($request->id);

        $application->status = 6 ; //granded
        $application->grant_status =6;

        $application->amount_granted =  (int) $request->granted_amount;
        $application->granted_date =  date('Y-m-d H:i:s');
        $application->app_track = $application->app_track ."\n". "Granted on ".date('d-m-Y', strtotime("now"));
        $application->reason_status = "Granted on ".date('d-m-Y', strtotime("now"));
        $numInstallments=(int) $request->noofinstallments;
        $application->no_of_installments = $numInstallments;
        $toDate = date('Y-m-d H:i:s',strtotime("+1 months"));
        if ($application->save() ) {
            if($numInstallments >= 1 )
            {
                for ($i=0; $i < $request->noofinstallments; $i++) 
                { 
                    $installment = new Installment; 
                    $installment->appl_id = $application->id;
                    $installment->installment_number = $i+1;
                    $installment->amount = (int) $application->amount_granted / $numInstallments;
                    if($i==0)
                    {
                        $installment->status='2';
                        $installment->due_date=$toDate;
                    }
                    else
                    {
                        $installment->due_date=date('Y-m-d H:i:s',strtotime("+2 months"));
                        if($i>=2) $installment->due_date=date('Y-m-d H:i:s',strtotime("+3 months"));
                        $installment->status = '1';
                    }
                    $installment->save();
                }
                if($numInstallments>=1)
                {
                    return redirect()->route('edit-installments',['id'=>$application->id]);
                }
            }
            return redirect()->back()->with('success', 'Application successfully Granted');
        }else{
            return redirect()->back()->with('fail', 'Something Wrong try again.');
        }
    }


    // Rejection
    public function postRejectApp(Request $request){
            $this->validate($request,['reason_rejection'=>'required']);

            $application = Application::find($request->id);

            $application->status = 5 ; //rejected
            $application->amount_granted = 0;
            $application->grant_status =0;
            $application->app_track = $application->app_track ."\n".$request->reason_rejection. " on ".date('d-m-Y', strtotime("now"));
            $application->reason_status = $request->reason_rejection;
            if ($application->save() ) {
                    return redirect()->back()->with('success', 'Application successfully rejected');
                }else{
                    return redirect()->back()->with('fail', 'Something Wrong try again.');
            }
    }

    public function printApplication($id)
    {
        $application = Application::find($id);
        if($application==null){
            return redirect()->route('admin-dashboard')->with('fail','Application does not exist!!');
        }
        $yearsetting=Yearsetting::find($application->year_id);
        $district=District::orderBy('id', 'asc')->pluck('district','id')->toArray();
        $refno=$application->refno;
        $person=Person::find($application->persid);
        $category=$application->category->catname;
        $course=isset($application->course) ? $application->course->coursename : $application->course_other;
        $district=$application->district->district;      
        $area=$application->area;       
        $unit=$application->unit;
        $arealeaders=Areaauth::where('areaid', '=', $area->id )->first();
        return view('application-print',compact('person','application'))->with('yearsetting',$yearsetting)
                                        ->with('district',$district)->with('area',$area)->with('unit',$unit)->with('arealeaders',$arealeaders)
                                        ->with('category',$category)->with('course',$course)->with('refno',$refno);
    }
        /**
     * get application details
     */
    public function viewApplication($id)
    {
        $application = Application::find($id);
        if($application==null){
            return redirect()->route('admin-dashboard')->with('fail','Application does not exist!!');
        }
        $meetings = Meeting::all();
        $statistics = Statistic::where('appl_id',$id)->orderBy('id','desc')->get();
        return view('admin.view-application')->with('application',$application)->with('statistics',$statistics)->with('meetings',$meetings);
    }


    /**
     * search applications in registry
     */
    public function applicationSearch(Request $request){
        if($request->app_number){
            $applications = Application::where('refno',$request->app_number)->get();
            if(sizeof($applications)===0){
                return redirect()->back()->with('fail','No application found with given application number');
            }
            else if(sizeof($applications)===1){
                return redirect()->route('view-application',['id'=> $applications[0]->id]);
            }
            else{
                return view('admin.applications.search-results')->with('applications',$applications)->with('status',"all");
            }
        }
        if($request->app_name){
            $applications = Application::where('applicant_name','like','%'.$request->app_name.'%')->get();
            if(sizeof($applications)===0){
                return redirect()->back()->with('fail','No application found with given applicant name');
            }
            else if(sizeof($applications)===1){
                return redirect()->route('view-application',['id'=> $applications[0]->id]);
            }
            else{
                return view('admin.applications.search-results')->with('applications',$applications)->with('status',"all");
            }
        }
        else{
            if($request->unit>0){
                $applications = Application::where('unit_id',$request->unit)->get();
            }
            else if($request->area>0){
                $applications = Application::where('area_id',$request->area)->get();
            }
            else if($request->district>0){
                $applications = Application::where('district_id',$request->district)->get();           
            }
            else{
                return redirect()->back()->with("fail","Enter at least one search parameter");
            }
        }

       return view('admin.applications.search-results')->with('applications',$applications)->with('status',"all");
    }

        /**
     * cancel the grant status for a application - application changed to custom status
     */
    public function cancelGrant($id){
        $application = Application::find($id);
        $installments = $application->installments;
        if($application->delivered_date!==null){
            return redirect()->back()->with('fail','Amount delivered for this application. Cannot cancel grant.');
        }

        foreach ($installments as $key => $installment) {
            $installments[$key]->delete();
        }

        $application->status=10;
        $application->reason_status = "application grant cancelled successfully";
        $application->app_track .= '\n Grant cancelled on '.date('Y-m-d H:i:s');
        $application->grant_status = 0;
        $application->no_of_installments = 0;
        $application->amount_granted = 0.00;
        $application->granted_date = null;
        if($application->save()){
            return redirect()->back()->with('success',"Successfully cancelled grant for this application.");
        }
        else{
            return redirect()->back()->with('fail',"Failed to cancel the grant.");
        }
    }

        /**
     * get meeting sheet for a application
     */
    public function getMeetingSheet($id){
        $application = Application::find($id);
        // The old whereHas compared the related application's status against
        // $application->status — the same row on both sides, so it never
        // filtered anything out.
        $current_statistic = Statistic::where('appl_id',$application->id)
        ->with('Application')
        ->orderBy('id','desc')->first();
        return view('admin.meeting-sheet')->with('application',$application)->with('current_statistic',$current_statistic);
    }

        /**
     * Get File statistics
     */
    public function getStatistics(){
        $categories=Category::orderBy('id', 'asc')->get();
        $districts = District::all();
        $x_axis = "district";
        $y_axis = "category";
        return view('admin.statistics')->with('districts',$districts)->with('categories',$categories)
                                       ->with('x_axis',$x_axis)->with('y_axis',$y_axis);
    }
    /**
     * post Statistics
     */
    public function postStatistics(Request $request){
        if($request->date){
            $date = explode(' - ', $request->date);
            $dateFrom = $date[0];
            $dateTo = $date[1];

            $Datef= strtotime( $dateFrom );
            $Datet= strtotime( $dateTo );
            $date1 = date( 'Y-m-d', $Datef);
            $date2 = date( 'Y-m-d', $Datet);
        }
        else{
            $date1=$date2=null;
        }
        $district = $request->district;
        $area = $request->area;
        $category = $request->category;
        $categories=Category::orderBy('id', 'asc')->get();
        $districts = District::all();
        $areas=array();
        $units=array();
        if($district[0]==""){
            $x_axis = "district";
            $districts = District::all();
        }
        else{
            //district selected
            if($area ==""){
                $x_axis = "area";
                $areas = Area::whereIn('district_id',$district)->get();
            }
            else{ 
                //area selected
                $x_axis = "unit";
                $units = Unit::whereIn('area_id',$area)->get();
            }
        }
        $y_axis = "category";
 
        return view('admin.statistics')->with('districts',$districts)->with('areas',$areas)->with('units',$units)
                                       ->with('date1',$date1)->with('date2',$date2)
                                       ->with('district_id',$district)->with('area_id',$area)
                                       ->with('cat_id',$category)->with('x_axis',$x_axis)->with('y_axis',$y_axis)
                                       ->with('categories',$categories);        
    }

}
