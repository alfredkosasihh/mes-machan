<?php

namespace App\Repositories;

use App\Entities\StandardCt;
use App\Entities\ProcessCalendar;
use App\Entities\CompanyCalendar;
use App\Entities\SetupShift;
use App\Entities\RestSetup;
use App\Entities\DayPerformanceStatistics;
use Carbon\Carbon;

class OEEperformanceRepository
{
    public function work($sum){
        $work = [];
        
        $work['date'] = Carbon::today()->format("Y-m-d"); // date
        // $work['date'] = '2019-11-08';

        $day = Carbon::now()->dayOfWeek; // day
        if($day == 1){
            $work['day'] = '一';
        }elseif($day == 2){
            $work['day'] = '二';
        }elseif($day == 3){
            $work['day'] = '三';
        }elseif($day == 4){
            $work['day'] = '四';
        }elseif($day == 5){
            $work['day'] = '五';
        }elseif($day == 6){
            $work['day'] = '六';
        }elseif($day == 7){
            $work['day'] = '日';
        }

        $weekend = Carbon::now()->isWeekend(); // weekend
        if($weekend == false){
            $work['weekend'] = '';
        }else{
            $work['weekend'] = '休';
        }
        
        // $com_work_type = ProcessCalendar::where('date', '2019-11-08')->first(); // work_name
        $com_work_type = CompanyCalendar::where('date', $work['date'])->first(); //看看公司行事曆有沒有加班資料

        if( $com_work_type == null ){ //如果公司行事曆沒資料
            $work_type = ProcessCalendar::where('date', $work['date'])->first(); //看看機台行事曆有沒有加班資料
            
            if( $work_type == false ){ //如果機台行事曆沒有加班資料 //沒加班
                $work['work_name'] = '正常班';  
            }elseif( $work_type->work_type_id == null ){
                if($work_type->status == 2 ){
                    $work['work_name'] = '休假';
                }elseif($work_type->status == 3){
                    $work['work_name'] = '國定假日';
                }
            }else{ //有加班
                $work_name = SetupShift::where('id', $work_type->work_type_id)->first()->type;
                if($work['weekend'] == '休'){
                    $work['work_name'] = '假日加班';
                }else{
                    $work['work_name'] = $work_name;
                }
            }

        }else{  //如果公司行事曆有資料 
            if( $com_work_type->work_type_id == null ){
                if($com_work_type->status == 2 ){
                    $work['work_name'] = '休假';
                }elseif($com_work_type->status == 3){
                    $work['work_name'] = '國定假日';
                }
            }else{ //有加班
                $work_name = SetupShift::where('id', $com_work_type->work_type_id)->first()->type;
                if($work['weekend'] == '休'){
                    $work['work_name'] = '假日加班';
                }else{
                    $work['work_name'] = $work_name;
                }
            }
        }


        // standard_working_hours
        if($work['work_name'] == '正常班' || $work['work_name'] == '早班' || $work['work_name'] == '中班' || $work['work_name'] == '晚班'){
            $work['standard_working_hours'] = '8:00:00';
        }elseif($work['work_name'] == '休假' || $work['work_name'] == '國定假日'){
            $work['standard_working_hours'] = '0:00:00';
        }elseif($work['work_name'] == '正常班加3' || $work['work_name'] == '大夜班' || $work['work_name'] == '早班加3'){ 
            $work['standard_working_hours'] = '11:00:00';
        }elseif($work['work_name'] == '正常班加3.5'){ 
            $work['standard_working_hours'] = '11:30:00';
        }elseif($work['work_name'] == '早班+中班'){ 
            $work['standard_working_hours'] = '16:00:00';
        }elseif($work['work_name'] == '正常加班1+大夜班'){ 
            $work['standard_working_hours'] = '24:00:00';
        }
        

        // total_hours
        if($work['work_name'] == '正常班'){

            $total_hours = 0;
            $totalTime = DayPerformanceStatistics::where('report_work_date', $work['date'])->select('total_hours')->distinct()->get();
            
            foreach($totalTime as $key =>$datas){
                $total_hours += strtotime($datas->total_hours) - strtotime(Carbon::today());
            }
            $work['total_hours'] = date("H:i:s", ($total_hours)-8*60*60);

        }elseif($work['work_name'] == '休假' || $work['work_name'] == '國定假日'){
            $work['total_hours'] = '00:00:00';
        }else{  // 以下處理加班
            if($com_work_type == null){  // 如果公司行事曆沒資料
                $work_type_id = ProcessCalendar::where('date', $work['date'])->first()->work_type_id; //看看機台行事曆的加班資料
                $work_time = SetupShift::where('id', $work_type_id)->first();
                $work_off = strtotime($work_time->work_off) - strtotime(Carbon::today());
                $work_on = strtotime($work_time->work_on) - strtotime(Carbon::today());

                $rest = RestSetup::where('id', $work_time->rest_group)->first();
                $rest_start = strtotime($rest->start) - strtotime(Carbon::today());
                $rest_end = strtotime($rest->end) - strtotime(Carbon::today());
                $rest_time = $rest_end - $rest_start;

                $work['total_hours'] = date("H:i:s", ($work_off - $work_on - $rest_time + 28800)-8*60*60); 
                // workoff - workon - 休息時間 + 8hour      28800為8小時的時間戳(秒數)
            }else{
                $work_time = SetupShift::where('id', $com_work_type->work_type_id)->first();
                $work_off = strtotime($work_time->work_off) - strtotime(Carbon::today());
                $work_on = strtotime($work_time->work_on) - strtotime(Carbon::today());

                $rest = RestSetup::where('id', $work_time->rest_group)->first();
                $rest_start = strtotime($rest->start) - strtotime(Carbon::today());
                $rest_end = strtotime($rest->end) - strtotime(Carbon::today());
                $rest_time = $rest_end - $rest_start;

                $work['total_hours'] = date("H:i:s", ($work_off - $work_on - $rest_time + 28800)-8*60*60); 
            }
        }

        return $work;

    }


