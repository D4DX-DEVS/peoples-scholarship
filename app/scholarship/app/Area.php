<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{

    protected $table = 'area';
    public $timestamps = false;

     public function unit()
    {
      return $this->hasMany('App\Unit', 'area_id');
    }

      public function district()
    {
      return $this->belongsTo('App\District', 'district_id');
    }

    public function areaauth()
    {
      return $this->hasOne('App\Areaauth','areaid');
    }
    /**
     * get Passed files number for area
     * based on category
     */
    public function getPassedNumber($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $appCount = Application::where('area_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->count();
            }
            else{
                $appCount = Application::where('area_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        else{
            if($date1===null){
                $appCount = Application::where('area_id',$this->id)->where('grant_status','>',0)->count();
            }
            else{
                $appCount = Application::where('area_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        return $appCount;
    }

    // /**
    //  * get Delivered files number for area
    //  * based on category,scheme
    //  */
    public function getDeliveredNumber($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $appCount = Application::where('area_id',$this->id)->where('status',8)->where('cat_id',$category)->count();
            }
            else{
                $appCount = Application::where('area_id',$this->id)->where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        else{
            if($date1===null){
                $appCount = Application::where('area_id',$this->id)->where('status',8)->count();
            }
            else{
                $appCount = Application::where('area_id',$this->id)->where('status',8)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->count();
            }
        }
        return $appCount;
    }

    // /**
    //  * get Passed Amount for area
    //  * based on category,scheme
    //  */
    public function getPassedAmount($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $totalAmount = Application::where('area_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('area_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        else{
            if($date1===null){
                $totalAmount = Application::where('area_id',$this->id)->where('grant_status','>',0)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('area_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        return $totalAmount;
    }

    // /**
    //  * get Delivered Amount for area
    //  * based on category,scheme
    //  */
    public function getDeliveredAmount($category="all",$date1=null,$date2=null){
        if($category!=="all"){
            if($date1===null){
                $totalAmount = Application::where('area_id',$this->id)->where('status',8)->where('cat_id',$category)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('area_id',$this->id)->where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        else{
            if($date1===null){
                $totalAmount = Application::where('area_id',$this->id)->where('status',8)->sum('amount_granted');
            }
            else{
                $totalAmount = Application::where('area_id',$this->id)->where('status',8)->whereBetween('granted_date', [$date1.'%', $date2.'%'])->sum('amount_granted');
            }
        }
        return $totalAmount;
    }


}
