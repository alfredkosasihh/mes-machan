<?php
    namespace App\Repositories;
    use App\Entities\Resource;
    class RollerDataRepository
    {
        public function findFirstOpenId($findFirstOpen)
        {
            return Resource::where('id', $findFirstOpen->resources_id)->first();
        }

        public function findPreviousId($data)
        {
            return Resource::where('id', '<', $data['id'])->where('date', $data['date'])->orderby('id', 'desc')->first();
        }

        public function findId($data)
        {
            return Resource::where('id', '>', $data['id'])->first();
        }

        public function findLessId($data)
        {
            return Resource::where('id', '<=', $data->id)->where('date', $data['date'])->get(['id']);
        }

        public function updateFlag($data)
        {
            return Resource::where('id', $data->id)->update(['flag' => 1]);
        }

        public function data()
        {
            return Resource::where('flag', 0)->orderby('id')->get();
        }
    }

?>