    //////////////////////////機台加工時間      machine_processing_time

    public function mass_production_time($sum){

        $getSameDay = DayPerformanceStatistics::where('report_work_date', $sum['date'])->get();
        $sum_mass_production_time = 0;

        if( $getSameDay->first() == null ){
            return '';
        }else{
            foreach($getSameDay as $key =>$data){
                $mass_production_time = strtotime($data->mass_production_time) - strtotime(Carbon::today());
                $sum_mass_production_time = $sum_mass_production_time + $mass_production_time;
            }
            return date("H:i:s", $sum_mass_production_time-8*60*60);//將時間戳轉回字串    
        }
    }
    public function machine_processing_time($sum){
        $machine_processing_time = [];

        $getSameDay = DayPerformanceStatistics::where('report_work_date', $sum['date'])->get();    

        $sum_total_downtime = 0;   // total_downtime
        if( $getSameDay->first() == null ){ 
            $machine_processing_time['total_downtime'] = '';
        }else{
            foreach($getSameDay as $key =>$data){
                if($data->total_downtime != '00:00:00'){
                    $down_time = strtotime($data->total_downtime) - strtotime(Carbon::today());
                    $sum_total_downtime = $sum_total_downtime + $down_time;
                }
            }
            $machine_processing_time['total_downtime'] = date("H:i:s", $sum_total_downtime-8*60*60);//將時間戳轉回字串  
        }

        $sum_standard_processing_seconds = 0;
        if( $getSameDay->first() != null ){ 
            foreach($getSameDay as $key =>$data){  // standard_processing_seconds
                $sum_standard_processing_seconds = $sum_standard_processing_seconds + $data->standard_processing_seconds;
            }   
        }
        $machine_processing_time['standard_processing_seconds'] = $sum_standard_processing_seconds;

        $sum_actual_processing_seconds = 0;
        if( $getSameDay->first() != null ){ 
            foreach($getSameDay as $key =>$data){  // actual_processing_seconds
                $sum_actual_processing_seconds = $sum_actual_processing_seconds + $data->actual_processing_seconds;
            }
        }
        $machine_processing_time['actual_processing_seconds'] = $sum_actual_processing_seconds;
        
 
        $sum_updown_time = 0;   // updown_time
        if( $getSameDay->first() != null ){ 
            foreach($getSameDay as $key =>$data){  // actual_processing_seconds
                $sum_updown_time = $sum_updown_time + $data->updown_time;
            }
        }
        $machine_processing_time['updown_time'] = $sum_updown_time;

        return $machine_processing_time;
    }
    
    
    //////////////////////////////////機檯作業數量      machine_works_number

