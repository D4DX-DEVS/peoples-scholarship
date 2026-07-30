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
use App\Support\ExportsTables;
use App\Support\FilterOptions;

class GrantedController extends Controller
{
    use ExportsTables;

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
         // Rows come from grantedData() ten at a time; only the filter
         // options are needed up front, and they come from the lookup tables
         // so every value is offered rather than only those on page one.
         // Only granted rows are listed, so the filters offer only values
         // that appear on one.
         $scope = ['grant_status' => ['$gt' => 0]];

         return view('admin.granted.granted-list')
             ->with('grantTypes', [6 => 'Documents Waiting', 7 => 'Forwaded to Accounts', 8 => 'Installment Due', 9 => 'Completed'])
             ->with('filterCategories', FilterOptions::forRelated('applications', 'cat_id', \App\Category::class, 'catname', $scope))
             ->with('filterUnits', FilterOptions::forRelated('applications', 'unit_id', \App\Unit::class, 'unit', $scope))
             ->with('filterAreas', FilterOptions::forRelated('applications', 'area_id', \App\Area::class, 'area', $scope))
             ->with('filterDistricts', FilterOptions::forRelated('applications', 'district_id', \App\District::class, 'district', $scope));
    }

    /**
     * Column layout of the granted list, shared with its exports.
     */
    private function grantedColumns(): array
    {
        return [
            0  => ['path' => 'id',                 'title' => 'ID'],
            1  => ['path' => 'refno',              'title' => 'Appl. ID'],
            2  => ['path' => 'person.personname',  'title' => 'Applicant Name'],
            3  => ['path' => 'person.address',     'title' => 'Address'],
            4  => ['path' => 'grant_status',       'title' => 'Status'],
            5  => ['path' => 'granted_date',       'title' => 'Granted Date'],
            6  => ['path' => 'category.catname',   'title' => 'Category'],
            7  => ['path' => 'unit.unit',          'title' => 'Unit'],
            8  => ['path' => 'area.area',          'title' => 'Area'],
            9  => ['path' => 'district.district',  'title' => 'District'],
            10 => ['path' => null, 'searchable' => false, 'orderable' => false, 'title' => 'Actions'],
        ];
    }

    private function grantedQuery(): \App\Support\DataTableQuery
    {
        return new \App\Support\DataTableQuery(
            collection: 'applications',
            lookups: [
                'person'   => ['from' => 'persons',    'localField' => 'persid'],
                'unit'     => ['from' => 'unit',       'localField' => 'unit_id'],
                'area'     => ['from' => 'area',       'localField' => 'area_id'],
                'district' => ['from' => 'district',   'localField' => 'district_id'],
                'category' => ['from' => 'categories', 'localField' => 'cat_id'],
            ],
            columns: $this->grantedColumns(),
            baseMatch: ['grant_status' => ['$gt' => 0]],
            filters: [
                'district'     => 'district.district',
                'area'         => 'area.area',
                'unit'         => 'unit.unit',
                'category'     => 'category.catname',
                'grant_status' => ['path' => 'grant_status', 'numeric' => true],
            ],
        );
    }

    /**
     * The four grant states, as the list has always labelled them.
     */
    private function grantStatusText($status): string
    {
        return match ((int) $status) {
            6 => 'Documents Waiting',
            7 => 'Forwaded to Accounts',
            8 => 'Installment Due',
            9 => 'Completed',
            default => 'Undefined',
        };
    }

    private function grantedModels(array $ids)
    {
        $models = Application::with(['person', 'unit', 'district', 'area', 'category'])
            ->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn ($id) => $models->get($id))->filter()->values();
    }

    /**
     * Rows for the granted list.
     */
    public function grantedData(Request $request)
    {
        $page = $this->grantedQuery()->page($request);

        $data = $this->grantedModels($page['ids'])->map(fn ($application) => [
            e($application->id),
            e($application->refno),
            e(optional($application->person)->personname),
            e(optional($application->person)->address),
            e($this->grantStatusText($application->grant_status)),
            $application->status !== 1 && $application->granted_date
                ? e(date('d M Y', strtotime($application->granted_date))) : '',
            e(optional($application->category)->catname),
            e(optional($application->unit)->unit),
            e(optional($application->area)->area),
            e(optional($application->district)->district),
            '<div class="btn-group">'
            .'<a href="'.route('admin-app-edit', ['appli_id' => $application->id, 'pers_id' => $application->persid])
                .'" target="_blank" class="noprint"> <button title="Edit" class="btn btn-warning btn-xs"> <i class="fa fa-pencil"></i> </button> </a>'
            .'<a href="'.route('edit-installments', ['id' => $application->id])
                .'" target="_blank" class="noprint"> <button title="Installments" class="btn btn-info btn-xs"> <i class="fa fa-money"></i> </button> </a>'
            .'</div>',
        ]);

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $page['total'],
            'recordsFiltered' => $page['filtered'],
            'data'            => $data,
        ]);
    }

    /**
     * Print or download every granted row matching the current filters.
     */
    public function grantedExport(Request $request)
    {
        $request->merge(['start' => 0, 'length' => -1]);

        $rows = $this->grantedModels($this->grantedQuery()->page($request)['ids'])
            ->map(fn ($a) => [
                $a->id,
                $a->refno,
                optional($a->person)->personname,
                optional($a->person)->address,
                $this->grantStatusText($a->grant_status),
                $a->status !== 1 && $a->granted_date ? date('d M Y', strtotime($a->granted_date)) : '',
                optional($a->category)->catname,
                optional($a->unit)->unit,
                optional($a->area)->area,
                optional($a->district)->district,
            ]);

        return $this->exportResponse($request, 'Granted applications', 'granted.csv', $this->grantedColumns(), $rows);
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
       // Rows come from duesData() ten at a time.
       return view('admin.granted.installments-due');
    }

    /**
     * Column layout of the dues list, shared with its exports.
     */
    private function duesColumns(): array
    {
        return [
            0 => ['path' => 'application.refno',          'title' => 'Application number'],
            1 => ['path' => 'application.applicant_name', 'title' => 'Name'],
            2 => ['path' => 'installment_number',         'title' => 'Installment number'],
            3 => ['path' => 'due_date',                   'title' => 'Due date'],
            4 => ['path' => 'status',                     'title' => 'Status'],
            5 => ['path' => 'due_date', 'searchable' => false, 'title' => ''],
        ];
    }

    private function duesQuery(): \App\Support\DataTableQuery
    {
        // Dates are stored in 'Y-m-d H:i:s', which compares chronologically
        // as a plain string, so the same bound the page always used works.
        $toDate = date('Y-m-d H:i:s', strtotime('+1 months'));

        return new \App\Support\DataTableQuery(
            collection: 'installments',
            lookups: [
                'application' => ['from' => 'applications', 'localField' => 'appl_id'],
            ],
            columns: $this->duesColumns(),
            baseMatch: [
                'due_date' => ['$ne' => null, '$lt' => $toDate],
                'status'   => ['$lt' => 4],
            ],
        );
    }

    private function duesModels(array $ids)
    {
        $models = Installment::with('application')->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn ($id) => $models->get($id))->filter()->values();
    }

    private function dueDateText($dueDate): string
    {
        return ($dueDate === null || $dueDate === '0000-00-00 00:00:00')
            ? 'Not Scheduled'
            : date('d-M-Y', strtotime($dueDate));
    }

    /**
     * Rows for the installments-due list.
     */
    public function duesData(Request $request)
    {
        $page = $this->duesQuery()->page($request);

        $data = $this->duesModels($page['ids'])->map(function ($installment) {
            $application = $installment->application;
            $state       = $installment->getStatus($installment->status);

            return [
                $application
                    ? '<a href="'.route('view-application', ['id' => $application->id]).'" target="_blank">'.e($application->refno).'</a>'
                    : '',
                e(optional($application)->applicant_name),
                $application
                    ? '<a href="'.route('edit-installments', ['id' => $application->id]).'" target="_blank">'.e($installment->installment_number).'</a>'
                    : e($installment->installment_number),
                e($this->dueDateText($installment->due_date)),
                '<span class="label status bg-'.e($state['bgColour']).'">'.e($state['statusText']).'</span>',
                e($installment->due_date),
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw'),
            'recordsTotal'    => $page['total'],
            'recordsFiltered' => $page['filtered'],
            'data'            => $data,
        ]);
    }

    /**
     * Print or download every due installment matching the current search.
     */
    public function duesExport(Request $request)
    {
        $request->merge(['start' => 0, 'length' => -1]);

        $rows = $this->duesModels($this->duesQuery()->page($request)['ids'])
            ->map(fn ($i) => [
                optional($i->application)->refno,
                optional($i->application)->applicant_name,
                $i->installment_number,
                $this->dueDateText($i->due_date),
                $i->getStatus($i->status)['statusText'],
                $i->due_date,
            ]);

        return $this->exportResponse($request, 'Installments due', 'installments-due.csv', $this->duesColumns(), $rows);
    }
}
