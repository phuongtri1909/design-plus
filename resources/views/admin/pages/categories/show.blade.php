@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-md-12">
                <div class="panel pb-2">
                    <div class="panel-heading">
                        <div>
                            <a href="{{ route('categories.index') }}" class="dp-color"><i class="fa-solid fa-arrow-left "></i> Trở lại</a>
                        </div>
                        <div class="row">
                            <div class="col-12 text-left">
                                <h4 class="title">Thông tin: {{ $category->name }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="infomation mx-5">
                        <p class="text-dark my-0"><span class="font-weight-bold">Slug:</span> {{ $category->slug }}</p>
                        <p class="text-dark my-0"><Span class="font-weight-bold">Ngày tạo:</Span> {{ $category->created_at }}</p>
                        <p class="text-dark my-0"><Span class="font-weight-bold">Ngày cập nhật:</Span> {{ $category->updated_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection