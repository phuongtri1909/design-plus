@extends('layouts.base')

@section('content')
<div>
    <div id="reporter">
        <div class="reporter">
            <div class="d-flex menu mb-5">
                <div>
                    <button class="btn-menu me-2">Tạo bài viết mới</button>
                </div>
                <div>
                    <button class="btn-menu">Xem tất cả bài</button>
                </div>
            </div> 
            <div class="form-reporter">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <p class="px-3 text-reporter">Vui lòng tạo bài viết mới của bạn theo yêu cầu bên dưới:</p>
                <form id="myForm" action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{old('title') ?? $post_draft->title }}" required autocomplete="title" placeholder="Tựa đề bài viết">

                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <select id="category_id" class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                <option value="" disabled selected>Thể loại bài viết</option>
                                @if ($categorys)
                                    @foreach ($categorys as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id') ?? $post_draft->category_id)  == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>

                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <input id="brief_intro" type="text" class="form-control @error('brief_intro') is-invalid @enderror" name="brief_intro" value="{{old('brief_intro') ?? $post_draft->brief_intro }}" required autocomplete="title" placeholder="Giới thiệu ngắn về bài viết">

                            @error('brief_intro')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <textarea rows="10" id="content" class="form-control @error('content') is-invalid @enderror" name="content" required autocomplete="content" placeholder="Nội dung bài viết">{{old('content') ?? $post_draft->content}}</textarea>

                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <div class="col-12">
                            <p class="text-center">
                                <label class="label-uploade" for="attachment">
                                    <a class="btn-upload form-control" role="button" aria-disabled="false">Upload ảnh cho bài viết  <i class="fa-solid fa-plus"></i></a>
                                </label>
                                <input class="@error('file') is-invalid @enderror" type="file" name="file[]" accept=".jpg,.png,.jpeg" id="attachment" hidden multiple/>
                                @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </p>
                            <p id="files-area">
                                <span id="filesList">
                                    <span id="files-names"></span>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="btn-action mb-2 mb-5 text-center">
                        <div class="row">
                            <div class="col-12 col-xl-4 mb-2">
                                <button id="btn-review" type="button" data-bs-toggle="modal" data-bs-target="#GFG" class="btn-border-gradient border-gradient-blue">
                                    Xem lại bài
                                </button>
                            </div>
                            <div class="col-12 col-xl-4 mb-2">
                                <button id="saveTemp" class="btn-border-gradient border-gradient-blue">
                                    Lưu tạm bài viết
                                </button>
                            </div>
                            <div class="col-12 col-xl-4 mb-2">
                                <button id="saveNew" class="btn-border-gradient border-gradient-blue">
                                    Lưu & soạn bài mới
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
  
        <div class="modal fade" id="GFG"> 
            <div class="modal-dialog  modal-lg   
                        modal-dialog-scrollable "> 
                <div class="modal-content"> 
                    <div class="modal-header"> 
                        <h5 class="modal-title" id="GFGLabel">  
                            Bài viết: <span class="span-modal-title"> tựa đề nè</span> 
                        </h5> 
                        <button type="button" class="btn-close" 
                                data-bs-dismiss="modal" aria-label="Close"> 
                        </button> 
                    </div> 
                    <div class="modal-body"> 
                         <span class="span-modal">Thể loại bài viết:</span>
                         <p class="category-name"></p>
                        <hr>
                        <span class="span-modal ">Giới thiệu ngắn:</span>
                        <p class="brief-intro"></p>
                        <hr>
                        <span class="span-modal ">Nội dung bài viết</span>
                        <p class="content"></p>
                        <hr>
                        <span class="span-modal ">Ảnh đính kèm:</span>
                        <div class="image-container">

                        </div>

                    </div> 
                </div> 
            </div> 
        </div>  

        <div class="toast align-items-center position-fixed end-0" style="top: 5%" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body toast-success">
                    Hello, world! This is a toast message.
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        
    </div>
</div>
@endsection
@push('scripts')
    <script>
    function getInputValues() {
        return {
            title: $('#title').val(),
            category_id: $('#category_id').val(),
            brief_intro: $('#brief_intro').val(),
            content: $('#content').val(),
            attachments: $('#attachment').prop('files')
        };
    }

    function showToast(message, type) {
        var toastBody = $('.toast-body');
        toastBody.removeClass('toast-success toast-error toast-warning');
        switch(type) {
            case 'success':
                toastBody.addClass('toast-success');
                break;
            case 'error':
                toastBody.addClass('toast-error');
                break;
            case 'warning':
                toastBody.addClass('toast-warning');
                break;
        }
        toastBody.text(message);
        $('.toast').toast('show');
    }

    $('#btn-review').on('click', function() {
        var values = getInputValues();
        $('#GFG .span-modal-title').text(values.title);
        $('#GFG .category-name').text(values.category_id);
        $('#GFG .brief-intro').text(values.brief_intro);
        $('#GFG .content').text(values.content);

        var files = values.attachments;
        var imageContainer = $('#GFG .image-container');
        imageContainer.empty();

        for (var i = 0; i < files.length; i++) {
            var url = URL.createObjectURL(files[i]);
            var img = $('<img>').attr('src', url);
            imageContainer.append(img);
        }
    });

    $(document).ready(function() {
        $('#saveTemp').click(function(e) {
            $('#myForm').attr('action', '/drafts');
        });

        $('#saveNew').click(function(e) {
            $('#myForm').attr('action', '/routeForSaveNew');
        });
    });

    // $('#saveTemp').on('click', function(e) {
    //     e.preventDefault();

    //     var formData = new FormData();
    //     formData.append('title', $('#title').val());
    //     formData.append('category_id', $('#category_id').val());
    //     formData.append('brief_intro', $('#brief_intro').val());
    //     formData.append('content', $('#content').val());
    //     formData.append('_token', '{{ csrf_token() }}');

    //     var files = $('#attachment')[0].files;
    //     for (var i = 0; i < files.length; i++) {
    //         formData.append('file[]', files[i]);
    //     }

    //     $.ajax({
    //         url: '/drafts',
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(response) {
    //             showToast('Bài viết đã được lưu tạm', 'success');
    //             console.log(response);
    //         },
    //         error: function(error) {
    //             var errors = error.responseJSON;

    //             $.each(errors, function(key, value) {
    //                 console.log(value);
    //                 $('#content').after('<span class="invalid-feedback" role="alert"><strong>' + value[0] + '</strong></span>');
    //             });
    //             showToast('Đã có lỗi xảy ra, vui lòng thử lại sau', 'error');
    //         }
    //     });
    // });
    </script>
@endpush
