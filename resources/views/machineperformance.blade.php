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
            <span class="space-item">機台績效<span>
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
                                    <div class="col-md-1 textcenter">
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
                                        <select name="machine" id="machines"  class="clearable form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">物料代碼</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <input type="text" name="orderno" class="clearable form-control">
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