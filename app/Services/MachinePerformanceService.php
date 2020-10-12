<?php

namespace App\Services;

use App\Repositories\MachinePerformanceRepository;
use App\Repositories\RollerDataRepository;
use App\Services\RollerDataService;
use App\Entities\StandardCt;
use Carbon\Carbon;

class MachinePerformanceService
{
    protected $machinePerformanceRepo;
    protected $rollerDataService;
    protected $rollerDataRepo;

    public function __construct(MachinePerformanceRepository $machinePerformanceRepository, RollerDataService $rollerDataService
                                , RollerDataRepository $rollerDataRepository)
    {
        $this->machinePerformanceRepo = $machinePerformanceRepository; 
        $this->rollerDataService = $rollerDataService;
        $this->rollerDataRepo = $rollerDataRepository;
    }

    public function counts($data)
    {
        $count = $this->machinePerformanceRepo->checkUnfinish($data);
        $machine = $this->rollerDataService->machine($data);
        $count->id = $count->id + 1;
        $count->serial_number++;
     
        if ($count->resources_id == 0 || $data['date'] != $count['date']) { 
            
            $data['status_id'] == 3 ? $count->open++ : ($count->open == 0 ? $count->open : $count->open = '');
            $data['status_id'] == 4 ? $count->turn_off++ : ($count->turn_off == 0 ? $count->turn_off : $count->turn_off = '');
            $data['status_id'] == 20 ? $count->refueling_start++ : ($count->refueling_start == 0 ? $count->refueling_start : $count->refueling_start = '');
            $data['status_id'] == 21 ? $count->refueling_end++ : ($count->refueling_end == 0 ? $count->refueling_end : $count->refueling_end = '');
            $data['status_id'] == 22 ? $count->aggregate_start++ : ($count->aggregate_start == 0 ? $count->aggregate_start : $count->aggregate_start = '');
            $data['status_id'] == 23 ? $count->aggregate_end++ : ($count->aggregate_end == 0 ? $count->aggregate_end : $count->aggregate_end = '');
         
        } else {
            $lastopen = $this->machinePerformanceRepo->getLastOpen();
            $lastturn = $this->machinePerformanceRepo->getLastTurn();
            $lastrefuelingstart = $this->machinePerformanceRepo->getLastRefuelingStart();
            $lastrefuelingend = $this->machinePerformanceRepo->getLastRefuelingEnd();
            $lastaggregatestart = $this->machinePerformanceRepo->getLastAggregateStart();
            $lastaggregateend = $this->machinePerformanceRepo->getLastAggregateEnd();

            $data['status_id'] == 3 ? $count->open = $lastopen->open + 1 : $count->open = '';
            $data['status_id'] == 4 ? $count->turn_off = $lastturn->turn_off + 1 : $count->turn_off = '';
            $data['status_id'] == 20 ? $count->refueling_start = $lastrefuelingstart->refueling_start + 1 : $count->refueling_start = '';
            $data['status_id'] == 21 ? $count->refueling_end = $lastrefuelingend->refueling_end + 1 : $count->refueling_end = '';
            $data['status_id'] == 22 ? $count->aggregate_start = $lastaggregatestart->aggregate_start + 1 : $count->aggregate_start = '';
            $data['status_id'] == 23 ? $count->aggregate_end = $lastaggregateend->aggregate_end + 1: $count->aggregate_end = '';
        }
        $data['status_id'] == 3 ? $count->start_count++ : $count->start_count;
        $data['status_id'] == 4 ? $count->stop_count++ : $count->stop_count;
        $data['status_id'] == 15 ? $count->sensro_inputs++ : $count->sensro_inputs;
        $data['status_id'] == 10 ? $count->machine_inputs_day++ : $count->machine_inputs_day;

        if ($machine == '捲料機1') {
            $data['status_id'] == 9 ? $count->second_completion++ : $count->second_completion;
            $data['status_id'] == 9 ? $count->machine_completion_day++ : $count->machine_completion_day;
        } else {
            $data['status_id'] == 10 ? $count->second_completion++ : $count->second_completion;
            $data['status_id'] == 10 ? $count->machine_completion_day++ : $count->machine_completion_day;
        }

        if ($count->resources_id == 0 || $data['orderno'] != $count->resource->orderno) {
            $count->machine_completion = 0;
            $count->machine_inputs = 0;
            if ($machine == '捲料機1') {   
                $data['status_id'] == 9 ? $count->machine_completion++ : $count->machine_completion;
            } else {
                $data['status_id'] == 10 ? $count->machine_completion++ : $count->machine_completion;
            }
            $data['status_id'] == 10 ? $count->machine_inputs++ : $count->machine_inputs;
        } else {
            $data['status_id'] == 10 ? $count->machine_inputs++ : $count->machine_inputs;

            if ($machine == '捲料機1') {   
                $data['status_id'] == 9 ? $count->machine_completion++ : $count->machine_completion;
                $data['status_id'] == 9 ? $count->processing_completion_time = $data['time'] : $count->processing_completion_time = "";
            } else {
                $data['status_id'] == 10 ? $count->machine_completion++ : $count->machine_completion;
                $data['status_id'] == 10 ? $count->processing_completion_time = $data['time'] : $count->processing_completion_time = "";
            }
            if ($machine == '捲料機1') {
                $data['status_id'] == 10 ? $count->processing_start_time = $data['time'] : $count->processing_start_time = "00:00:00";
            } else {
                if ($data['status_id'] == 10) {
                    $count->processing_start_time = $data['time'];
                } else {
                    $completion = $this->machinePerformanceRepo->getLastOpen($count);
                    $restart = $this->machinePerformanceRepo->findPreviousFirstStartCount($count);
                    if ($completion) {
                        if ($completion->machine_completion_day > 0) {
                            $count->processing_start_time = $completion->time;
                        } else {
                            if ($restart) {
                                $count->processing_start_time = $restart->time;
                            }
                        }
                    }
                }
            }
        }
    
        if (($data['orderno'] == '' && $data['date'] != $count['date']) || $count->resources_id == 0) {
            $count->serial_number_day = 1;
        } else {
            $count->serial_number_day++;
        }
        
        $count->date = $data->date;
        $count->time = $data->time;
        $count->machine = $machine;
       
        return $count;
    }

