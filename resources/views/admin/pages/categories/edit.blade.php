@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-offset-1 col-md-12">
            <div class="panel pb-2">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col col-sm-3 col-xs-12">
                            <h4 class="title">Sửa thể loại</h4>
                        </div>
                        <div class="btn-action col-sm-9 col-xs-12 text-right">
                            <button class="btn btn-default btn-save" title="Save"><i class="fa-regular fa-floppy-disk"></i></button>
                        </div>
                    </div>
                </div>
             
                <form id="formUpdateCategory" action="{{ route('categories.update',$category->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                    @csrf
                    @method('PUT')
                    <div class="form-group w-100 mb-2">
                        <div class="mx-5">
                            <input id="name" type="text" class="input-create form-control @error('name') is-invalid @enderror" name="name" value="{{old('name', $category->name)}}" autocomplete="name" placeholder="Tên thể loại">

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title dp-color" id="messageModalLabel">Thông báo</h5>
                  
                </div>
                <div class="modal-body" id="messageContent">
                    <!-- Message will be inserted here -->
                </div>
              </div>
            </div>
        </div>
    </div>
@endsection

@push('script-admin')
    <script>
        $(document).ready(function() {
            $('.btn-save').click(function(e) {
                e.preventDefault();
                $('#formUpdateCategory').submit();
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