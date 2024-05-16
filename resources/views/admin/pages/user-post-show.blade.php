@extends('admin.layouts.app')
<style>
    .card {
        border-radius: 10px !important;
    }

    ul.list-style-none li {
        list-style: none;
    }

    ul.list-style-none li a {
        color: #f26bac;
        padding: 8px 0px;
        display: block;
        text-decoration: none;
    }

    .m-t-5 {
        margin-top: 5px;
    }

    .w-30px {
        width: 30px;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            <a href="{{ route('user.post.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i>
                                Trở lại</a>
                        </div>
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="card-title m-b-0">Thông tin người lấy bài: {{ $user->full_name }}</h4>
                            </div>
                            <div class="infomation mx-5">
                                <p class="text-dark"><span class="font-weight-bold">Tài khoản:</span> {{ $user->username }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày đăng ký:</Span> {{ $user->created_at }}</p>
                                <p class="text-dark"><Span class="font-weight-bold">Ngày cập nhật:</Span> {{ $user->updated_at }}
                                </p>
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

                    
                    <hr>
                    <div class="panel-heading">
                        <div class=" mt-3">   
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title m-b-0">Danh sách các bài đã lấy</h4>
                                </div>
                                <ul class="list-style-none pl-0 pl-md-5">

                                    @foreach ($user->get_posts as $get_post)
                                        <li class="d-flex no-block card-body">
                                            <i class="fa fa-check-circle w-30px m-t-5 text-success"></i>
                                            <div>
                                                <a href="{{ route('posts.show',$get_post->post->slug) }}" class="m-b-0 font-medium p-0" data-abc="true">{{ $get_post->post->title }}</a>
                                                <span class="text-muted">Tác giả: {{ $get_post->post->user->full_name }}</span>
                                            </div>
                                            <div class="ml-auto">
                                                <div class="tetx-right">
                                                    @php
                                                        $now = \Carbon\Carbon::now();
                                                        $created_at = $get_post->created_at;
                                                        $minutes = $created_at->diffInMinutes($now);
                                                        $hours = $created_at->diffInHours($now);
                                                        $days = $created_at->diffInDays($now);
                                                        $weeks = $created_at->diffInWeeks($now);
                                                        $months = $created_at->diffInMonths($now);
                                                        $years = $created_at->diffInYears($now);
                                                    @endphp
                                                    @if($years >= 1)
                                                        <h6 class="text-muted m-b-0">{{ $years }} năm trước</h6>
                                                    @elseif($months >= 1)
                                                        <h6 class="text-muted m-b-0">{{ $months }} tháng trước</h6>
                                                    @elseif($weeks >= 1)
                                                        <h6 class="text-muted m-b-0">{{ $weeks }} tuần trước</h6>
                                                    @elseif($days >= 1)
                                                        <h6 class="text-muted m-b-0">{{ $days }} ngày trước</h6>
                                                    @elseif($hours >= 1)
                                                        <h6 class="text-muted m-b-0">{{ $hours }} giờ trước</h6>
                                                    @else
                                                        <h6 class="text-muted m-b-0">{{ $minutes }} phút trước</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <div id="pagination">
                                    {{ $user->get_posts->links('vendor.pagination.custom') }}
                                </div>

                            </div>     
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
