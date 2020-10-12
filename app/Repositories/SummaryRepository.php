<?php

namespace App\Repositories;

use App\Entities\Resource;
use App\Entities\StandardCt;
use App\Entities\ProcessCalendar;
use App\Entities\CompanyCalendar;
use App\Entities\SetupShift;
use App\Entities\MachineDefinition;
use App\Entities\RestSetup;
use Carbon\Carbon;

function calc_total_hours($work_shift, $end, $dayPerfor,$first_time, $last_time){
    $work_end = strtotime($work_shift->work_off) - strtotime(Carbon::today()); //班別設定的工作結束時間

    if($end->orderno == $dayPerfor['material_name']){
        $sum_rest_time = calc_sum_rest_time($work_shift, $first_time, $work_end);
        return date("H:i:s", ($work_end - $first_time - $sum_rest_time)-8*60*60);
    }else{
        $sum_rest_time = calc_sum_rest_time($work_shift, $first_time, $last_time);
        return date("H:i:s", ($last_time - $first_time - $sum_rest_time)-8*60*60);
    }
}

function calc_sum_rest_time($work_shift, $first_time, $last_time){
    $rest = RestSetup::where('rest_id', $work_shift->rest_group)->get();

    $sum_rest_time = 0;
    foreach($rest as $key => $rest_datas){
        $rest_start = strtotime($rest_datas->start) - strtotime(Carbon::today());
        $rest_end = strtotime($rest_datas->end) - strtotime(Carbon::today());  

        if($first_time < $rest_start && $last_time > $rest_end){ //工作時間內有休息時間
            $rest_time = $rest_end - $rest_start;
            $sum_rest_time += $rest_time;
        }
    }
    return $sum_rest_time;
}

function normal_work_rest($first_time, $last_time){
    $morning_rest_start = strtotime('10:00:00') - strtotime(Carbon::today());
    $morning_rest_end = strtotime('10:10:00') - strtotime(Carbon::today());  
    $lunch_rest_start = strtotime('12:10:00') - strtotime(Carbon::today());
    $lunch_rest_end = strtotime('13:00:00') - strtotime(Carbon::today());  
    $afternoon_rest_start = strtotime('15:00:00') - strtotime(Carbon::today());
    $afternoon_rest_end = strtotime('15:10:00') - strtotime(Carbon::today()); 

    $rest0 = [$morning_rest_start, $morning_rest_end];
    $rest1 = [$lunch_rest_start, $lunch_rest_end];
    $rest2 = [$afternoon_rest_start, $afternoon_rest_end];
    $rest = array($rest0, $rest1, $rest2);

    $normal_work_rest = 0;
    for($i = 0; $i < 3; $i++){
        if($first_time < $rest[$i][0] && $last_time > $rest[$i][1]){ //工作時間內有休息時間
            $rest_time = $rest[$i][1] - $rest[$i][0];
            $normal_work_rest += $rest_time;
        }
    }
    return $normal_work_rest;
}

