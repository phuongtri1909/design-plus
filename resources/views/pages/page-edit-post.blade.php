@extends('layouts.base')

@section('content')
    <div id="detail-post">
        <div class="container">
            <div id="form-edit-post" class="form-edit-post">
                <div class="form-post">
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
                   
                    <p class="px-3 pt-5 text-reporter">Chỉnh sửa bài viết này:</p>
                    <form id="form-edit" action="{{ route('posts.update',['slug'=> $post->slug]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{old('title') ?? $post->title ?? '' }}" autocomplete="title" placeholder="Tựa đề bài viết">

                                @error('title')
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
                                            <option value="{{ $category->id }}" {{ (old('category_id') ?? $post->category_id ?? '')  == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                <textarea rows="5" id="brief_intro" class="form-control @error('brief_intro') is-invalid @enderror" name="brief_intro" autocomplete="brief_intro" placeholder="Giới thiệu ngắn về bài viết">{{old('brief_intro') ?? $post->brief_intro ?? ''}}</textarea>

                                @error('brief_intro')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col-12">
                                <textarea rows="10" id="content" class="form-control @error('content') is-invalid @enderror" name="content" autocomplete="content" placeholder="Nội dung bài viết">{{old('content') ?? $post->content ?? ''}}</textarea>

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
                            <button id="save" type="submit" class="btn-border-gradient border-gradient-blue">
                                Lưu lại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const dt = new DataTransfer();
        const filesList = $("#filesList > #files-names");

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
            $("#attachment").on('change', handleAttachmentChange);


            let images = {!! json_encode($post->postImages ?? []) !!};

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
            }
        });
    </script>
@endpush