    public function machine_works_number($sum){
        $machine_works_number = [];
        $getSameDay = DayPerformanceStatistics::where('report_work_date', $sum['date'])->get(); 

        
        //  total_completion_that_day
        $sum_total_completion_that_day = 0;

        if( $getSameDay->first() == null ){            
            $machine_works_number['total_completion_that_day'] = '';
        }else{
            foreach($getSameDay as $key =>$data){
                $sum_total_completion_that_day = $sum_total_completion_that_day + $data->total_completion_that_day;
            }
            $machine_works_number['total_completion_that_day'] = $sum_total_completion_that_day; 
        }

        
        //  machine_processing
        $sum_machine_processing = 0;

        if( $getSameDay->first() == null ){
            $machine_works_number['machine_processing'] = '';
        }else{
            foreach($getSameDay as $key =>$data){
                $sum_machine_processing = $sum_machine_processing + $data->machine_processing;
            }
            $machine_works_number['machine_processing'] = $sum_machine_processing;
        }


        //  actual_production_quantity
        $sum_actual_production_quantity = 0;

        if( $getSameDay->first() == null ){
            $machine_works_number['actual_production_quantity'] = '';
        }else{
            foreach($getSameDay as $key =>$data){
                $sum_actual_production_quantity = $sum_actual_production_quantity + $data->actual_production_quantity;
            }
            $machine_works_number['actual_production_quantity'] = $sum_actual_production_quantity;
        }


        //  standard_completion
        $sum_standard_completion = 0;
        if( $getSameDay->first() == null ){
            $machine_works_number['standard_completion'] = 0;
        }else{
            foreach($getSameDay as $key =>$data){
                $sum_standard_completion = $sum_standard_completion + $data->standard_completion;
            }
            $machine_works_number['standard_completion'] = $sum_standard_completion;
        }


        //  total_input_that_day
        $sum_total_input_that_day = 0;

        if( $getSameDay->first() == null ){
            $machine_works_number['total_input_that_day'] = '';
        }else{
            foreach($getSameDay as $key =>$data){
                $sum_total_input_that_day = $sum_total_input_that_day + $data->total_input_that_day;
            }
            $machine_works_number['total_input_that_day'] = $sum_total_input_that_day;
        }


        //  adverse_number
        if($machine_works_number['total_completion_that_day'] == ''){
            $machine_works_number['adverse_number'] = '';
        }else{
            $machine_works_number['adverse_number'] = ($machine_works_number['total_input_that_day'] - $machine_works_number['total_completion_that_day']);
        }

        return $machine_works_number;
    }


    ///////////////////////////////////////機台嫁動除外工時   machinee_work_except_hours

