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
    .textcenter{
        text-align:center;
    }
</style>
@endsection

@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <h2>機台日績效統計</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統設定</span>
            <span class="space-item">></span>
            <span class="space-item">
            <a href="{{ route('show_dayperformance') }}">機台日績效統計</a><span>
            <span class="space-item">></span>
            <span class="space-item">{{$the_last_data['date_start']}}<span>
            <span class="space-item">~</span>
            <span class="space-item">{{$the_last_data['date_end']}}<span>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">資料編輯</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('search_dayperformance_date')}}"  method="GET">
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">機台日績效統計日期查詢</label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="date_start" id="date_start" class="clearable form-control" required>
                                    </div>
                                    <div class="col-md-1 textcenter">
                                        <label class="control-label"> ~ </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="date_end" id="date_end" class="clearable form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">機台</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <select name="machine_name" id="machines" value="" class="clearable form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-2 textcenter">
                                            <label class="control-label">物料代碼</label>
                                    </div>
                                    <div class="col-md-10 textcenter">
                                        <input type="text" name="material_name" id="material_name" class="clearable form-control">
                                    </div>
                                </div>
                                <hr>
                            <div style="text-align:center">
                                <button type="submit" onclick="" id="sendBtn" class="btn btn-success btn-lg" style="width:45%">確認</button>
                                <button type="reset" onclick="" class="btn btn-secondary btn-lg" style="width:45%">清除資料</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="total-data">載入筆數 | 共 {{$datas->total()}} 筆</div>
        <div style="margin-top:15px;">
            <table class="table table-striped table-pos">
                <thead class="thead-color">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">報工日期</th>
                        <th scope="col">班別</th>
                        <th scope="col">標準工時</th>
                        <th scope="col">總時數</th>
                        <th scope="col">機台代碼</th>
                        <th scope="col">機台名稱</th>
                        <th scope="col">生產類別</th>
                        <th scope="co1">製令單號</th>
                        <th scope="col">物料名稱</th>
                        <th scope="col">生產數量</th>
                        <th scope="col">機台加工次數</th>
                        <th scope="col">實際生產數量</th>
                        <th scope="col">標準應完工量</th>
                        <th scope="col">當天累計投入數</th>
                        <th scope="col">當天累計完工數</th>
                        <th scope="co1">不良數</th>
                        <th scope="col">標準加工秒數</th>
                        <th scope="col">標準上下料時間</th>
                        <th scope="col">量產時間</th>
                        <th scope="col">總停機時間</th>
                        <th scope="col">標準加工秒數</th>
                        <th scope="col">實際加工秒數</th>
                        <th scope="col">機台速度</th>
                        <th scope="co1">上下料時間</th>
                        <th scope="col">暖機(校正)時間</th>
                        <th scope="col">吊料時間</th>
                        <th scope="col">集料時間</th>
                        <th scope="col">休息時間</th>
                        <th scope="col">換模換線</th>
                        <th scope="col">物料品質不良處置時間</th>
                        <th scope="col">模具損壞換線時間</th>
                        <th scope="co1">程式修改時間</th>
                        <th scope="col">集會時間</th>
                        <th scope="col">環境整理整頓時間</th>
                        <th scope="col">除外工時合計</th>
                        <th scope="col">故障停機時間</th>
                        <th scope="col">機台保養時間</th>
                        <th scope="col">機台稼動率</th>
                        <th scope="col">性能稼動率</th>
                        <th scope="co1">良率</th>
                        <th scope="col">OEE</th>
                    </tr>
                </thead>
                <tbody>  
                        @foreach ($datas as $key =>$data)
                    <tr>     
                        <td>{{ ++$key + ($datas->currentPage() - 1) * 100 }}</td>
                        <td>{{ $data->report_work_date }}</td>
                        <td>{{ $data->work_name }}</td>
                        <td>{{ $data->standard_working_hours }}</td>
                        <td>{{ $data->total_hours }}</td>
                        <td>{{ $data->machine_code }}</td>
                        <td>{{ $data->machine_name }}</td>
                        <td>{{ $data->production_category }}</td>
                        <td>{{ $data->order_number }}</td>
                        <td>{{ $data->material_name }}</td>
                        <td>{{ $data->production_quantity }}</td>
                        <td>{{ $data->machine_processing }}</td>
                        <td>{{ $data->actual_production_quantity }}</td>
                        <td>{{ $data->standard_completion }}</td>
                        <td>{{ $data->total_input_that_day }}</td>
                        <td>{{ $data->total_completion_that_day }}</td>
                        <td>{{ $data->adverse_number }}</td>
                        <td>{{ $data->standard_processing }}</td>
                        <td>{{ $data->standard_updown }}</td>
                        <td>{{ $data->mass_production_time }}</td>
                        <td>{{ $data->total_downtime }}</td>
                        <td>{{ $data->standard_processing_seconds }}</td>
                        <td>{{ $data->actual_processing_seconds }}</td>
                        <td>{{ $data->machine_speed }}</td>
                        <td>{{ $data->updown_time }}</td>
                        <td>{{ $data->correction_time }}</td>
                        <td>{{ $data->hanging_time }}</td>
                        <td>{{ $data->aggregate_time }}</td>
                        <td>{{ $data->break_time }}</td>
                        <td>{{ $data->chang_model_and_line }}</td>
                        <td>{{ $data->bad_disposal_time }}</td>
                        <td>{{ $data->model_damge_change_line_time }}</td>
                        <td>{{ $data->program_modify_time }}</td>
                        <td>{{ $data->meeting_time }}</td>
                        <td>{{ $data->environmental_arrange_time }}</td>
                        <td>{{ $data->excluded_working_hours }}</td>
                        <td>{{ $data->machine_downtime }}</td>
                        <td>{{ $data->machine_maintain_time }}</td>
                        <td>{{ $data->machine_utilization_rate*100 }}%</td>
                        <td>{{ $data->performance_rate*100 }}%</td>
                        <td>{{ $data->yield*100 }}%</td>
                        <td>{{ $data->OEE*100 }}%</td>      
                    </tr>
                    @endforeach
                    {!! $datas->appends(request()->query())->links() !!}
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    window.onload = function(){
        document.getElementById('date_start').value = "{{$the_last_data['date_start']}}";
        document.getElementById('date_end').value = "{{$the_last_data['date_end']}}";
        document.getElementById('material_name').value = "{{$the_last_data['material_name']}}";
    }

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
            document.getElementById('machines').value = "{{$the_last_data['machine_name']}}";
        });
    }
    getMachineDefiniton();
</script>
@endsection