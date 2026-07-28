<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;

class AjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function postUnit(Request $request)
    {   
        $area = DB::table('area')->where('district', $request->district)->lists('area');
        return $area;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function postArea(Request $request)
    {   
        $id=Input::get('area'); 
        $unit = DB::table('unit')->where('area_id', $id)->orderBy('unit', 'asc')->pluck('unit','id')->toArray();
        return $unit;
    }

    public function getCategory()
    {
        $categories=DB::table('categories')->lists('catname','id');
        return $categories;
        
    }

    
    public function postCategory()
    {   $id=Input::get('category'); 
        $courses =DB::table('courses')->where([
            ['cat_id', '=', $id ],
            ['course_enabled', '=', '1']
         ])->pluck('coursename','id')->toArray();
       return $courses;

    }

     public function postDistrict()
    {   $id=Input::get('district'); 
        $area =DB::table('area')->where('district_id', $id)->orderBy('area', 'asc')->pluck('area','id')->toArray();
       return $area;

    }
     public function postDistrictAll()
    {   $id=Input::get('district'); 

        $area =DB::table('area')->whereIn('district_id', $id)->lists('area','id');/* DB::table('schemes')->where('department_id', $request->department)->lists('name');*/
       return $area;

    }

      public function postAreaId()
    {   $id=Input::get('area'); 
        $unit = DB::table('unit')->where('area_id', $id)->lists('unit','id');/* DB::table('schemes')->where('department_id', $request->department)->lists('name');*/
        return $unit;
    }
    
    

}