    public function machinee_work_except_hours($sum){
        $machinee_work_except_hours = [];
        $getSameDay = DayPerformanceStatistics::where('report_work_date', $sum['date'])->get();
        
        $machinee_work_except_hours['correction_time'] = '';

        // hanging_time  
        $sum_hanging_time = 0;
        foreach($getSameDay as $key =>$data){
            if($data->hanging_time != "00:00:00"){
                $hanging_time = strtotime($data->hanging_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_hanging_time = $sum_hanging_time + $refueling_time;
            }
        }
        $machinee_work_except_hours['hanging_time'] = date("H:i:s", $sum_hanging_time-8*60*60);//將時間戳轉回字串


        // aggregate_time
        $sum_aggregate_time = 0;
        foreach($getSameDay as $key =>$data){
            if($data->aggregate_time != "00:00:00"){
                $aggregate_time = strtotime($data->aggregate_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_aggregate_time = $sum_aggregate_time + $aggregate_time;
            }
        }
        $machinee_work_except_hours['aggregate_time'] = date("H:i:s", $sum_aggregate_time-8*60*60);//將時間戳轉回字串


        // break_time
        $sum_break_time = 0;
        foreach($getSameDay as $key =>$data){
            if($data->break_time != "00:00:00"){
                $break_time = strtotime($data->break_time) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_break_time = $sum_break_time + $break_time;
            }
        }
        $machinee_work_except_hours['break_time'] = date("H:i:s", $sum_break_time-8*60*60);//將時間戳轉回字串

        $sum_chang_model_and_line = 0;
        foreach($getSameDay as $key =>$data){
            if($data->chang_model_and_line !== "00:00:00"){
                $chang_model_and_line = strtotime($data->chang_model_and_line) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_chang_model_and_line = $sum_chang_model_and_line + $chang_model_and_line;
            }
        }
        $machinee_work_except_hours['chang_model_and_line'] = date("H:i:s", $sum_chang_model_and_line-8*60*60);//將時間戳轉回字串

        // machine_downtime
        $sum_machine_downtime = 0; 
        foreach($getSameDay as $key =>$data){
            if($data->machine_downtime != "00:00:00"){
                $machine_downtime = strtotime($data->machine_downtime) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_machine_downtime = $sum_machine_downtime + $machine_downtime;
            }
        }
        $machinee_work_except_hours['machine_downtime'] = date("H:i:s", $sum_machine_downtime-8*60*60);//將時間戳轉回字串
        

        $machinee_work_except_hours['bad_disposal_time'] = '';
        $machinee_work_except_hours['model_damge_change_line_time'] = '';
        $machinee_work_except_hours['program_modify_time'] = '';
        $machinee_work_except_hours['machine_maintain_time'] = '';

        // excluded_working_hours
        $sum_excluded_working_hours = 0;
        foreach($getSameDay as $key =>$data){
            if($data->excluded_working_hours != "00:00:00"){
                $excluded_working_hours = strtotime($data->excluded_working_hours) - strtotime(Carbon::today()); //將字串改為時間戳  之後再相減進行校正
                $sum_excluded_working_hours = $sum_excluded_working_hours + $excluded_working_hours;
            }
        }
        $machinee_work_except_hours['excluded_working_hours'] = date("H:i:s", $sum_excluded_working_hours-8*60*60);//將時間戳轉回字串        

        return $machinee_work_except_hours;

    }
    
    
    public function machine_performance($sum){

        $machine_performance = [];
        
        $sameday = DayPerformanceStatistics::where('report_work_date', $sum['date'])->get();
        $machine_utilization_rate = 0;
        foreach($sameday as $key =>$datas){
            $machine_utilization_rate = $machine_utilization_rate + $datas->machine_utilization_rate;
        }
        $machine_performance['machine_utilization_rate'] = round(($machine_utilization_rate/count($sameday)), 2);

        
        $performance_rate = 0;
        foreach($sameday as $key =>$datas){
            $performance_rate = $performance_rate + $datas->performance_rate;
        }
        $machine_performance['performance_rate'] = round(($performance_rate/count($sameday)), 2);


        
        $yield = 0;
        foreach($sameday as $key =>$datas){
            $yield = $yield + $datas->yield;
        }
        $machine_performance['yield'] = round(($yield/count($sameday)), 2);


        
        $OEE = 0;
        foreach($sameday as $key =>$datas){
            $OEE = $OEE + $datas->OEE;
        }
        $machine_performance['OEE'] = round(($OEE/count($sameday)), 2);

        return $machine_performance;
    }
}