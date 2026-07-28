<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator, Input, Redirect, Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Category;
use App\Course;
use App\Yearsetting;
use DB;
use Session;
use Route;

class SettingsController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
  
    /**
     * setting page
     */
    public function getSettings(){
        $categories=Category::orderBy('id', 'asc')->get();
        $courses=Course::orderBy('cat_id', 'asc')->orderBy('id', 'asc')->get();
        return view('admin.course-settings')->with('categories',$categories)->with('courses',$courses);
    }

    /**
     * add new Category
     */
    public function addNewCategory(Request $request){
        $this->validate($request,['catname'=>'required|unique:categories']);
        $category = new Category();
        $category->catname = $request->catname;
        if($category->save()){
            return redirect()->back()->with("success",'Successfully added Category '.$category->name);
        }
        else{
            return redirect()->back()->with('fail','Failed to save changes');
        }
    }

    /**
     * Add new Course
     */
    public function addNewCourse(Request $request){
        $this->validate($request,['category'=>'required','course_name'=>'required']);
        $course_exists = Course::where('cat_id',$request->category)->where('coursename',$request->course_name)->first();
        if($course_exists !== null){
            return redirect()->back()->with('fail',$request->course_name.' already exists in the selected Category');
        }
        $course = new Course();
        $course->coursename = $request->course_name;
        $course->cat_id = $request->category;

        if($course->save()){
            return redirect()->back()->with("success",'Successfully added Course '.$course->coursename);
        }
        else{
            return redirect()->back()->with('fail','Failed to save changes');
        }
    }

    public function getCourseAdmin($courseid){
        $curCourse = Course::find($courseid) ;
        return view('admin.course-admin')->with('result',$curCourse);
    }

    public function addCourseAdmin(Request $request){
        if($this->validate($request,['coursename' => 'required','catname' => 'required'])) 
          {         
            $curCourse =  Course::find($request->courseid);
            $curCourse->coursename=$request->coursename;
            $curCourse->course_enabled = $request->course_enabled;
            $curCourse->save();
            $curCategory =  Category::find($request->catid);
            if($request->catname != $curCategory->catname )
            {
                $curCategory->catname=$request->catname;
                $curCategory->save();
            }                                                                                
            return redirect()->route('admin-settings')->with('success','Course details successfully updated');
         }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
    }

    public function removeCourse($id){
        $course = Course::find($id);       
        if ($course->delete()) {
            return redirect()->route('admin-settings')->with('success','course successfully deleted');           
        }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
        
    }

    public function removeCategory($id){
        $category = Category::find($id);              
        if ($category->delete()) {
            DB::delete('DELETE FROM courses WHERE cat_id = '.$id);
            return redirect()->route('admin-settings')->with('success','Category and related Courses successfully deleted');           
        }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
        
    }

    public function getYears(){
        $yearSets=Yearsetting::orderBy('id', 'desc')->get();
        $curYear=$yearSets[0];
        return view('admin.year-settings')->with('yearSets',$yearSets)->with('curYear',$curYear);
    }

    public function addEditYears(Request $request){
        if($this->validate($request,['year_period' => 'required','appli_limit' => 'required'])) 
          {          
            if($request->id==''){
                $curyear =  new Yearsetting;
            }
            else {
                $curyear =  Yearsetting::find($request->id);
            }

            $curyear->yearperiod=$request->year_period;
            $curyear->applimit=$request->appli_limit;
            $curyear->entryenable=$request->entry_enable;
                                                
          /*new end*/
            if ($curyear->save()) {
                return redirect()->back()->with('success','Year details successfully updated');
            }
         }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
    }

    public function removeYear($id){
        $cyear = Yearsetting::find($id);              
        if ($cyear->delete()) {
            DB::delete('DELETE FROM applications WHERE year_id = '.$id);
            return redirect()->route('admin-year-settings')->with('success','Year and related Applications successfully deleted');           
        }
        return redirect()->back()->with('fail','Something went wrong. Please try again');
        
    }
}
