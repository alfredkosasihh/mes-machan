@extends('layouts.myapp')

@section('css')
<style>
    tr th{
        text-align: center;
    }
    tr td{
        text-align: center;
    }
    div .a{
        text-align: right;
        padding-right:25%;
    }
    .space-item {
            margin-left: 10px;
    }

    .slectbtn{
        width:5%;
    }
    
    .breadcrumb-custom {
        background-color: #3D404C;
        width: 99%;
        margin:0px auto;
        padding: 15px 15px;
        margin-bottom: 20px;
        list-style: none;
        border-radius: 4px;
        color: #fff;
    }
    .total-data {
        width: 98%;
        margin:0px auto;
        /* padding: 0px 10px; */
    }
    .total-page {
        width: 98%;
        margin:2% 2% ;
    }
    .table-pos {
        margin: 0px auto;
        width: 98%;
    }
    .thead-color {
        background-color: #E85726;
        color: #fff;
        height: 10px;
    }
    .panel-default {
        border-color: #000000;
    }
    .panel-default > .panel-heading {
        color: #fff;
        background-color: #000000;
        border-color: #000000;
    }
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .textcenter {
        text-align:center;
    }
    .m-t-15{
        margin-top: 15px;
    }
</style>
@endsection

@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <h2>機台績效</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統設定</span>
            <span class="space-item">></span>
            <a href="{{ route('show_machineperformance') }}">機台績效</a><span>
            <span class="space-item">></span>
            <span class="space-item">{{ $data['date_start']}}<span>
            <span class="space-item">~</span>
            <span class="space-item">{{ $data['date_end']}}<span>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">資料編輯</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('search_machineperformance')}}"  method="GET">    
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                        <label class="control-label">機台績效日期查詢</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="date_start" class="clearable form-control" required>
                                    </div>
                                    <div class="col-md-2 textcenter">
                                        <label class="control-label"> ~ </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="date_end" class="clearable form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">訊息狀態</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <select name="message_status"  class="clearable form-control">
                                            <option disabled selected>---請選擇---</option>
                                            <option value="訂單開始">訂單開始</option>
                                            <option value="訂單結束">訂單結束</option>
                                            <option value="外部停止">外部停止</option>
                                            <option value="開機">開機</option>
                                            <option value="關機">關機</option>
                                            <option value="X伺服馬達異常">X伺服馬達異常</option>
                                            <option value="二次元異常開始">二次元異常開始</option>
                                            <option value="二次元異常結束">二次元異常結束</option>
                                            <option value="送料異常開始">送料異常開始</option>
                                            <option value="送料異常結束">送料異常結束</option>
                                            <option value="二次元成品完成">二次元成品完成</option>
                                            <option value="送料機成品完成">送料機成品完成</option>
                                            <option value="二次元連線開始">二次元連線開始</option>
                                            <option value="二次元連線結束">二次元連線結束</option>
                                            <option value="送料機連線開始">送料機連線開始</option>
                                            <option value="送料機連線結束">送料機連線結束</option>
                                            <option value="Sensro1">Sensro1</option>
                                            <option value="Sensro2">Sensro2</option>
                                            <option value="成品數量到達">成品數量到達</option>
                                            <option value="送料機換料開始">送料機換料開始</option>
                                            <option value="送料機換料結束">送料機換料結束</option>
                                            <option value="集料轉移開始">集料轉移開始</option>
                                            <option value="集料轉移結束">集料轉移結束</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">完工狀態</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <select name="completion_status" class="clearable form-control">
                                            <option disabled selected>---請選擇---</option>
                                            <option value="正常生產">正常生產</option>
                                            <option value="異常">異常</option>
                                            <option value="不正常">不正常</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">生產狀態</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <select name="manufacturing_status" class="clearable form-control">
                                        <option disabled selected>---請選擇---</option>
                                            <option value="上班">上班</option>
                                            <option value="休息">休息</option>
                                            <option value="異常">異常</option>
                                            <option value="不正常">不正常</option>
                                            <option value="開始生產">開始生產</option>
                                            <option value="自動完工">自動完工</option>
                                            <option value="正常生產">正常生產</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">機台</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <select name="machine" id="machines" class="clearable form-control">   
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">時間區間</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="time_start" class="clearable form-control">
                                            <option disabled selected>---請選擇---</option>
                                            <option value="01:00">01:00</option>
                                            <option value="02:00">02:00</option>
                                            <option value="03:00">03:00</option>
                                            <option value="04:00">04:00</option>
                                            <option value="05:00">05:00</option>
                                            <option value="06:00">06:00</option>
                                            <option value="07:00">07:00</option>
                                            <option value="08:00">08:00</option>
                                            <option value="09:00">09:00</option>
                                            <option value="10:00">10:00</option>
                                            <option value="11:00">11:00</option>
                                            <option value="12:00">12:00</option>
                                            <option value="13:00">13:00</option>
                                            <option value="14:00">14:00</option>
                                            <option value="15:00">15:00</option>
                                            <option value="16:00">16:00</option>
                                            <option value="17:00">17:00</option>
                                            <option value="18:00">18:00</option>
                                            <option value="19:00">19:00</option>
                                            <option value="20:00">20:00</option>
                                            <option value="21:00">21:00</option>
                                            <option value="22:00">22:00</option>
                                            <option value="23:00">23:00</option>
                                            <option value="24:00">24:00</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 textcenter">
                                        <label class="control-label"> ~ </label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="time_end" class="clearable form-control">
                                        <option disabled selected>---請選擇---</option>
                                            <option value="01:00">01:00</option>
                                            <option value="02:00">02:00</option>
                                            <option value="03:00">03:00</option>
                                            <option value="04:00">04:00</option>
                                            <option value="05:00">05:00</option>
                                            <option value="06:00">06:00</option>
                                            <option value="07:00">07:00</option>
                                            <option value="08:00">08:00</option>
                                            <option value="09:00">09:00</option>
                                            <option value="10:00">10:00</option>
                                            <option value="11:00">11:00</option>
                                            <option value="12:00">12:00</option>
                                            <option value="13:00">13:00</option>
                                            <option value="14:00">14:00</option>
                                            <option value="15:00">15:00</option>
                                            <option value="16:00">16:00</option>
                                            <option value="17:00">17:00</option>
                                            <option value="18:00">18:00</option>
                                            <option value="19:00">19:00</option>
                                            <option value="20:00">20:00</option>
                                            <option value="21:00">21:00</option>
                                            <option value="22:00">22:00</option>
                                            <option value="23:00">23:00</option>
                                            <option value="24:00">24:00</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                            <div class="textcenter">
                                <button type="submit" onclick="" id="sendBtn" class="btn btn-success btn-lg" style="width:45%">確認</button>
                                <button type="reset" onclick="" class="btn btn-secondary btn-lg" style="width:45%">清除資料</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="total-data">載入筆數 | 共 {{$datas->total()}}  筆</div>
        <div clsss="m-t-15">
            <table class="table table-striped table-pos">
                <thead class="thead-color">
                    <tr>
                        <th scope="col">序</th>
                        <th scope="col">機台</th>
                        <th scope="col">程序說明</th>
                        <th scope="col">單元</th>
                        <th scope="col">異常狀態</th>
                        <th scope="col">No</th>
                        <th scope="col">當天序號</th>
                        <th scope="col">開機</th>
                        <th scope="co1">關機</th>
                        <th scope="col">當天日期</th>
                        <th scope="col">時間</th>
                        <th scope="col">機台累計完工數</th>
                        <th scope="col">機台累計投入數</th>
                        <th scope="col">當天完工數</th>
                        <th scope="col">當天投入數</th>
                        <th scope="col" >Sensor投入</th>
                        <th scope="col">休息</th>
                        <th scope="co1">休息時間</th>
                        <th scope="col">訊息狀態</th>
                        <th scope="col">停機時間</th>
                        <th scope="col">完工狀態</th>
                        <th scope="col">加工總工時參數</th>
                        <th scope="col">二次元完工數</th>
                        <th scope="col">生產狀態</th>
                        <th scope="col">加工工件開始時間</th>
                        <th scope="co1">加工工件完工時間</th>
                        <th scope="col">作業時間</th>
                        <th scope="col">捲料T/T</th>
                        <th scope="col">二次元T/T</th>
                        <th scope="col">CT(加工時間)秒</th>
                        <th scope="col">實際加工時間</th>
                        <th scope="col">重複開始次數</th>
                        <th scope="col">重複停機次數</th>
                        <th scope="co1">累計開始</th>
                        <th scope="col">累計結束</th>
                        <th scope="col">換料開始</th>
                        <th scope="col">換料結束</th>
                        <th scope="col">換料時間</th>
                        <th scope="col">換料器時間</th>
                        <th scope="col">集料開始</th>
                        <th scope="col">集料結束</th>
                        <th scope="co1">集料時間</th>
                        <th scope="col">集料器時間</th>
                    </tr>
                </thead>
                <tbody>  
                        @foreach ($datas as $key =>$data)
                    <tr>     
                    @if ($data->id>1)
                        <td>{{ ++$key + ($datas->currentPage() - 1) * 100 }}</td>
                        <td>{{ $data->machine }}</td>
                        <td>{{ $data->description }}</td>
                        <td>{{ $data->type }}</td>
                        <td>{{ $data->abnormal }}</td>
                        <td>{{ $data->serial_number }}</td>
                        <td>{{ $data->serial_number_day }}</td>
                        <td>{{ $data->open }}</td>
                        <td>{{ $data->turn_off }}</td>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->time }}</td>
                        <td>{{ $data->machine_completion }}</td>
                        <td>{{ $data->machine_inputs }}</td>
                        <td>{{ $data->machine_completion_day }}</td>
                        <td>{{ $data->machine_inputs_day }}</td>
                        <td>{{ $data->sensro_inputs }}</td>
                        <td>{{ $data->break }}</td>
                        <td>{{ $data->break_time }}</td>
                        <td>{{ $data->message_status }}</td>
                        <td>{{ $data->down_time }}</td>
                        <td>{{ $data->completion_status }}</td>
                        <td>{{ $data->total_processing_time }}</td>
                        <td>{{ $data->second_completion }}</td>
                        <td>{{ $data->manufacturing_status }}</td>
                        <td>{{ $data->processing_start_time }}</td>
                        <td>{{ $data->processing_completion_time }}</td>
                        <td>{{ $data->working_time }}</td>
                        <td>{{ $data->roll_t }}</td>
                        <td>{{ $data->second_t }}</td>
                        <td>{{ $data->ct_processing_time }}</td>
                        <td>{{ $data->actual_processing }}</td>
                        <td>{{ $data->restart_count }}</td>
                        <td>{{ $data->restop_count }}</td>
                        <td>{{ $data->start_count }}</td>
                        <td>{{ $data->stop_count }}</td>
                        <td>{{ $data->refueling_start }}</td>
                        <td>{{ $data->refueling_end }}</td>
                        <td>{{ $data->refueling_time }}</td>
                        <td>{{ $data->refueler_time }}</td>
                        <td>{{ $data->aggregate_start }}</td>
                        <td>{{ $data->aggregate_end }}</td>
                        <td>{{ $data->aggregate_time }}</td>
                        <td>{{ $data->collector_time }}</td>                       
                    </tr>
                    @endif
                    @endforeach
                    {!! $datas->appends(request()->query())->links() !!}
            </table>
        </div>
    </div>
</div>
<script>
    const getMachineDefiniton = () => {
        axios.get("{{ route('getMachineDefinition') }}")
        .then(({ data }) => {
            $('#machines').empty();
            $('#machines').append(`
                <option disabled selected value="">--- 請選擇 ---</option>
            `)
            data.forEach(data => {
                $('#machines').append(`
                    <option value="${data.machine_name}">${data.machine_name}</option>
                `);
            })
        });
    }
    getMachineDefiniton();
</script>
@endsection