@extends('layouts.base')

@section('content')
<div>
    <div id="reporter">
        <div class="reporter">
            <div class="d-flex menu mb-5">
                <div>
                    <button id="createPostBtn" class="btn-menu me-2">Tạo bài viết mới</button>
                </div>
                <div>
                    <button id="viewAllBtn" class="btn-menu">Xem tất cả bài</button>
                </div>
            </div> 
            <div id="formReporter" class="form-reporter">
                <div class="form-post">

                    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title dp-color" id="messageModalLabel">Thông báo</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="messageContent">
                                <!-- Message will be inserted here -->
                            </div>
                          </div>
                        </div>
                    </div>

                    <p class="px-3 text-reporter">Vui lòng tạo bài viết mới của bạn theo yêu cầu bên dưới:</p>
                    <form id="myForm" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{old('title') ?? $post_draft->title ?? '' }}" autocomplete="title" placeholder="Tựa đề bài viết">

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <select id="post_type" class="form-select @error('post_type') is-invalid @enderror" name="post_type" >
                                    <option value="" disabled selected>Loại tin</option>
                                    <option value="new" {{ (old('post_type') ?? $post_draft->post_type ?? '')  == 'new' ? 'selected' : '' }}>viết mới</option>
                                    <option value="translation" {{ (old('post_type') ?? $post_draft->post_type ?? '')  == 'translation' ? 'selected' : '' }}>Bài dịch</option>
                                </select>

                                @error('post_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <select id="category_id" class="form-select @error('category_id') is-invalid @enderror" name="category_id" >
                                    <option value="" disabled selected>Thể loại bài viết</option>
                                    @if ($categorys)
                                        @foreach ($categorys as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id') ?? $post_draft->category_id ?? '')  == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                <textarea rows="5" id="brief_intro" class="form-control @error('brief_intro') is-invalid @enderror" name="brief_intro" autocomplete="brief_intro" placeholder="Giới thiệu ngắn về bài viết">{{old('brief_intro') ?? $post_draft->brief_intro ?? ''}}</textarea>

                                @error('brief_intro')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <textarea rows="10" id="content" class="form-control @error('content') is-invalid @enderror" name="content" autocomplete="content" placeholder="Nội dung bài viết">{{old('content') ?? $post_draft->content ?? ''}}</textarea>

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
                        <span class="span-modal">Loại tin:</span>
                        <p class="post-type"></p>
                        <hr>
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
        

    </div>
</div>
@endsection
@push('scripts')
    <script>
    const dt = new DataTransfer();
    const toastBody = $('.toast-body');
    const imageContainer = $('#GFG .image-container');
    const filesList = $("#filesList > #files-names");
    const formReporter = $('#formReporter');
    const listReporter = $('#listReporter');
    
    function getInputValues() {
        return {
            title: $('#title').val(),
            post_type: $('#post_type').val(),
            category_id: $('#category_id').val(),
            brief_intro: $('#brief_intro').val(),
            content: $('#content').val(),
            attachments: $('#attachment').prop('files')
        };
    }

    function handleReviewClick() {
        var values = getInputValues();
        $('#GFG .span-modal-title').text(values.title);
        let postType = values.post_type;
        if (postType === 'new') {
            $('#GFG .post-type').text('Bài mới');
        } else if (postType === 'translation') {
            $('#GFG .post-type').text('Bài dịch');
        }
        $('#GFG .category-name').text(values.category_id);
        $('#GFG .brief-intro').text(values.brief_intro);
        $('#GFG .content').text(values.content);

        imageContainer.empty();
        for (var i = 0; i < values.attachments.length; i++) {
            var url = URL.createObjectURL(values.attachments[i]);
            var img = $('<img>').attr('src', url);
            imageContainer.append(img);
        }
    }

    function handleAttachmentChange(e) {
        for(var i = 0; i < this.files.length; i++){
            let fileBloc = createFileBlock(this.files.item(i));
            filesList.append(fileBloc);
            dt.items.add(this.files.item(i));
        };
        this.files = dt.files;
    }

    function createFileBlock(file) {
        let fileBloc = $('<span/>', {class: 'file-block'}),
            div = $('<div/>'),
            fileName = $('<span/>', {class: 'd-none name', text: file.name}),
            fileImage = $('<img/>', {src: URL.createObjectURL(file), class: 'image-post'});

        div.append('<span class="file-delete"><span>+</span></span>')
            .append(fileName)
            .append(fileImage);

        fileBloc.append(div);
        return fileBloc;
    }

    $(document).ready(function() {
        $('#btn-review').on('click', handleReviewClick);
        $("#attachment").on('change', handleAttachmentChange);

        $('#saveTemp').click(function() {
            $('#myForm').attr('action', '/drafts');
        });

        $('#saveNew').click(function() {
            $('#myForm').attr('action', '/posts');
        });

        let images = {!! json_encode($post_draft->postImages ?? []) !!};

        if (images.length > 0) {
           
            let fetchPromises = images.map(function(image) {
                return fetch('/file/' + image.image)
                    .then(response => response.blob())
                    .then(blob => {
                        let originalFileName = image.image.split("/").pop();
                        let file = new File([blob], originalFileName);
                        let fileBloc = createFileBlock(file);
                        filesList.append(fileBloc);
                        dt.items.add(file);
                    });
            });

            $.when.apply($, fetchPromises).then(function() {
                
            }); 
        }

        let input = $('input[name="file[]"]');
                input[0].files = dt.files;
                $(document).on('click', '.file-delete', function() {
                    let name = $(this).next('span.name').text();
                    $(this).closest('.file-block').remove();
                    $.each(dt.items, function(i, item){
                        if(name === item.getAsFile().name){
                            dt.items.remove(i);
                            return false;
                        }
                    });
                    $('#attachment')[0].files = dt.files;
                });
    });
    
    $(document).ready(function() {
            $('#createPostBtn').click(function() {
                window.location.href = "{{ route('home') }}";
            });

            $('#viewAllBtn').click(function() {
                window.location.href = "{{ route('posts.allPosts') }}";
            });
    });

    $(document).ready(function() {
        @if (session('success'))
            $('#messageContent').text('{{ session('success') }}');
            $('#messageModal').modal('show');
        @endif
        @if (session('error'))
            $('#messageContent').text('{{ session('error') }}');
            $('#messageModal').modal('show');
        @endif
    });
    </script>
@endpush