    public function restart($data)
    {
        $status = $this->counts($data);
        
        $findPreviousId = $this->rollerDataRepo->findPreviousId($data);
        
        is_null($findPreviousId) ? $findPreviousId['status_id'] = 0 : $findPreviousId->status_id;

        if ($status['open'] == '') {
            $status["restart_count"] = '';
        } else {
            if ($status['open'] == '1') {
                $status["restart_count"] = '';
            } else {
                
                if (($status->status_id == 3) && ($findPreviousId['status_id'] == 3)) {
                    $status["restart_count"] = ++$status->restart_count;
                } else {
                    $status["restart_count"] = '';
                }
            }
        }
        if ($status['turn_off'] == '') {
            $status['restop_count'] = '';
        } else {
            if ($status['turn_off'] == '1') {
                $status['restop_count'] = '';
            } else {
                if (($status->status_id == 4) && ($findPreviousId['status_id'] == 4)) {
                    $status['restop_count'] = ++$status->restop_count;
                } else {
                    $status['restop_count'] = '';
                }
            }
        }
       
        return $status;
    }

    public function machineT($data)
    {
        $status = $this->restart($data);
        $machinetime = $this->machinePerformanceRepo->findPreviousMachineInput($data, $status);
        $completionday = $this->machinePerformanceRepo->findCompletionDay($data, $status);
    
        $machineT = 0;
        $secondT = 0;
        $actual = 0;

        if ($status->machine == '捲料機1') {

            if ($data['status_id'] == 10) {
                if ($status->machine_inputs_day >= 2) {
                    if ($machinetime->processing_start_time) {
                        $machineT = strtotime($status->processing_start_time) - strtotime($machinetime->processing_start_time);
                    } else {
                        $machineT = 0;
                    }
                } else {
                    $machineT = 0;
                }
            } else {
                $machineT = 0;
            }
        } else {
            if ($data['status_id'] == 10) {
                $machineT = strtotime($status->processing_completion_time) - strtotime($status->processing_start_time);
            } else {
                $machineT = 0;
            }
        }
        
        $machintime = $this->machinePerformanceRepo->findCompletion($status);
        $previousmachinetime = $this->machinePerformanceRepo->findPreviousCompletion($status);
        $previousmachineday = $this->machinePerformanceRepo->findPreviousCompletionDay($status);
        
        if ($data['status_id'] == 9) {

            if ($status->machine_inputs_day >= 2) {
                if ($completionday == null) {
                    if ($previousmachinetime && $machintime) {
                        $secondT = strtotime($machintime->processing_completion_time) - strtotime($previousmachinetime->processing_completion_time);
                    } else {
                        if ($machintime == null) {
                            $secondT = strtotime($status->processing_completion_time) - strtotime($previousmachinetime->processing_completion_time);
                        }
                    }
                } else {
                    if ($previousmachineday) {
                        $secondT = strtotime($completionday->processing_completion_time) - strtotime($previousmachineday->processing_completion_time);
                    } else {
                        $secondT = strtotime($completionday->processing_completion_time) - strtotime(Carbon::today());
                    }
                }
            } else {
                $secondT = 0;
            }
        } else {
            $secondT = 0;
        }

        $status->roll_t = $machineT;
        $status->second_t = $secondT;
    
        if ($status->machine == '捲料機1') {
            if ($data['status_id'] == 9) {
                $actual = $status->second_t;
            } else {
                if ($status->machine == '捲料機2') {
                    if ($data['status_id'] == 10) {
                        $actual = $status->roll_t;
                    } else {
                        $actual = 0;
                    }
                } else {
                    $actual = 0;
                }
            }
        }

        $status->actual_processing = $actual;

        return $status;
    }

    public function standard($data)
    {
        $status = $this->machineT($data);

        $calculate262 = 0;
        $calculate263 = 0;
        $calculate363 = 0;

        if ($status['actual_processing'] == 0) {
            $calculate262 = 0;
        } else {
            if ($data->orderno == 'UAT-H-26-2') {
                $calculate262 = $status['actual_processing'];
            } else {
                $calculate262 = 0;
            }
        }

        if ($status['actual_processing'] == 0) {
            $calculate263 = 0;
        } else {
            if ($data->orderno == 'UAT-H-26-3') {
                $calculate263 = $status['actual_processing'];
            } else {
                $calculate263 = 0;
            }
        }

        if ($status['actual_processing'] == 0) {
            $calculate363 = 0;
        } else {
            if ($data->orderno == 'UAT-H-36-3') {
                $calculate363 = $status['actual_processing'];
            } else {
                $calculate363 = 0;
            }
        }

        $status['uat_h_26_2'] = $calculate262;
        $status['uat_h_26_3'] = $calculate263;
        $status['uat_h_36_3'] = $calculate363;

        $standard = StandardCt::where('orderno', $data['orderno'])->first();

        $standard262 = 0;
        $standard263 = 0;
        $standard363 = 0;

        if ($status['uat_h_26_2'] == 0) { 
            $standard262 = 0;
        } else {
            $standard262 = $standard->standard_ct;
        }

        if ($status['uat_h_26_3'] == 0) { 
            $standard263 = 0;
        } else {
            $standard263 = $standard->standard_ct;
        }

        if ($status['uat_h_36_3'] == 0) { 
            $standard363 = 0;
        } else {
            $standard363 = $standard->standard_ct;
        }

        $status['standard_uat_h_26_2'] = $standard262;
        $status['standard_uat_h_26_3'] = $standard263;
        $status['standard_uat_h_36_3'] = $standard363;

        return $status;
    }

    public function break($data)
    {
        $mainprogram = $this->rollerDataService->completion($data);
        $status = $this->standard($data);

        $time = array("08:00:00", "10:10:00", "12:00:00", "13:10:00", "15:10:00", "17:20:00", "17:50:00", "19:20:00", "19:30:00");
        $breaktime = "休息";

        $mainprogram->completion_status == '異常' ? strtotime($status->time) - strtotime($time[0]) < 0 && $data['status_id'] == 4 ? $breaktime = "休息" :
        (int) $status->time == 10 && strtotime($status->time) - strtotime($time[1]) <= 0 ? $breaktime = "休息" :
        (int) $status->time == 12 && strtotime($status->time) - strtotime($time[2]) <= 0 ? $breaktime = "休息" :
        (int) $status->time == 13 && strtotime($status->time) - strtotime($time[3]) <= 0 ? $breaktime = "休息" :
        (int) $status->time == 15 && strtotime($status->time) - strtotime($time[4]) <= 0 ? $breaktime = "休息" :
        strtotime($status->time) >= strtotime($time[5]) && strtotime($status->time) <= strtotime($time[6]) ? $breaktime = "休息" :
        strtotime($status->time) >= strtotime($time[7]) && strtotime($status->time) <= strtotime($time[8]) ? $breaktime = "休息" : 
        $breaktime = ""
        : $breaktime = "";
        /**
         * 這邊把變數$mainprogram的資料做合併
         * 
         */
        $status->description = $mainprogram->description;
        $status->type = $mainprogram->type;
        $status->abnormal = $mainprogram->abnormal;
        $status->message_status = $mainprogram->message_status;
        $status->completion_status = $mainprogram->completion_status;
        $status->break = $breaktime;
    
        return  $status;
    }

