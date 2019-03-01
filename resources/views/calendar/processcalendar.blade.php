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
        <h2>製程行事曆</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統管理</span>
            <span class="space-item">></span>
            <span class="space-item">製程行事曆<span>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">資料搜尋</div>
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('show-process-calendar') }}" method="GET">
                            <div class="form-group">
                                <label class="col-md-2 control-label">事業別</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="sel1" name="org-id" onchange="getResource()" required>
                                        <option selected>--- 請選擇廠別 ---</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">資源中心</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="sel2" name="id" required>  
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-md-2 control-label">調整月份</label>
                                <div class="col-md-10">
                                    <input id="sel3" name="date" class="form-control" type="month" required>
                                </div>
                            </div>
                            <hr>
                            <div style="text-align:center">
                                <button type="submit" id="sendBtn" class="btn btn-success btn-lg" style="width:45%">確認</button>
                                <button type="reset" onclick="resetOption()" class="btn btn-secondary btn-lg" style="width:45%">清除資料</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const getOrganization = () => {
        axios.get('{{ route('getorganization') }}')
            .then(({ data }) => {
                data.forEach(data => {
                    $('#sel1').append(`
                        <option value="${data.type}">${data.name}</option>
                    `);
                });
            });
    }

    const getResource = () => {
        axios.get('{{ route('getresource') }}', {
            params: {
                value: $('#sel1').val(),
            }
        })
        .then(({ data }) => {
            $('#sel2').empty();
            $('#sel2').append(`
                <option disabled selected value="">--- 請選擇 ---</option>
            `)
            data.forEach(data => {
                $('#sel2').append(`
                    <option value="${data.id}">${data.resource_name}</option>
                `);
            })
        });
    }

    const resetOption = () => {
        $('#sel2').empty();
        $('#sel2').append(`
            <option disabled selected value="">--- 請選擇 ---</option>
        `)
    }
    getOrganization();
</script>
@endsection