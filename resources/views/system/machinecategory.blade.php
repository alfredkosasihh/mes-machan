@extends('layouts.myapp')

@section('css')
<style>
    tr th {
        text-align: center;
    }

    tr td {
        text-align: center;
    }

    div .a {
        text-align: right;
        padding-right: 25%;
    }

    .space-item {
        margin-left: 10px;
    }

    .slectbtn {
        width: 5%;
    }

    .breadcrumb-custom {
        background-color: #3D404C;
        width: 99%;
        margin: 0px auto;
        padding: 15px 15px;
        margin-bottom: 20px;
        list-style: none;
        border-radius: 4px;
        color: #fff;
    }

    .total-data {
        width: 98%;
        margin: 0px auto;
        /* padding: 0px 10px; */
    }

    .total-page {
        width: 98%;
        margin: 2% 2%;
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
</style>
@endsection

@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <h2>機台類別定義</h2>
        <ol class="breadcrumb">
            <img src="{{ asset('img/u12.png') }}">
            <span class="space-item">系統設定</span>
            <span class="space-item">></span>
            <span class="space-item">機台類別定義<span>
        </ol>
        <div class="breadcrumb-custom">
            <span>資料列表</span>
            <div style="float:right; margin-top:-7px">
                <a href="{{route('machine-category.create')}}">
                    <button class="btn btn-success">新增</button>
                </a>
            </div>
        </div>
        <div class="total-data">
                載入筆數 |
                <span id="data-num"></span>
        </div>
        <div style="margin-top:15px;">
            <table class="table table-striped table-pos" id="machine-category-table">
                <thead class="thead-color">
                    <tr>
                        <th scope="col">序</th>
                        <th scope="co1">機台類別定義</th>
                        <th scope="col">機台類別</th>
                        <th scope="col">單機 / 多機</th>
                        <th scope="col">自動化</th>
                        <th scope="col">人機介面</th>
                        <th scope="col">操作</th>

                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div style="text-align:right">
                <span style="display: inline-block; margin-top: 27px;">
                    <span>每頁顯示筆數</span>
                    <select id="amount" onchange="MachineCategoryIndex(); $('#pagination-demo').twbsPagination('destroy');">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </span>
                <ul id="pagination-demo" class="pagination-sm" style="vertical-align: top;"></ul>
            </div>
        </div>
    </div>
    <script>
        function checkyn() {
            var check = confirm("是否要刪除該筆資料");
            if (check) {
                return true;
            } else {
                return false;
            }
        }
        const MachineCategoryIndex = (page = 1) => {
        const amount = $('#amount').val();
        axios.get("{{ route('MachineCategoryIndex') }}",{
            params: {
                amount,
                page,
            }
        }).then(({ data }) => {
            lastPage = data.last_page;       
            const datas = data.data; 
            $('#data-num').text(`共 ${data.total} 筆`);
            $('#machine-category-table tbody').empty();
            datas.forEach((data, key) => {
                $('#machine-category-table tbody').append(`
                    <tr>
                        <th scope="row">${key + 1 + (page - 1) * amount}</th>
                        <td>${data.machine_id}</td>
                        <td>${data.machine_name}</td>
                        <td>${data.type}</td>
                        <td>${data.auto}</td>
                        <td>${data.interface}</td>
                        <td>
                        <a href="machine-category/${data.id}/edit" class="btn btn-primary">編輯</a>
                            <form action="machine-category/${data.id}" onsubmit="return checkyn()"  style="display:inline-block" method ="POST"> 
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger">刪除</button>
                            </form>
                        </td>     
                    </tr>
                `)
            });
            $('#pagination-demo').twbsPagination({
                totalPages: lastPage,
                visiblePages: 5,
                first:'頁首',
                last:'頁尾',
                prev:'<',
                next:'>',
                initiateStartPageClick: false,
                onPageClick: function (event, page) {
                    MachineCategoryIndex(page)
                }
            });
        });
    }
    MachineCategoryIndex();
    </script>
    @endsection