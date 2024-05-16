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
                                <p class="text-dark"><Span class="font-weight-bold">Tổng bài:</Span> {{ $user->total_posts }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Chưa duyệt:</Span> {{ $user->count_no_approval }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Đã duyệt:</Span> {{ $user->count_approval }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Đã đăng:</Span> {{ $user->count_push_post }}</p>
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