function calc_unnnormal_standard_work_hours($dayPerfor, $end, $work_shift, $last_time){
    $work_start = strtotime($work_shift->work_on) - strtotime(Carbon::today()); //班別設定的工作開始時間
    $work_end = strtotime($work_shift->work_off) - strtotime(Carbon::today()); //班別設定的工作結束時間

    if($end->orderno == $dayPerfor['material_name']){  // 最後一筆
        if($this->pre_last_time == 0 ){   //如果當天第一筆料號即是最後一筆
            $sum_rest_time = calc_sum_rest_time($work_shift, $work_start, $work_end);
            if($last_time > $work_end){  //如果工作時間超出班別時間
                $this->real_standard_worktime = ($last_time - $work_start - $sum_rest_time);
                return round((($last_time - $work_start - $sum_rest_time)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
            }else{
                $this->real_standard_worktime = ($work_end - $work_start - $sum_rest_time);
                return round((($work_end - $work_start - $sum_rest_time)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
            }
        }else{  // 真的是最後一筆
            $sum_rest_time = calc_sum_rest_time($work_shift, $this->pre_last_time, $work_end);
            if($last_time > $work_end){  //如果工作時間超出班別時間
                $this->real_standard_worktime = ($last_time - $this->pre_last_time - $sum_rest_time);
                return round((($last_time - $this->pre_last_time - $sum_rest_time)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
            }else{
                $this->real_standard_worktime = ($work_end - $this->pre_last_time - $sum_rest_time);
                return round((($work_end - $this->pre_last_time - $sum_rest_time)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
            }
        } 
    }else{ // 非最後一筆(多筆料號)
        if($this->pre_last_time == 0 ){   //當天第一筆料號 
            $standard_working_hours = $last_time - $work_start;
            $sum_rest_time = calc_sum_rest_time($work_shift, $work_start, $last_time);
            $this->pre_last_time = $last_time;        // 把第一筆料號的結束時間放進去
            $this->real_standard_worktime = ($standard_working_hours - $sum_rest_time);
            return round((($standard_working_hours - $sum_rest_time)/3600), 2); 
        }else{
            $standard_working_hours = $last_time - $this->pre_last_time;  //扣掉上一筆的結束時間
            $sum_rest_time = calc_sum_rest_time($work_shift, $this->pre_last_time, $last_time);
            $this->pre_last_time = $last_time;        // 把這次料號的結束時間放進去
            $this->real_standard_worktime = ($standard_working_hours - $sum_rest_time);
            return round((($standard_working_hours - $sum_rest_time)/3600), 2); 
        }
    }
}

class SummaryRepository
{
    protected $pre_last_time;
    protected $real_standard_worktime;
    public function __construct()
    {
        $this->pre_last_time = $pre_last_time = 0;
        $this->real_standard_worktime = $real_standard_worktime = 0;
    }

    public function work_name($dayPerfor){
        $com_work_type = CompanyCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看公司行事曆有沒有加班資料

        if( $com_work_type == null ){ //如果公司行事曆沒資料
            $work_type = ProcessCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看機台行事曆有沒有加班資料
            
            if( $work_type == false ){ //如果機台行事曆沒有加班資料 //沒加班
                return '正常班';  
            }elseif( $work_type->work_type_id == null ){
                if($work_type->status == 2 ){
                    return '休假';
                }elseif($work_type->status == 3){
                    return '國定假日';
                }
            }else{ //機台行事曆有加班
                $work_name = SetupShift::where('id', $work_type->work_type_id)->first()->type;
                if(Carbon::now()->isWeekend() == true){
                    return '假日加班';
                }else{
                    return $work_name;
                }
            }

        }else{  //公司行事曆有資料 
            if( $com_work_type->work_type_id == null ){
                if($com_work_type->status == 2 ){
                    return '休假';
                }elseif($com_work_type->status == 3){
                    return '國定假日';
                }
            }else{ //公司行事曆有加班
                $work_name = SetupShift::where('id', $com_work_type->work_type_id)->first()->type;
                if(Carbon::now()->isWeekend() == true){
                    return '假日加班';
                }else{
                    return $work_name;
                }
            }
        }
    }

    public function standard_working_hours($dayPerfor){
        $last = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->latest('time')->first();
        $end = Resource::where('date', $dayPerfor['report_work_date'])->latest('time')->first(); //resource當天最後一筆
        $last_time = strtotime($last->time) - strtotime(Carbon::today());  //當天該料號結束工作的時間

        if ($dayPerfor['work_name'] == '休假' || $dayPerfor['work_name'] == '國定假日') {
            return 0;
        }elseif($dayPerfor['work_name'] == '正常班' ){

            $work_end = strtotime('17:10:00') - strtotime(Carbon::today()); //正常班設定的最後時間
            $work_start = strtotime('8:00:00') - strtotime(Carbon::today()); //正常班設定的開始時間

            if($end->orderno == $dayPerfor['material_name']){  //正常班 最後一筆
                if($this->pre_last_time == 0 ){   //如果當天第一筆料號即是最後一筆
                    if($last_time > $work_end){  //如果工作時間超出班別時間
                        $this->real_standard_worktime = ($last_time - $work_start - 4200);
                        return round((($last_time - $work_start - 4200)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
                    }else{
                        $this->real_standard_worktime = ($work_end - $work_start - 4200);
                        return round((($work_end - $work_start - 4200)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
                    }
                }else{  // 真的是最後一筆
                    $normal_rest = normal_work_rest($this->pre_last_time , $work_end);
                    if($last_time > $work_end){  //如果工作時間超出班別時間
                        $this->real_standard_worktime = ($last_time - $this->pre_last_time - $normal_rest);
                        return round((($last_time - $this->pre_last_time - $normal_rest)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
                    }else{
                        $this->real_standard_worktime = ($work_end - $this->pre_last_time - $normal_rest);
                        return round((($work_end - $this->pre_last_time - $normal_rest)/3600), 2); //顯示小時 扣掉1:10:00的休息時間
                    }
                } 
            }else{ //正常班 非 最後一筆
                if($this->pre_last_time == 0 ){   //當天第一筆料號 
                    $standard_working_hours = $last_time - $work_start;
                    $normal_rest = normal_work_rest($work_start , $last_time);
                    $this->pre_last_time = $last_time;        // 把第一筆料號的結束時間放進去
                    $this->real_standard_worktime = ($standard_working_hours - $normal_rest);
                    return round((($standard_working_hours - $normal_rest)/3600), 2); 
                }else{
                    $standard_working_hours = $last_time - $this->pre_last_time;  //扣掉上一筆的結束時間
                    $normal_rest = normal_work_rest($this->pre_last_time , $last_time);
                    $this->pre_last_time = $last_time;        // 把這次料號的結束時間放進去
                    $this->real_standard_worktime = ($standard_working_hours - $normal_rest);
                    return round((($standard_working_hours - $normal_rest)/3600), 2); 
                }
            }   

        }else{  //非正常班
            $com_work_type = CompanyCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看公司行事曆有沒有加班資料
            if( $com_work_type == null ){ //如果公司行事曆沒資料
                $work_type = ProcessCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看機台行事曆的加班資料
                $work_shift = SetupShift::where('id', $work_type->work_type_id)->first();

                $standard_working_hours = calc_unnnormal_standard_work_hours($dayPerfor, $end, $work_shift, $last_time);
                return $standard_working_hours;                

            }else{  //公司行事曆有資料 
                $work_shift = SetupShift::where('id', $com_work_type->work_type_id)->first();
                $standard_working_hours = calc_unnnormal_standard_work_hours($dayPerfor, $end, $work_shift, $last_time);
                return $standard_working_hours;
            }
        }
    }

    public function total_hours($dayPerfor)  //改成 當天這筆料號在summaries的總working_time
    {
        $first_time = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->first()->time;
        $last_time = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->latest('time')->first()->time;
        $end = Resource::where('date', $dayPerfor['report_work_date'])->latest('time')->first(); //resource當天最後一筆
        $first_time = strtotime($first_time) - strtotime(Carbon::today()); //當天該料號工作開始時間
        $last_time = strtotime($last_time) - strtotime(Carbon::today()); //當天該料號工作結束時間

        if ($dayPerfor['work_name'] == '休假' || $dayPerfor['work_name'] == '國定假日') {
            return 0;
        }elseif($dayPerfor['work_name'] == '正常班'){ 

            $work_end = strtotime('17:10:00') - strtotime(Carbon::today()); //正常班設定的最後結束時間
            if($end->orderno == $dayPerfor['material_name']){
                $sum_rest_time = normal_work_rest($first_time, $work_end);
                return date("H:i:s", ($work_end - $first_time - $sum_rest_time)-8*60*60); // 顯示確切時數
            }else{
                $sum_rest_time = normal_work_rest($first_time, $last_time);
                return date("H:i:s", ($last_time - $first_time - $sum_rest_time)-8*60*60); // 顯示確切時數
            }
        }else{
            $com_work_type = CompanyCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看公司行事曆有沒有加班資料
            if( $com_work_type == null ){ //如果公司行事曆沒資料
                $work_type = ProcessCalendar::where('date', $dayPerfor['report_work_date'])->first(); //看看機台行事曆的加班資料
                $work_shift = SetupShift::where('id', $work_type->work_type_id)->first();
                
                $total_hours = calc_total_hours($work_shift, $end, $dayPerfor,$first_time, $last_time);
                return $total_hours;
                
            }else{  //公司行事曆有資料 
                $work_shift = SetupShift::where('id', $com_work_type->work_type_id)->first();

                $total_hours = calc_total_hours($work_shift, $end, $dayPerfor,$first_time, $last_time);
                return $total_hours;
            }
        }
    }

    public function machine_name($dayPerfor){
        $standard = StandardCt::where('orderno', $dayPerfor['material_name'])->first();
        return $standard->MachineDefinition->machine_name;
    }
    public function machine_code($dayPerfor){
        $definition = MachineDefinition::where('machine_name', $dayPerfor['machine_name'])->first();
        return $definition->machine_id;
    }

    //機檯作業數量
    public function machine_works_number($dayPerfor)
    {
        $machine_works_number = [];
        $sameDayAndName_id10 = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->where('status_id', 10)->with('summary')->get();
        $sameDayAndName_id9 = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->where('status_id', 9)->with('summary')->get();
        $count1 = 0;
        $count2 = 0;

        // machine_processing
        // COUNTIFS( 捲料機績效分析!$C:$C, 10,  捲料機績效分析!$E:$E,機台日績效統計表!$B6,   捲料機績效分析!$B:$B,機台日績效統計表!$J6) 
        foreach ($sameDayAndName_id10 as $key => $data) {
            $count1 = $count1 + 1;
        }
        $machine_works_number['machine_processing'] = $count1;
        $machine_works_number['actual_production_quantity'] = $count1;  // 同上？
        $machine_works_number['total_input_that_day'] = $count1;    //同上上？？

        // standard_completion //個別料號分開計算 有問題
        $machine_works_number['standard_completion'] = (($dayPerfor['standard_working_hours'] * 3600) / ($dayPerfor['standard_processing'] + $dayPerfor['standard_updown']));
        // total_completion_that_day
        // COUNTIFS( 捲料機績效分析!$C:$C, 9,   捲料機績效分析!$E:$E,機台日績效統計表!$B7,   捲料機績效分析!$B:$B,機台日績效統計表!$J7)
        foreach ($sameDayAndName_id9 as $key => $data) {
            $count2 = $count2 + 1;
        }
        $machine_works_number['total_completion_that_day'] = $count2;

        $machine_works_number['adverse_number'] = ($machine_works_number['total_input_that_day'] - $machine_works_number['total_completion_that_day']);

        return $machine_works_number;
    }

    //標準ct
    public function standard_processing($dayPerfor)
    {
        $standard = StandardCt::where('orderno', $dayPerfor['material_name'])->first();
        return $standard->standard_ct;
    }
    public function standard_updown($dayPerfor)
    {
        $standard = StandardCt::where('orderno', $dayPerfor['material_name'])->first();
        return $standard->standard_updown;
    }

    //機台加工時間
    public function mass_production_time($dayPerfor)
    {
        $sameDayAndName_changeLine = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->whereHas(
            'summary',
            function ($query) {
                $query->where('abnormal', "換線");
            }
        )->first();

        $sameDayAndName = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->with('summary')->get();
        $sameDay = Resource::where('date', $dayPerfor['report_work_date'])->with('summary')->get();
        $sum = 0;
        $sum0 = 0;
        $sum1 = 0;
        $sum2 = 0;

        if ($sameDayAndName_changeLine == null) {    //沒有換線
            foreach ($sameDayAndName as $key => $data) {
                $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                $sum1 = $sum1 + $time;
            }
            foreach ($sameDayAndName as $key => $data) {
                if ($data->summary->serial_number_day == 1) {
                    $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                    $sum2 = $sum2 + $time;
                }
            }
            $sum = $sum1 - $sum2;
            return date("H:i:s", $sum - 8 * 60 * 60); //將時間戳轉回字串

        } else {      //有換線
            if ($dayPerfor['material_name'] == "") {

                foreach ($sameDay as $key => $data) {
                    $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                    $sum1 = $sum1 + $time;
                }
                foreach ($sameDayAndName as $key => $data) {
                    if (1 == $data->summary->serial_number_day) {
                        $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                        $sum2 = $sum2 + $time;
                    }
                }
                $sum = $sum1 - $sum2;
                return date("H:i:s", $sum - 8 * 60 * 60); //將時間戳轉回字串

            } else {  //有換線也有料號名

                foreach ($sameDayAndName as $key => $data) {
                    $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                    $sum0 = $sum0 + $time;
                    if ('換線' == $data->summary->abnormal) {
                        $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                        $sum1 = $sum1 + $time;
                    }
                }
                foreach ($sameDayAndName as $key => $data) {
                    if (1 == $data->summary->serial_number_day) {
                        $time = strtotime($data->summary->working_time) - strtotime(Carbon::today());
                        $sum2 = $sum2 + $time;
                    }
                }
                $sum = $sum0 - $sum1 - $sum2;
                return date("H:i:s", $sum - 8 * 60 * 60); //將時間戳轉回字串
            }
        }
    }
    public function machine_processing_time($dayPerfor)
    {
        $machine_processing_time = [];
        $sameDay = Resource::where('date', $dayPerfor['report_work_date'])->with('summary')->get();
        $sameDayAndName = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->with('summary')->get();
        $total_downtime = 0;
        $actual_processing_seconds = 0;

        // total_downtime
        if ($sameDayAndName->first() == null) {
            $machine_processing_time['total_downtime'] = '';
        } else {
            foreach ($sameDayAndName as $key => $data) {
                $down_time = strtotime($data->summary->down_time) - strtotime(Carbon::today());
                $total_downtime = $total_downtime + $down_time;
            }
            $machine_processing_time['total_downtime'] = date("H:i:s", $total_downtime - 8 * 60 * 60);
        }

        // standard_processing_seconds  標準應完工量 乘 標準加工秒數
        
        $machine_processing_time['standard_processing_seconds'] = ($dayPerfor['standard_processing']*$dayPerfor['standard_completion']);

        // actual_processing_seconds
        foreach ($sameDayAndName as $key => $data) {
            $uat_h_26_2 = $data->summary->uat_h_26_2;
            $uat_h_26_3 = $data->summary->uat_h_26_3;
            $uat_h_36_3 = $data->summary->uat_h_36_3;
            $actual_processing_seconds = $actual_processing_seconds + $uat_h_26_2 + $uat_h_26_3 + $uat_h_36_3;
        }
        $machine_processing_time['actual_processing_seconds'] = $actual_processing_seconds;

        $machine_processing_time['machine_speed'] = '';   //空白??

        $machine_processing_time['updown_time'] = ($dayPerfor['total_completion_that_day'] * $dayPerfor['standard_updown']);

        return $machine_processing_time;
    }

    //機台嫁動除外工時   machinee_work_except_hours
    public function machinee_work_except_hours($dayPerfor)
    {
        $machinee_work_except_hours = [];
        $sameDayAndName = Resource::where('orderno', $dayPerfor['material_name'])->where('date', $dayPerfor['report_work_date'])->with('summary')->get();
        $hanging_time = 0;
        $aggregate_time = 0;
        $break_time = 0;
        $chang_model_and_line = 0;
        $excluded_working_hours = 0;

        $machinee_work_except_hours['correction_time'] = '';  //由APP輸入或由機台自動判定除外工時(暖機(校正)時間)

        //hanging_time
        foreach ($sameDayAndName as $key => $data) {
            if ($data->summary->refueling_time != "00:00:00") {
                $refueling_time = strtotime($data->summary->refueling_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $hanging_time = $hanging_time + $refueling_time;
            } else {
                $machinee_work_except_hours['hanging_time'] = 0;
            }
        }
        $machinee_work_except_hours['hanging_time'] = date("H:i:s", $hanging_time - 8 * 60 * 60); //將時間戳轉回字串

        //aggregate_time
        foreach ($sameDayAndName as $key => $data) {
            if ($data->summary->aggregate_time != "00:00:00") {
                $aggregateTime = strtotime($data->summary->aggregate_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $aggregate_time = $aggregate_time + $aggregateTime;
            } else {
                $machinee_work_except_hours['aggregate_time'] = 0;
            }
        }
        $machinee_work_except_hours['aggregate_time'] = date("H:i:s", $aggregate_time - 8 * 60 * 60); //將時間戳轉回字串    

        //break_time
        foreach ($sameDayAndName as $key => $data) {
            if ($data->summary->break_time != "00:00:00") {
                $breakTime = strtotime($data->summary->break_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $break_time = $break_time + $breakTime;
            } else {
                $machinee_work_except_hours['break_time'] = 0;
            }
        }
        $machinee_work_except_hours['break_time'] = date("H:i:s", $break_time - 8 * 60 * 60); //將時間戳轉回字串


        $machinee_work_except_hours['chang_model_and_line'] = 0;  //由APP輸入或由機台自動判定除外工時(換模換線)
        foreach ($sameDayAndName as $key => $data) {
            if ($data->summary->refueling_time != "00:00:00" || $data->summary->refueler_time != "00:00:00") {
                $refueling_time = strtotime($data->summary->refueling_time) - strtotime(Carbon::today()); 
                $refueler_time = strtotime($data->summary->refueler_time) - strtotime(Carbon::today()); 
                $chang_model_and_line = $chang_model_and_line + $refueling_time + $refueler_time;
            } else {
                $machinee_work_except_hours['chang_model_and_line'] = 0;
            }
        }
        $machinee_work_except_hours['chang_model_and_line'] = date("H:i:s", $chang_model_and_line - 8 * 60 * 60); //將時間戳轉回字串

        $machinee_work_except_hours['bad_disposal_time'] = '';  //由APP輸入或由機台自動判定除外工時(物料品質不良處置時間)
        $machinee_work_except_hours['model_damge_change_line_time'] = '';  //由APP輸入或由機台自動判定除外工時(模具損壞換線時間)
        $machinee_work_except_hours['program_modify_time'] = '';  //由APP輸入或由機台自動判定除外工時(程式修改時間)
        $machinee_work_except_hours['meeting_time'] = '';  //由APP輸入集會時間
        $machinee_work_except_hours['environmental_arrange_time'] = '';  //由APP輸入或由機台自動判定除外工時(環境整理整頓時間)

        //excluded_working_hours
        $a0 = $machinee_work_except_hours['correction_time'];
        $a1 = $machinee_work_except_hours['hanging_time'];
        $a2 = $machinee_work_except_hours['aggregate_time'];
        $a3 = $machinee_work_except_hours['break_time'];
        $a4 = $machinee_work_except_hours['chang_model_and_line'];
        $a5 = $machinee_work_except_hours['bad_disposal_time'];
        $a6 = $machinee_work_except_hours['model_damge_change_line_time'];
        $a7 = $machinee_work_except_hours['program_modify_time'];
        $a8 = $machinee_work_except_hours['meeting_time'];
        $a9 = $machinee_work_except_hours['environmental_arrange_time'];

        $a = array($a0, $a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9);

        for ($i = 0; $i < 10; $i++) {
            if ($a[$i] == '') {      //把原本是空白格的時間更正為0
                $a[$i] = '00:00:00';
            }
            $a[$i] = strtotime($a[$i]) - strtotime(Carbon::today());
            $excluded_working_hours = $excluded_working_hours + $a[$i];
        }  
        $machinee_work_except_hours['excluded_working_hours'] = date("H:i:s", $excluded_working_hours - 8 * 60 * 60);

        return $machinee_work_except_hours;
    }

    //機台性能除外工時   performance_exclusion_time
    public function performance_exclusion_time($dayPerfor)
    {
        $performance_exclusion_time = [];
        $machine_downtime = 0;
        $machine_utilization_rate = 0;

        //machine_downtime
        $total_downtime = strtotime($dayPerfor['total_downtime']) - strtotime(Carbon::today());
        $excluded_working_hours = strtotime($dayPerfor['excluded_working_hours']) - strtotime(Carbon::today());

        $machine_downtime = $total_downtime - $excluded_working_hours;
        $performance_exclusion_time['machine_downtime'] = date("H:i:s", $machine_downtime - 8 * 60 * 60);

        $performance_exclusion_time['machine_maintain_time'] = '';  //由APP輸入

        //machine_utilization_rate   ($mass_production_time - $total_downtime + $updown_time)/($standard_working_hours)
        $mass_production_time = strtotime($dayPerfor['mass_production_time']) - strtotime(Carbon::today());
        $updown_time = $dayPerfor['updown_time'];

        //應該除以標準工時 但這裡直接拿總時數計算更快
        if($mass_production_time < $total_downtime){
            $machine_utilization_rate = round(((($mass_production_time + $total_downtime) - $total_downtime - $updown_time) / ($this->real_standard_worktime)), 4);
        }else{
            $machine_utilization_rate = round((($mass_production_time - $total_downtime - $updown_time) / ($this->real_standard_worktime)), 4);
        }
        $performance_exclusion_time['machine_utilization_rate'] = $machine_utilization_rate;

        $performance_exclusion_time['performance_rate'] = round(($dayPerfor['total_completion_that_day'] / $dayPerfor['standard_completion']), 4);

        //yield  ($total_completion_that_day - $adverse_number)/($total_completion_that_day)

        if($dayPerfor['total_completion_that_day'] == 0){
            $performance_exclusion_time['yield'] = 0;
        }else{
            $performance_exclusion_time['yield'] = round((($dayPerfor['total_input_that_day'] - $dayPerfor['adverse_number']) / ($dayPerfor['total_input_that_day'])),4);
        }

        $performance_exclusion_time['OEE'] = round(($performance_exclusion_time['machine_utilization_rate'] * $performance_exclusion_time['performance_rate'] * $performance_exclusion_time['yield']), 4);

        return $performance_exclusion_time;
    }
}
