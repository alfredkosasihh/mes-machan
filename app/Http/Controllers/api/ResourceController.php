<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Repositories\MachinePerformanceRepository;
use App\Repositories\RollerDataRepository;
use App\Services\MachinePerformanceService;
set_time_limit(0);
class ResourceController extends Controller
{
    protected $machinePerformanceRepo;
    protected $rollerDataRepo;
   
    public function __construct(MachinePerformanceRepository $machinePerformanceRepo, MachinePerformanceService $machService,
                                RollerDataRepository $rollerDataRepo)
    {
        $this->machinePerformanceRepo = $machinePerformanceRepo;
        $this->machService = $machService;
        $this->rollerDataRepo = $rollerDataRepo;
    }

    public function show()
    {
        return view('machineperformance');
    }

    public function searchdate()
    {
        $data = request()->only('date_start', 'date_end','time_start', 'time_end', 'completion_status', 'message_status', 'manufacturing_status', 'machine', 'orderno');
        
        $datas = $this->machinePerformanceRepo->searchdate($data);
        
     
        if (!empty($data['message_status'])){
            $datas = $datas->where('message_status', $data['message_status']);
        } else {
            $data['message_status'] = "";
        }
        
        if (!empty($data['completion_status'])){
            $datas = $datas->where('completion_status', $data['completion_status']);
        } else {
            $data['completion_status'] = "";
        }

        if (!empty($data['manufacturing_status'])){
            $datas = $datas->where('manufacturing_status', $data['manufacturing_status']);
        } else {
            $data['manufacturing_status'] = "";
        }

        if (!empty($data['machine'])){
            $datas = $datas->where('machine', $data['machine']);
        } else {
            $data['machine'] = "";
        }

        if (!empty($data['time_start'])){
            $datas = $datas->where('time', '>=', $data['time_start']);
        } else {
            $data['time_start'] = "";
        }

        if (!empty($data['time_end'])){
            $datas = $datas->where('time', '<=', $data['time_end']);
        } else {
            $data['time_end'] = "";
        }

        if (!empty($data['time_start']) && !empty($data['time_end'])){
            $datas = $datas->whereBetween('time', [$data['time_start'], $data['time_end']]);
        } 

        if(!empty($data['orderno'])){
            $datas->whereHas('resource',function ($query) use ($data){
                    $query->where('orderno', $data['orderno']);
            });
        } else {
            $data['orderno'] = "";
        }
        
        $datas = $datas->paginate(100);
        
        if($datas){
            return view('searchmachineperformance', ['datas' => $datas, 'the_last_data' => $data]);
        }
        return view('machineperformance');
    }

    public function fixmachinedatabase()
    { 
        $parme = $this->rollerDataRepo->data();

        foreach ($parme as $parmas) {
            
            $status = $this->machService->refueling($parmas);
            
            $this->machinePerformanceRepo->create($status->toArray());
            
            $this->machService->updateflag($parmas);

            $updat = $this->machService->update($status);   
        }
        return response()->json(['status' => $updat]);
    }
}