    public function worktime($data)
    {
        $status = $this->break($data);
        $before = $this->machinePerformanceRepo->findPreviousResourcesId($data);
        $time = date("08:00:00");
        $worktime = '0';

        if (isset($before)) {
            if ($before->resources_id == 0 || $before->resource->date != $data['date']) {
                (int) $status->time < (int) $time ? $worktime = '0' : $worktime = strtotime($status->time) - strtotime($time);
            } else {
                $before->time == "" ? $worktime = '0' : strtotime($status->time) - strtotime($before->time) < 0 ? $worktime = '0' : $worktime =  strtotime($status->time) - strtotime($before->time);
            }
        } else {
            $worktime = '0';
        }

        $worktime = date("H:i:s", $worktime - 8 * 3600); 

        $status->working_time = $worktime;
        return $status;
    }

    public function manufacturing($data)
    {
        $status = $this->worktime($data);
        $manufacture = '0';

        if ($status->serial_number_day < 10 && $status->open <= 1 && $data->date) { //當天且開機小於等於1
            $manufacture = '上班';
        } else {
            if ($data['status_id'] == 4 && $status->break == '休息') {
                $manufacture = '休息';
            } else {
                if ($data['status_id'] == 3) {
                    $manufacture = '開始生產';
                } else {
                    if ($data['status_id'] == 9 && $data['code'] == '500') {
                        $manufacture = "自動完工";
                    } else {
                        $manufacture = $status->completion_status;
                    }
                }
            }
        }

        $status->manufacturing_status = $manufacture;
        return $status;
    }

    public function downtime($data)
    {
        $status = $this->manufacturing($data);
        $worktime = strtotime($status->working_time) - strtotime(Carbon::today()); //轉秒數
        $status->restop_count ? $status->restop_count : $status['restop_count'] == 0;
        $beforeTime = 0;
        $sumTime = 0;

        if ($status->open == '') {
            if ($worktime > 180 && $data['status'] == 5) {
                $down_time = $status->working_time;
            } else {
                $down_time = '00:00:00';
            }
        } else {
            $beforeturn =  $this->machinePerformanceRepo->findTurnOff($status);
            $beforeopen =  $this->machinePerformanceRepo->findPreviousOpen($status);
            
            if ($status->open == 1) {
                $down_time = '00:00:00';
            } else {
                if ((isset($beforeturn) && $beforeturn->turn_off) ? $beforeturn->turn_off : $beforeturn['turn_off'] == 0) {
                    if ($beforeturn['turn_off'] > $status->open && $status->stop_count != '') {
                        if ($beforeopen->time ?  $beforeopen->time : $beforeopen['time'] == "00:00:00") {
                            $down_time = strtotime($status->time) - strtotime($beforeopen['time']);
                            $down_time = date("H:i:s", $down_time - 8 * 60 * 60);
                        }
                    } else {
                        if ((isset($beforeturn) && $beforeopen->time) ?  $beforeopen->time : $beforeopen['time'] == "00:00:00") {
                            if ($beforeopen['open'] > $beforeturn->turn_off && $status->restart_count == '') {

                                $restop = $this->machinePerformanceRepo->findReOpen();
                                $nowtime = $this->machinePerformanceRepo->findTurnOffEqualStopCount($data, $beforeopen, $restop);

                                foreach ($nowtime as $key => $data) {
                                    $beforeTime += strtotime($data->time) - strtotime(Carbon::today());
                                }

                                if ($beforeTime > 0 ? $beforeTime = date("H:i:s", $beforeTime - 8 * 60 * 60) : $beforeTime == '00:00:00') {
                                    $down_time = strtotime($status->time) - strtotime($beforeTime);
                                    $down_time = date("H:i:s", $down_time - 8 * 60 * 60);
                                }
                            } else {
                                if ($status->restart_count != '') {
                                    $down_time = '00:00:00';
                                } else {
                                    $nowtime = $this->machinePerformanceRepo->findTurnOffEqualStop($data, $status);
                                    foreach ($nowtime as $key => $data) {
                                        $sumTime += strtotime($data->time) - strtotime(Carbon::today());
                                    }

                                    if ($sumTime > 0 ? $sumTime = date("H:i:s", $sumTime - 8 * 60 * 60) : $sumTime == '00:00:00') {
                                        $down_time = strtotime($status->time) - strtotime($sumTime);
                                        $down_time = date("H:i:s", $down_time - 8 * 60 * 60);
                                    }
                                }
                            }
                        } else {
                            $down_time = '00:00:00';
                        }
                    }
                } else {
                    $down_time = '00:00:00';
                }
            }
        }
        $status->down_time = $down_time;

        $breaktime = $this->breaktime($status);

        $status->break_time = $breaktime;

        return $status;
    }

