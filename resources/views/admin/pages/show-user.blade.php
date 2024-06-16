@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            <a href="{{ route('reporter.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i> Trở lại</a>
                        </div>
                        <div class="card" style="border-radius:10px">
                            <div class="card-body text-center">
                                <h4 class="card-title m-b-0">Thông tin phóng viên: {{ $user->full_name }}</h4>
                            </div>
                            <div class="infomation mx-5">
                                <p class="text-dark"><span class="font-weight-bold">Tài khoản:</span> {{ $user->username }}</p>
                                <a class="text-dark link-custom" href="{{ route('reporter.report', ['id' => $user->id, 'report' => 'up']) }}"><p class="text-dark"><Span class="font-weight-bold">Đã up lên:</Span> {{ $user->count_send_approval }}</p></a>
                                <a class="text-dark link-custom" href="{{ route('reporter.report', ['id' => $user->id, 'report' => 'approval']) }}"><p class="text-dark"><Span class="font-weight-bold">Đã duyệt:</Span> {{ $user->count_approval }}</p></a>
                                <a class="text-dark link-custom" href="{{ route('reporter.report', ['id' => $user->id, 'report' => 'post']) }}"><p class="text-dark"><Span class="font-weight-bold">Đã đăng:</Span> {{ $user->count_push_post }}</p></a>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày đăng ký:</Span> {{ $user->created_at }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày cập nhật:</Span> {{ $user->updated_at }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Trạng thái:</Span> 
                                    @if ($user->status == 'active')
                                        <span class="badge badge-success">active</span>
                                    @else
                                        <span class="badge badge-danger">inactive</span>
                                    @endif
                                </p>
                                
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
@endsection