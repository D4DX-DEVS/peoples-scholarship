<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    //
    protected $table = 'unit';

    public $timestamps = false;

      public function area()
    {
      return $this->belongsTo('App\Area', 'area_id');
    }
     public function district()
    {
      return $this->belongsTo('App\District', 'district_id');
    }
     public function application()
    {
      return $this->hasMany('App\Application', 'unit_id');
    }

    /**
     * get Passed files number for unit
     * based on department,scheme
     */
    public function getPassedNumber($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $appCount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->count();
            }
            else{
                $appCount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        else{
            if($date1===null){
                $appCount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->count();
            }
            else{
                $appCount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        return $appCount;
    }

    // /**
    //  * get Delivered files number for unit
    //  * based on department,scheme
    //  */
    public function getDeliveredNumber($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $appCount = Application::where('unit_id',$this->id)->where('status',13)->where('cat_id',$category)->count();
            }
            else{
                $appCount = Application::where('unit_id',$this->id)->where('status',13)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        else{
            if($date1===null){
                $appCount = Application::where('unit_id',$this->id)->where('status',13)->count();
            }
            else{
                $appCount = Application::where('unit_id',$this->id)->where('status',13)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        return $appCount;
    }

    // /**
    //  * get Passed Amount for unit
    //  * based on department,scheme
    //  */
    public function getPassedAmount($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $totalAmount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        else{
            if($date1===null){
                $totalAmount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('unit_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        return $totalAmount;
    }

    // /**
    //  * get Delivered Amount for unit
    //  * based on department,scheme
    //  */
    public function getDeliveredAmount($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $totalAmount = Application::where('unit_id',$this->id)->where('status',13)->where('cat_id',$category)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('unit_id',$this->id)->where('status',13)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        else{
            if($date1===null){
                $totalAmount = Application::where('unit_id',$this->id)->where('status',13)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('unit_id',$this->id)->where('status',13)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        return $totalAmount;
    }


}
