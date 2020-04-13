<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SummaryRepository;
use App\Repositories\ResourceRepository;
use App\Entities\DayPerformanceStatistics;
use App\Entities\Summary;
use App\Entities\Resource;
use Carbon\Carbon;

set_time_limit(0);
class DayPerformanceStatisticsController extends Controller
{
    protected $SummaryRepo;
    public function __construct(SummaryRepository $SummaryRepo)
    {
        $this->SumRepo = $SummaryRepo;
    }

    public function show(){
        return view('dayperformance');
    }

    public function searchdate()
    {
        $data = request()->only('date_start', 'date_end', 'material_name', 'machine_name');

        $datas = DayPerformanceStatistics::whereBetween('report_work_date' , [$data['date_start'], $data['date_end']]);

        if (!empty($data['machine_name'])){
            $datas = $datas->where('machine_name', $data['machine_name']);
        } else {
            $data['machine_name'] = "";
        }
        if (!empty($data['material_name'])){
            $datas = $datas->where('material_name', $data['material_name']);
        } else {
            $data['material_name'] = "";
        }
        
        $datas = $datas->paginate(100);
            if($datas){
                return view('searchdayperformance', ['datas' => $datas, 'the_last_data' => $data]);
            }
            return view('dayperformance');
    }

    public function getmachineperformance()
    {
        $today = Carbon::today()->format("Y-m-d");
        // $orderno = Resource::where('date', '2019-11-08')->select('orderno')->distinct()->get();
        $orderno = Resource::where('date', $today)->select('orderno')->distinct()->get();

        foreach($orderno as $key =>$datas){
            if($datas->orderno !== ''){
                $dayPerfor =  [];

                //製令資訊
                $dayPerfor['order_number'] = '';  // 製令單號
                $dayPerfor['material_name'] = $datas->orderno;   
                $dayPerfor['production_quantity'] = 1;   // 生產數量 
                
                $dayPerfor['report_work_date'] = $today; //$today
                $dayPerfor['work_name'] = $this->SumRepo->work_name($dayPerfor);     //跟oee一樣去抓是否有加班?setup_shifts->name, process_calendars
                $dayPerfor['standard_working_hours'] = $this->SumRepo->standard_working_hours($dayPerfor);
                $dayPerfor['total_hours'] = $this->SumRepo->total_hours($dayPerfor);   

                //機台代碼 機台名稱
                $dayPerfor['machine_name'] = $this->SumRepo->machine_name($dayPerfor);
                $dayPerfor['machine_code'] = $this->SumRepo->machine_code($dayPerfor);

                $dayPerfor['production_category'] = '量產';
                
                //標準ct
                $dayPerfor['standard_processing'] = $this->SumRepo->standard_processing($dayPerfor); 
                $dayPerfor['standard_updown'] =  $this->SumRepo->standard_updown($dayPerfor); 
                
                //機檯作業數量      machine_works_number
                $machine_works_number = $this->SumRepo->machine_works_number($dayPerfor);
                $dayPerfor = array_merge($dayPerfor, $machine_works_number);
                
                //機台加工時間      machine_processing_time
                $dayPerfor['mass_production_time'] = $hanging_time = $this->SumRepo->mass_production_time($dayPerfor); //因為真的太長所以分開寫...
                $machine_processing_time = $this->SumRepo->machine_processing_time($dayPerfor);
                $dayPerfor = array_merge($dayPerfor, $machine_processing_time);

                //機台嫁動除外工時   machinee_work_except_hours
                $machinee_work_except_hours = $this->SumRepo->machinee_work_except_hours($dayPerfor);
                $dayPerfor = array_merge($dayPerfor, $machinee_work_except_hours);

                //機台性能除外工時      performance_exclusion_time
                $performance_exclusion_time = $this->SumRepo->performance_exclusion_time($dayPerfor);
                $dayPerfor = array_merge($dayPerfor, $performance_exclusion_time);
        
                DayPerformanceStatistics::updateOrCreate(
                    [   'report_work_date' => $dayPerfor['report_work_date'], 
                        'material_name' => $dayPerfor['material_name']
                    ] ,
                    [   'standard_working_hours' => $dayPerfor['standard_working_hours'],
                        'total_hours' => $dayPerfor['total_hours'],
                        'production_quantity' => $dayPerfor['production_quantity'],
                        'work_name' => $dayPerfor['work_name'],
                        'machine_code' => $dayPerfor['machine_code'],
                        'machine_name' => $dayPerfor['machine_name'],
                        'production_category' => $dayPerfor['production_category'],
                        'standard_processing' => $dayPerfor['standard_processing'],
                        'standard_updown' => $dayPerfor['standard_updown'],
                        'machine_processing' => $dayPerfor['machine_processing'],
                        'actual_production_quantity' => $dayPerfor['actual_production_quantity'],
                        'total_input_that_day' => $dayPerfor['total_input_that_day'],
                        'standard_completion' => $dayPerfor['standard_completion'],
                        'total_completion_that_day' => $dayPerfor['total_completion_that_day'],
                        'adverse_number' => $dayPerfor['adverse_number'],
                        'mass_production_time' => $dayPerfor['mass_production_time'],
                        'total_downtime' => $dayPerfor['total_downtime'],
                        'standard_processing_seconds' => $dayPerfor['standard_processing_seconds'],
                        'actual_processing_seconds' => $dayPerfor['actual_processing_seconds'],
                        'machine_speed' => $dayPerfor['machine_speed'],
                        'updown_time' => $dayPerfor['updown_time'],
                        'correction_time' => $dayPerfor['correction_time'],
                        'hanging_time' => $dayPerfor['hanging_time'],
                        'aggregate_time' => $dayPerfor['aggregate_time'],
                        'break_time' => $dayPerfor['break_time'],
                        'chang_model_and_line' => $dayPerfor['chang_model_and_line'],
                        'bad_disposal_time' => $dayPerfor['bad_disposal_time'],
                        'model_damge_change_line_time' => $dayPerfor['model_damge_change_line_time'],
                        'program_modify_time' => $dayPerfor['program_modify_time'],
                        'meeting_time' => $dayPerfor['meeting_time'],
                        'environmental_arrange_time' => $dayPerfor['environmental_arrange_time'],
                        'excluded_working_hours' => $dayPerfor['excluded_working_hours'],
                        'machine_downtime' => $dayPerfor['machine_downtime'],
                        'machine_maintain_time' => $dayPerfor['machine_maintain_time'],
                        'machine_utilization_rate' => $dayPerfor['machine_utilization_rate'],
                        'performance_rate' => $dayPerfor['performance_rate'],
                        'yield' => $dayPerfor['yield'],
                        'OEE' => $dayPerfor['OEE'],
                    ] );
            }
            
        }
    }


    
    
}