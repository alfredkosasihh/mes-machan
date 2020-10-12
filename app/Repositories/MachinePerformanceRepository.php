<?php
    
    namespace App\Repositories;

    use App\Entities\Summary;

    class MachinePerformanceRepository 
    {
        public function findFirstOpen()
        {
            return Summary::where('open', '1')->first();
        }
       
        public function checkUnfinish($data)
        {
            $count = Summary::with('resource')->whereRaw('id = (select max(`id`) from summaries)')->first(); //前一筆資料
            if ($count == null) {
                return Summary::create(['resources_id' => 0, 'description' => '', 'processing_start_time' => '00:00:00', 'processing_completion_time' => '00:00:00']);
            } else {
                if ( $count->resource == null || $count->resource->date != $data['date']){
                    $count->open = 0;
                    $count->turn_off = 0;   
                    $count->start_count = 0;
                    $count->stop_count = 0;
                    $count->refueling_start = 0;
                    $count->refueling_end = 0;
                    $count->aggregate_start = 0;
                    $count->aggregate_end = 0;
                    $count->machine_completion_day = 0;
                    $count->machine_inputs_day = 0;
                    $count->sensro_inputs = 0;
                    $count->second_completion = 0;
                }
            }

            return $count;
        }

        public function getLastOpen()
        {
            return Summary::where('open', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getLastTurn()
        {
            return Summary::where('turn_off', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getLastRefuelingStart()
        {
            return Summary::where('refueling_start', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getLastRefuelingEnd()
        {
            return Summary::where('refueling_end', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getLastAggregateStart()
        {
            return Summary::where('aggregate_start', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getLastAggregateEnd()
        {
            return Summary::where('aggregate_end', '!=', '')->orderby('id', 'desc')->first();
        }

        public function getPreviousCompletion($count)
        {
            return Summary::where('machine_completion_day', $count->machine_completion_day - 1)->first();
        }

        public function findPreviousFirstStartCount($count)
        {
            return Summary::where('start_count', $count->start_count)->first(); //建一個累加開或關的
        }

        public function findPreviousMachineInput($data, $status)
        {
            return Summary::where('machine_inputs_day', $status['machine_inputs_day'] - 1)
            ->where('resources_id', '>', 0)->whereHas(
                'resource',
                function ($query) use ($data) {
                    $query->where('date', $data['date']);
                }
            )->first();
        }

        public function findCompletionDay($data, $status)
        {
            return Summary::where('machine_completion_day', $status['machine_inputs_day'])->where('resources_id', '>', 0)->whereHas(
                'resource',
                function ($query) use ($data) {
                    $query->where('date', $data['date']);
                }
            )->first();
        }

        public function findCompletion($status)
        {
            return Summary::where('machine_completion', $status['machine_completion'])->where('resources_id', '>', 0)->first();
        }

        public function findPreviousCompletion($status)
        {
            return Summary::where('machine_completion', $status['machine_completion'] - 1)->where('resources_id', '>', 0)->first();
        }

        public function findPreviousCompletionDay($status)
        {
            return Summary::where('machine_completion_day', $status['machine_completion_day'] - 1)->where('resources_id', '>', 0)->first();
        }

        public function findPreviousResourcesId($data)
        {
            return Summary::with('resource')->where('resources_id', $data['id'] - 1)->first();
        }

        public function findTurnOff($status)
        {
            return Summary::where('turn_off', $status->open)->first();
        }

        public function findPreviousOpen($status)
        {
            return Summary::where('open', $status->open - 1)->first(); //前一筆開機次數 因為沒有0
        }

        public function findReOpen()
        {
            return Summary::where('restop_count', '!=', 0)->orderby('time', 'desc')->first();
        }

        function findTurnOffEqualStopCount($data, $beforeopen, $restop)
        {
            return Summary::where('turn_off', $beforeopen['stop_count'] + $restop)->whereHas(
                'resource',
                function ($query) use ($data) {
                    $query->where('date', $data['date']);
                }
            )->get();
        }

        public function findTurnOffEqualStop($data, $status)
        {
            return Summary::where('turn_off', $status['stop_count'])->whereHas(
                'resource',
                function ($query) use ($data) {
                    $query->where('date', $data['date']);
                }
            )->get();
        }

        public function findResourceId($findLessId)
        {
            return Summary::whereIn('resources_id', $findLessId);
        }

        public function findRefuelingStart($status)
        {
            return Summary::where('refueling_start', $status['refueling_end'])->orderby('id', 'desc')->first();
        }

        public function findAggregateStart($status)
        {
            return Summary::where('aggregate_start', $status['aggregate_end'])->orderby('id', 'desc')->first();
        }

        public function create($data)
        {	
            return Summary::create($data);
        }

        public function check($data)
        {
            $check = Summary::where('resources_id', $data->id)->count();

            if ($check != 0) {
                return True;
            } else {
                return False;
            }
        }

        public function findPreviousResourceId($status)
        {
            return Summary::where('resources_id', $status->resources_id - 1)->first();
        }

        public function findMachineCompletionDay($status)
        {
            return Summary::where('machine_completion_day', $status->machine_completion_day)->where('resources_id', '>', 0)->first(); //找前面一筆相同的 顯示完工時間
        }

        public function findPreviousInputDay($status)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - 1)->where('resources_id', '>', 0)->first(); //Q4-1 = R
        }

        public function findPreviousTwoInputDay($status)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - 2)->where('resources_id', '>', 0)->first(); //Q4-2 = R
        }

        public function findPreviousInputDaySubtractSum($status, $sum)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - $sum - 1)->where('resources_id', '>', 0)->first(); //Q4-(Q-R)-1 = R
        }

        public function findPreviousTwoInputDaySubtractSum($status, $sum)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - $sum - 2)->where('resources_id', '>', 0)->first(); //Q4-(Q-R)-2 = R
        }

        public function findPreviousThreeInputDay($status)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - 3)->where('resources_id', '>', 0)->first(); //Q4-3 = R
        }

        public function findPreviousThreeInputDaySubtractSum($status, $sum)
        {
            return Summary::where('machine_inputs_day', $status->machine_completion_day - $sum - 3)->where('resources_id', '>', 0)->first(); //Q4-(Q-R)-3 = R
        }

        public function checkResourceId($newdata)
        {
            return Summary::where('resources_id', $newdata['resources_id'])->first();
        }

        public function searchdate($data)
        {
            return Summary::with('resource')->whereBetween('date', [$data['date_start'], $data['date_end']]);
        }
    }
?>