    public function breaktime($status)
    {
        $breaktime = '';
        $break =  $this->machinePerformanceRepo->findTurnOff($status);

        if ($break) {
            if ($break->break == '休息') {
                $breaktime = $status->down_time;
            } else {
                if ($status->break == '休息' && $status->down_time != '') {
                    $breaktime = $status->down_time;
                } else {
                    $breaktime = '00:00:00';
                }
            }
        } else {
            $breaktime = '00:00:00';
        }

        return $breaktime;
    }

    public function refue_time($data)
    {
        $status = $this->downtime($data);
        $findLessId =  $this->rollerDataRepo->findLessId($data);
        $findResourceId = $this->machinePerformanceRepo->findResourceId($findLessId);
        $findStopCount = $findResourceId->where('stop_count', $status->start_count)->get();

        $refue_time = '';
        $aggregate_time = '';
        if ($status->refueling_end == '') {
            $refue_time = '';
        } else {
            if ($findStopCount->count() == 0) {
                $refue_time = "00:00:00";
            } else {
                $sumtime = 0;
                foreach ($findStopCount as $sumitem) {     
                    $sumitemSec = strtotime($sumitem->down_time) - strtotime(Carbon::today());
                    $refue_time = $sumtime + $sumitemSec;
                }
            }
        }
        $findStartCount = $findResourceId->where('start_count', $status->stop_count)->get();

        if ($status->aggregate_end == '') {
            $aggregate_time = '';
        } else {
            if ($findStartCount->count() == 0) {
                $aggregate_time = "00:00:00";
            } else {
                $sumtime = 0;
                foreach ($findStartCount as $sumitem) {
                    $sumitemSec = strtotime($sumitem->down_time) - strtotime(Carbon::today());
                    $aggregate_time = $sumtime + $sumitemSec;
                }
            }
        }
	
       	$refue_time == ''? : $refue_time == "00:00:00" ? : $refue_time = date("H:i:s", $refue_time - 8 * 60 * 60);
	$aggregate_time == ''? :$aggregate_time =="00:00:00" ? : $refue_time = date("H:i:s", $aggregate_time - 8 * 60 * 60);
	
        $status->refueling_time = $refue_time;
        $status->aggregate_time = $aggregate_time;
	
        return $status;
    }

    public function refueling($data)
    {
        $status = $this->refue_time($data);
        $refueling = '00:00:00';
        $aggregate = '00:00:00';

        if ($status['refueling_end'] != 0) {
            $RefuelingStart = $this->machinePerformanceRepo->findRefuelingStart($status);
            if ($RefuelingStart) {
                $refueling = strtotime($status->time) - strtotime($RefuelingStart->time);
                $refueling = date("H:i:s", $refueling - 8 * 60 * 60);
            } else {
                $refueling = '00:00:00'; 
            }
        } else {
            $refueling = '00:00:00';
        }
        if ($status['aggregate_end'] != 0) {
            $AggregateStart = $this->machinePerformanceRepo->findAggregateStart($status);
            if ($AggregateStart) {
                $aggregate = strtotime($status->time) - strtotime($AggregateStart->time);
                $aggregate = date("H:i:s", $aggregate - 8 * 60 * 60);
            } else {
                $aggregate = '00:00:00'; 
            }
        } else {
            $aggregate = '00:00:00';
        }

        $status['refueler_time'] = $refueling;
        $status['collector_time'] = $aggregate;
      	
        $status->resources_id = $data->id;
        unset($status['resource']);
        
        return $status;
    }

