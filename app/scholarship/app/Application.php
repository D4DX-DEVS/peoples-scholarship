<?php

namespace App;

use App\Support\MongoModel;
use DB;

class Application extends MongoModel
{
    /**
     * Integer columns, so ids arriving as strings are cast.
     *
     * @var list<string>
     */
    protected $integerColumns = ['area_id', 'attachments_exist', 'cat_id', 'course_id', 'district_id', 'grant_status', 'no_of_installments', 'persid', 'status', 'unit_id', 'year_id'];

	/**
	 * department to which the file belongs
	 */
    public function appYear(){
    	return $this->belongsTo('App\Yearsetting','year_id');
    }

    public function category(){
    	return $this->belongsTo('App\Category','cat_id');
    }

    /**
     *  scheme of the file
     */
    public function course(){
    	return $this->belongsTo('App\Course','course_id');
    }

    /**
     * district to which the file/application belongs
     */
    public function district(){
    	return $this->belongsTo('App\District','district_id');
    }

    /**
     * area to which the file/application belongs
     */
    public function area(){
    	return $this->belongsTo('App\Area','area_id');
    }

    /**
     * unit to which the file/application belongs
     */
    public function unit(){
    	return $this->belongsTo('App\Unit','unit_id');
    }

    /**
     *  user who created this file
     */
    public function person(){
    	return $this->belongsTo('App\Person','persid');
    }

    public function getStatus(){
    	return $this->belongsTo('App\Status','status');
    }

    public function installments(){
        return $this->hasMany('App\Installment','appl_id');
    }

    public function getGrantStatus($status)
    {
     switch ($status) {
         case '6':
             $statusText =  'Waiting for Documents';
             $bgColour = 'green';
             break;
         case '7':
             $statusText =  'Forwarded to Accounts';
             $bgColour = 'yellow';
             break;
         case '8':
             $statusText =  'Installments Due';
             $bgColour = 'green';
             break;
         case '9':
             $statusText =  'Completed';
             $bgColour = 'green';
             break;
         default:
             $statusText =  'Status undefined';
             $bgColour = 'red';
             break;
         }
         return ['statusText'=>$statusText , 'bgColour'=> $bgColour];
    }
        /**
     * returns number of installments in status
     * @param status number
     * @return number of installments
     */
    public function getInstallmentsCount($status){
        return $this->installments->where('status',$status)->count();
    }

    /**
    * returns the total amount payed
    */    
    public function getAmountPayed(){
        $installments = $this->installments->where('status',4)->all();
        $amount = 0.00;
        foreach ($installments as $installment) {
            $amount += $installment->amount;
        }
        return $amount;
    }


    /**
     * returns pending amount to be payed to the beneficiary
     */
    public function getAmountPending(){
        return $this->amount_granted-$this->getAmountPayed();
    }

    /**
     * return current installment
     */
    public function getCountInstallmentsDue(){
        $installment = Installment::where('appl_id',$this->id)->whereIn('status',[1,2,3])->get();
        return $installment->count();
    }
    /**
     * Returns the latest statistic entry for this file
     */
    public function getLatestStatistic(){
        $statistic = Statistic::where('appl_id',$this->id)->orderBy('id','desc')->first();
        return $statistic;
    }

            /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getAppCount($status=0,$yearid=0){
        
        $query = DB::table('applications');
        if ($status !== 0) {
            $query->where('status',$status)->where('year_id',$yearid);
        }else{
            $query->where('status','<',9 )->where('year_id',$yearid);
        }

        return $query->count();
    }
}
