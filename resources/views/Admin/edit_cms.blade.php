@extends('Admin.layouts.admin_layout')
@section('title', 'CMS Management')
@push('styles')
<style>
       .modal-content {
        position: relative !important;
    }

    .ck.ck-balloon-panel {
        z-index: 1056 !important;
        position: absolute !important;
    }

    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .ck.ck-editor__main {
        scroll-margin-top: 0 !important;
    }
</style>
@endpush
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $page->id ? 'Edit CMS Page: ' . $page->title : 'Add New CMS Page' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $page->id ? route('cms.update', $page->id) : route('cms.store') }}" method="POST">
                @csrf
                @if($page->id)
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="title">Page Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                </div>
                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="editor" name="content" class="form-control" rows="10">{{ old('content', $page->content) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">{{ $page->id ? 'Save Changes' : 'Create Page' }}</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<!-- <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script> -->
<!-- <script>
    CKEDITOR.replace('editor');
</script> -->
<script>
document.addEventListener('focusin', function (e) {
    if (e.target.closest('.ck') !== null) {
        e.stopImmediatePropagation();
    }
}, true);
let add_editorInstance;

ClassicEditor.create(document.querySelector("#editor"), {
    ckfinder: {
        uploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}"
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            '|',
            'imageStyle:alignLeft',
            'imageStyle:full',
            'imageStyle:alignRight',
            '|',
            'resizeImage:50',
            'resizeImage:75',
            'resizeImage:original'
        ],
        resizeUnit: 'px',
        resizeOptions: [
            {
                name: 'resizeImage:original',
                label: 'Original',
                value: null
            },
            {
                name: 'resizeImage:50',
                label: '50%',
                value: '50'
            },
            {
                name: 'resizeImage:75',
                label: '75%',
                value: '75'
            }
        ],
        styles: ['full', 'alignLeft', 'alignRight']
    },
})
.then(editor => {
    add_editorInstance = editor;
    console.log("CKEditor initialized with image resizing");
})
.catch(error => {
    console.error("CKEditor Error:", error);
});
</script>
@endsection
