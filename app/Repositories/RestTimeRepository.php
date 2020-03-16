<?php

namespace App\Repositories;

use App\Entities\RestGroup;
use Carbon\Carbon;

class RestTimeRepository
{
    public function create(array $data)
    {
        $reslut = RestGroup::create([
            'rest_name' => $data['work_name'],
            'work_type' => $data['work_type'],
        ]);
        foreach ($data['type'] as $key => $value) {
            $reslut->restSetup()->create([
                'start' => $data['rest_time_start'][$key],
                'end' => $data['rest_time_end'][$key],
                'remark' => $data['rest_remark'][$key],
                'type' => $data['type'][$key]
            ]);
        }
    }

    public function restTimeData($amount)
    {
        return RestGroup::paginate($amount);
    }

    public function find($id)
    {
        return RestGroup::with('restSetup')->find($id);
    }

    public function destroy($id)
    {
        $datas = RestGroup::find($id)->shifts()->get();
        foreach ($datas as $key => $data) {
            $data->relatedCompany()->delete();
            $data->relatedProcess()->delete();
        }
        RestGroup::find($id)->restSetup()->delete();
        RestGroup::find($id)->shifts()->delete();
        return RestGroup::destroy($id);
    }

    public function deleteData($id, $restId)
    {
        RestGroup::find($id)->restSetup()->where('id', $restId)->delete();
    }

    public function update(array $data, $id)
    {
        $rest = RestGroup::find($id);
        return $rest ? $rest->update($data) : false;
    }

    public function updateData(array $data, $id, $restId)
    {
        $timeStart = Carbon::createFromTime(explode(':', $data['start'])[0], explode(':', $data['start'])[1]);
        $timeEnd = Carbon::createFromTime(explode(':', $data['end'])[0], explode(':', $data['end'])[1]);
        $limitTimes = RestGroup::find($restId)->restSetup()->where('id', '<>', $id)->get(['start', 'end']);
        $timeStatus =  $this->judgeTime($timeStart, $timeEnd, $limitTimes);
        $checkExists = RestGroup::find($restId)
            ->shifts()->where(function ($query) use ($data) {
                $query->where('work_on', '>=', $data['start'])
                    ->orwhere('work_off', '<=', $data['end']);
            })->exists();
        if (!$timeStatus || $checkExists) {
            return false;
        }
        return RestGroup::find($restId)->restSetup()->where('id', $id)->update($data);
    }

    public function createData(array $data , $id)
    {
        $timeStart = Carbon::createFromTime(explode(':', $data['start'])[0], explode(':', $data['start'])[1]);
        $timeEnd = Carbon::createFromTime(explode(':', $data['end'])[0], explode(':', $data['end'])[1]);
        $limitTimes = RestGroup::find($id)->restSetup()->get(['start', 'end']);
        $timeStatus = $this->judgeTime($timeStart, $timeEnd, $limitTimes);
        dd($timeStatus);
        $checkExists = RestGroup::find($id)
            ->shifts()->where(function ($query) use ($data) {
                $query->where('work_on', '>=', $data['start'])
                    ->orwhere('work_off', '<=', $data['end']);
            })->exists();
        if (!$timeStatus || $checkExists) {
            return false;
        }
        return RestGroup::find($id)->restSetup()->create($data);
    }

    private function judgeTime($timeStart, $timeEnd, $limitTimes)
    {
        dd($timeStart, $timeEnd, $limitTimes);
        if ($timeStart > $timeEnd || $timeStart == $timeEnd) {
            return false;
        }     
        foreach ($limitTimes as $key => $limitTime){
            $start = Carbon::parse($limitTime->start);    
            $end =  Carbon::parse($limitTime->end);
            if($timeStart->between($start, $end, false) || $timeEnd->between($start, $end, false) || ($timeStart < $start && $timeEnd > $start)){
                return false;
            } elseif (($timeStart == $start || $timeEnd == $end)) {
                return false;
            }
        }
        return true;
    }
}
