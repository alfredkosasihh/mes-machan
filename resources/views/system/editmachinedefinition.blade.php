@extends('layouts.myapp')

@section('css')
<style>
    .space-item {
        margin-left: 10px;
    }
    .panel-default {
        border-color: #000000;
    }
    .panel-default > .panel-heading {
        color: #fff;
        background-color: #000000;
        border-color: #000000;
    }
    .form-horizontal .control-label {
        text-align: center;
    }
    hr {
        border-top: 1px solid #ccc;
    }
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-secondary:hover {
        color: #fff;
        background-color: #5a6268;
        border-color: #545b62;
    }
    .btn.focus, .btn:focus, .btn:hover {
        color: #fff;
    }
</style>

@endsection

@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <h2>機台定義</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統設定</span>
            <span class="space-item">></span>
            <span class="space-item">機台定義<span>
            <span class="space-item">></span>
            <span class="space-item">資料編輯頁</span>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">資料編輯</div>
                    <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('machine-definition.update', $datas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                            <div class="form-group">
                                <label class="col-md-2 control-label">機台名稱</label>
                                <div class="col-md-10">
                                    <input name="machine_name" class="clearable form-control" value="{{$datas->machine_name}}" required>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">機台類別</label>
                                <div class="col-md-10">
                                        <select id="machine-name"  name = "machine_category" class="form-control"  required>
                                        </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">製程別</label>
                                <div class="col-md-10">
                                    <select id="ApsProcessCode"  name = "aps_process_code"  class="form-control" required> 
                                    </select>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">班別</label>
                                <div class="col-md-10">
                                    <select  class="form-control" id="work-type"  name="group_setting" onchange="getRestId()" required>
                                        <option value="正常班" {{ $datas->group_setting === '正常班' ? 'selected' : '' }}>正常班</option>
                                        <option value="早班" {{ $datas->group_setting === '早班' ? 'selected' : '' }}>早班</option>
                                        <option value="中班" {{ $datas->group_setting === '中班' ? 'selected' : '' }}>中班</option>
                                        <option value="晚班" {{ $datas->group_setting === '晚班' ? 'selected' : '' }}>晚班</option>
                                        <option value="大夜班" {{ $datas->group_setting === '大夜班' ? 'selected' : '' }}>大夜班</option>
                                        <option value="混合型" {{ $datas->group_setting === '混合型' ? 'selected' : '' }}>混合型</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                    <label class="col-md-2 control-label">標準換線(分)</label>
                                    <div class="col-md-10">
                                        <input name="change_line_time" class="clearable form-control" value="{{$datas->change_line_time	}}" required>
                                    </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">休息類別</label>
                                <div class="col-md-10">
                                    <select name="class_assign" class="form-control"  id="rest-id" required>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">適用OEE</label>
                                <div class="col-md-10">
                                    <select name="oee_assign" class="form-control" required>
                                        <option value ="雷射專用" {{ $datas->oee_assign === '雷射專用' ? 'selected' : '' }}>雷射專用</option>
                                        <option value ="P1&NCT專用" {{ $datas->oee_assign === 'P1&NCT專用' ? 'selected' : '' }}>P1&NCT專用</option>   
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div style="text-align:center">
                                <button type="submit" id="sendBtn" class="btn btn-success btn-lg" style="width:45%">確認</button>
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
    let firsta = true;
    let firstb = true;
    let firstc = true;
    const getRestId = () => {
        axios.get("{{ route('rest-group') }}", {
            params: {
                value: $('#work-type').val()
            }
        })
        .then(({ data }) => {

            $("#rest-id").empty();
            if (firsta) {
                $("#rest-id").append(`
                    <option disabled selected ">{{$datas->Rest->rest_name}}</option>
                `)
                firsta = !firsta
            }
            data.forEach(data => {
                $('#rest-id').append(`
                    <option value="${data.id}">${data.rest_name}</option>
                `);
            })
        });
    }
    getRestId();
   
    const getMachineId = () => {
        
        axios.get("{{ route('getMachineData') }}", {
        })
        .then(({ data }) => {

            $("#machine-name").empty();
            if (firstb) {
                $("#machine-name").append(`
                    <option disabled selected >{{ $datas->machine_category_name }}</option>
                `)
                firstb = !firstb
            }
            data.forEach(data => {
                $('#machine-name').append(`
                    <option value="${data.id}"> ${data.machine_name}</option>
                `);
            })
        });  
    }
    getMachineId();

    const getApsProcessCode = () => {
        
        axios.get("{{ route('getApsData') }}", {
        })
        .then(({ data }) => {

            $("#ApsProcessCode").empty();
            if (firstc) {
                $("#ApsProcessCode").append(`
                    <option disabled selected >{{$datas->process_description}}</option>
                `)
                firstc = !firstc
            }
            data.forEach(data => {
                $('#ApsProcessCode').append(`
                    <option value="${data.aps_id}"> ${data.process_routing_name}</option>
                `);
            })
        });  
    }
    getApsProcessCode();
</script>
@endsection