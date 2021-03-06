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
        <h2>班別設定</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統管理</span>
            <span class="space-item">></span>
            <span class="space-item">班別設定<span>
            <span class="space-item">></span>
            <span class="space-item">新增班別設定</span>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">新增資料</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('work-type.store') }}" method="POST" >
                            @csrf
                            <div class="form-group">
                                <label class="col-md-2 control-label">班別名稱</label>
                                <div class="col-md-10">
                                    <input name="work_name" class="form-control" required>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">班別類型</label>
                                <div class="col-md-10">
                                    <select name="work_type" class="form-control" id="work-type" onchange="getRestId()" required>
                                        <option disabled selected value="">--- 請選擇班別類型 ---</option>
                                        <option value="正常班">正常班</option>
                                        <option value="早班">早班</option>
                                        <option value="中班">中班</option>
                                        <option value="晚班">晚班</option>
                                        <option value="大夜班">大夜班</option>
                                        <option value="混合型">混合型</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">上班時間</label>
                                    <div class="col-md-4">
                                        <input type="time" class="form-control" id="start" name="work_time_start" required>
                                    </div>
                                    <div class="col-md-2" style="text-align:center;">
                                        <label style="margin:7px;"> ~ </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="time" class="form-control" id="end" name="work_time_end" required>
                                    </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">休息類別</label>
                                <div class="col-md-10">
                                    <select name="rest_id" class="form-control"  id="rest-id" required>
                                        <option disabled selected value="">--- 請選擇 ---</option>
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
    if ("{{ session('message') }}") {
        alert("{{ session('message') }}");
    }
    const getRestId = () => {
        axios.get("{{ route('rest-group') }}", {
            params: {
                value: $('#work-type').val()
            }
        })
        .then(({ data }) => {
            $('#rest-id').empty();
            $('#rest-id').append(`
                <option disabled selected value="">--- 請選擇 ---</option>
            `)
            data.forEach(data => {
                $('#rest-id').append(`
                    <option value="${data.id}">${data.rest_name}</option>
                `);
            })
        });
    }
    // const judgeTime = () => {
    //         let firstCondition = $('#start').val() >= $('#end').val();
    //         if ( firstCondition ) {
    //             alert('時間錯誤');
    //             return false;
    //         }
    // }
    
</script>
@endsection
