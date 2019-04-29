<?php

namespace App\Repositories;

use App\Entities\MachineCategory;

class MachineCategoryRepository
{
    public function index()
    {
        return  MachineCategory::select('id', 'machine_id', 'machine_name', 'type', 'auto', 'interface')->paginate(100);
    }

    public function update($id, array $data)
    {
        $Machine = MachineCategory::find($id);

        if ($Machine) {
            return $Machine->update($data);
        }
        return false;
    }
    public function interface($data)
    {
        switch ($data['interface'])
        {
            case 'A': $data['interface']="可離線生產";
            break;
            case 'B': $data['interface']="人機同步生產";
            break;
            case 'C': $data['interface']="遠端遙控生產";
            break;
            case 'D': $data['interface']="無人化自動生產";
            break;
            case 'E': $data['interface']="人機手動";
            break;     
            default:
            return false;    
        }
        // dd($data);
           return $data;
    }
    public function identify($data){

        //  dd($data);
       
        $data['machine_id'] = $data['auto'];
        
        if(($data['auto_up']&&$data['auto_down'])==1)
        {
            $change='L';  
            $data['machine_id'] = $data['machine_id'].$change; 
        }
        elseif(($data['auto_up']&&$data['auto_down'])==0)
        {
            $change='U';  
            $data['machine_id'] = $data['machine_id'].$change;
        }
   
        switch ($data['type'])
        {
            case 'SS': 
            $change='S';
            $data['machine_id'] = $data['machine_id'].$change;
            break;
            case 'SM':   
            $change='M';
            $data['machine_id'] = $data['machine_id'].$change;
            break;
            case 'MS':  
            $change='S';
            $data['machine_id'] = $data['machine_id'].$change;
            break;
            case 'MM':   
            $change='M';
            $data['machine_id'] = $data['machine_id'].$change;
            break; 
            default:
            return false;    
        }
        if(($data['arrange']||$data['auto_arrange']||$data['auto_change']||$data['auto_pay']||$data['auto_finish'])==1)
        {
            $data['machine_id'] = $data['machine_id'] .'_';

            $arrange='p';
            $auto_arrange='s';
            $auto_change='t';
            $auto_pay='f';
            $auto_finish='d';
           
            $data['machine_id'] =  ($data['arrange']=="1")? $data['machine_id'].$arrange:$data['machine_id'];
            $data['machine_id'] =  ($data['auto_arrange']=="1")? $data['machine_id'].$auto_arrange:$data['machine_id'];
            $data['machine_id'] =  ($data['auto_change']=="1")? $data['machine_id'].$auto_change:$data['machine_id'];
            $data['machine_id'] =  ($data['auto_pay']=="1")? $data['machine_id'].$auto_pay:$data['machine_id'];
            $data['machine_id'] =  ($data['auto_finish']=="1")? $data['machine_id'].$auto_finish:$data['machine_id'];
            
        }
        
        return $data;

    }
}