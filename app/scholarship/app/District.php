<?php

namespace App;

use App\Support\MongoModel;

class District extends MongoModel
{
    protected $table = 'district';
    public $timestamps = false;
     public function area()
    {
      return $this->hasMany('App\Area', 'district_id');
    }

     public function unit()
    {
      return $this->hasMany('App\Unit', 'district_id');
    }
    /**
     * get Passed files number for district
     * based on category,scheme
     */
    public function getPassedNumber($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $appCount = Application::where('district_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->count();
        }
        else{
          $appCount = Application::where('district_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      else{
        if($date1===null){
          $appCount = Application::where('district_id',$this->id)->where('grant_status','>',0)->count();
        }
        else{
          $appCount = Application::where('district_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      return $appCount;
    }

    // /**
    //  * get Delivered files number for district
    //  * based on category,scheme
    //  */
    public function getDeliveredNumber($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $appCount = Application::where('district_id',$this->id)->where('status',8)->where('cat_id',$category)->count();
        }
        else{
          $appCount = Application::where('district_id',$this->id)->where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      else{
        if($date1===null){
          $appCount = Application::where('district_id',$this->id)->where('status',8)->count();
        }
        else{
          $appCount = Application::where('district_id',$this->id)->where('status',8)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      return $appCount;
    }

    // /**
    //  * get Passed amount for district
    //  * based on category,scheme
    //  */
    public function getPassedAmount($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $totalAmount = Application::where('district_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('district_id',$this->id)->where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      else{
        if($date1===null){
          $totalAmount = Application::where('district_id',$this->id)->where('grant_status','>',0)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('district_id',$this->id)->where('grant_status','>',0)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      return $totalAmount;
    }

    // /**
    //  * get Delivered Amount for district
    //  * based on category,scheme
    //  */
    public function getDeliveredAmount($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $totalAmount = Application::where('district_id',$this->id)->where('status',8)->where('cat_id',$category)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('district_id',$this->id)->where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      else{
        if($date1===null){
          $totalAmount = Application::where('district_id',$this->id)->where('status',8)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('district_id',$this->id)->where('status',8)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      return $totalAmount;
    }

    // /**
    //  * get Passed files number for all districts
    //  * based on category,scheme
    //  */
    public function getAllPassedNumber($category="all",$date1=null,$date2=null){
      
      if($category!=="all"){
        if($date1===null){
          $appCount = Application::where('grant_status','>',0)->where('cat_id',$category)->count();
        }
        else{
          $appCount = Application::where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      else{
        if($date1===null){
          $appCount = Application::where('grant_status','>',0)->count();
        }
        else{
          $appCount = Application::where('grant_status','>',0)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }

      return $appCount;
    }

    // /**
    //  * get Delivered files number for all districts
    //  * based on category,scheme
    //  */
    public function getAllDeliveredNumber($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $appCount = Application::where('status',8)->where('cat_id',$category)->count();
        }
        else{
          $appCount = Application::where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      else{
        if($date1===null){
          $appCount = Application::where('status',8)->count();
        }
        else{
          $appCount = Application::where('status',8)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->count();
        }
      }
      return $appCount;
    }

    // /**
    //  * get Passed amount for all district
    //  * based on category,scheme
    //  */
    public function getAllPassedAmount($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $totalAmount = Application::where('grant_status','>',0)->where('cat_id',$category)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('grant_status','>',0)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      else{
        if($date1===null){
          $totalAmount = Application::where('grant_status','>',0)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('grant_status','>',0)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      return $totalAmount;
    }

    // /**
    //  * get Delivered Amount for all district
    //  * based on category,scheme
    //  */
    public function getAllDeliveredAmount($category="all",$date1=null,$date2=null){
      if($category!=="all"){
        if($date1===null){
          $totalAmount = Application::where('status',8)->where('cat_id',$category)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('status',8)->where('cat_id',$category)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      else{
        if($date1===null){
          $totalAmount = Application::where('status',8)->sum('amount_granted');
        }
        else{
          $totalAmount = Application::where('status',8)->whereBetween('granted_date', [$date1.' 00:00:00', $date2.' 00:00:00'])->sum('amount_granted');
        }
      }
      return $totalAmount;
    }

}