    public function updateflag($data)
    {
        $check = $this->machinePerformanceRepo->check($data);
       
        if ($check) {
            $this->rollerDataRepo->updateflag($data);
        } else {
            dd($check);
        }
    }

    public function total($status)
    {
        $beforeID = $this->machinePerformanceRepo->findPreviousResourceId($status);
        $completion = $this->machinePerformanceRepo->findMachineCompletionDay($status);
        $sensro  = $this->machinePerformanceRepo->findPreviousInputDay($status);
        $sensro2 = $this->machinePerformanceRepo->findPreviousTwoInputDay($status);

        $sum = $status->machine_completion_day - $status->machine_inputs_day;
        $sensros  = $this->machinePerformanceRepo->findPreviousInputDaySubtractSum($status, $sum);
        $sensros2 = $this->machinePerformanceRepo->findPreviousTwoInputDaySubtractSum($status, $sum);

        is_null($beforeID) ? $beforeID['machine_completion_day'] = 0 : $beforeID->machine_completion_day;
        if ($status->machine_completion_day > $beforeID['machine_completion_day']  && $status->machine_completion_day != 1) {

            if ($status->machine_inputs_day - $status->machine_completion_day > 0) {

                if (isset($sensro)) {
                    if (strtotime($completion->processing_completion_time) - strtotime($sensro->processing_start_time) > 18) {
                        $total = strtotime($completion->processing_completion_time) - strtotime($sensro->processing_start_time);
                    } else {
                        if (isset($sensro2)) {
                            $total = strtotime($completion->processing_completion_time) - strtotime($sensro2->processing_start_time);
                        } else {
                            $total = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                        }
                    }
                } else {
                    if (strtotime($completion->processing_completion_time) > 18) {
                        $total = strtotime($completion->processing_completion_time);
                    } else {
                        if (isset($sensro2)) {
                            $total = strtotime($completion->processing_completion_time) - strtotime($sensro2->processing_start_time);
                        } else {
                            $total = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                        }
                    }
                }
            } else {
                if (isset($sensros)) { //$sensros存在
                    if (strtotime($completion->processing_completion_time) - strtotime($sensros->processing_start_time) > 18) {
                        $total = strtotime($completion->processing_completion_time) - strtotime($sensros->processing_start_time);
                    } else {
                        if (isset($sensros2)) {
                            $total = strtotime($completion->processing_completion_time) - strtotime($sensros2->processing_start_time);
                        } else {
                            $total = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                        }
                    }
                } else {  //$sensros不存在
                    if (strtotime($completion->processing_completion_time > 18)) {
                        $total = strtotime($completion->processing_completion_time);
                    } else {
                        if (isset($sensros2)) {
                            $total = strtotime($completion->processing_completion_time) - strtotime($sensros2->processing_start_time);
                        } else {
                            $total = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                        }
                    }
                }
            }
        } else {
            $total = 0;
        }
        $sensro3  = $this->machinePerformanceRepo->findPreviousThreeInputDay($status);
        $sensros3 = $this->machinePerformanceRepo->findPreviousThreeInputDaySubtractSum($status, $sum);

        if ($status->machine_completion_day > $beforeID['machine_completion_day'] && $status->processing_completion_time != "") {

            if ($total > 18 && $total < 28) {
                $CTtime = $total;
            } else {

                if ($status->machine_inputs_day > $status->machine_completion_day) {
                    if (isset($sensro3)) { //前面沒資料就不用相減了
                        $CTtime = strtotime($completion->processing_completion_time) - strtotime($sensro3->processing_start_time);
                    } else {
                        $CTtime = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                    }
                } else {
                    if (isset($sensros3)) { //前面沒資料就不用相減了
                        $CTtime = strtotime($completion->processing_completion_time) - strtotime($sensros3->processing_start_time);
                    } else {
                        $CTtime = strtotime($completion->processing_completion_time) - strtotime(Carbon::today());
                    }
                }
            }
        } else {
            $CTtime = 0;
        }
        $status->total_processing_time = $total;
        $status->ct_processing_time = $CTtime;
        return $status;
    }

    public function update($status)
    {
        $data = $this->total($status);
        $newdata = $data->toArray();
        unset($newdata['id']);
        $checkResourceId =  $this->machinePerformanceRepo->checkResourceId($newdata);

        if ($checkResourceId) {
            return $checkResourceId->update($newdata);
        }
    }
}
