@extends('admin.layouts.app')
@section('title', 'Edit Post')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 320px; font-size:.92rem; }
    .ql-toolbar.ql-snow { border-color:#dfe3ee; border-radius:9px 9px 0 0; background:#fbfcff; }
    .ql-container.ql-snow { border-color:#dfe3ee; border-radius:0 0 9px 9px; }
    .ql-snow .ql-stroke { stroke:#667085; }
    .ql-snow .ql-fill { fill:#667085; }
    .ql-snow .ql-picker { color:#667085; }
    .ql-snow.ql-toolbar button:hover .ql-stroke, .ql-snow.ql-toolbar button.ql-active .ql-stroke { stroke:#4e6ef2; }
    .ql-snow.ql-toolbar button:hover .ql-fill, .ql-snow.ql-toolbar button.ql-active .ql-fill { fill:#4e6ef2; }
    @media(max-width:575px){.ql-toolbar.ql-snow{overflow-x:auto;flex-wrap:nowrap;}.ql-formats{white-space:nowrap;}}
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h4>Edit Post</h4>
        <div class="sub">{{ $post->title }}</div>
    </div>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" id="post-form">
    @csrf @method('PUT')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="form-card mb-3">
                <div class="mb-3">
                    <label class="form-label">Post Title <span class="req">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title', $post->title) }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>
            </div>

            <div class="form-card mb-3">
                <label class="form-label">Content <span class="req">*</span></label>
                <div id="quill-editor">{!! old('content', $post->content) !!}</div>
                <input type="hidden" name="content" id="content-input">
            </div>

            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-search me-2"></i>SEO</h6>
                <div class="mb-3">
                    <label class="form-label small">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control form-control-sm"
                           value="{{ old('meta_title', $post->meta_title) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm">{{ old('meta_description', $post->meta_description) }}</textarea>
                </div>
                <div>
                    <label class="form-label small">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control form-control-sm"
                           value="{{ old('meta_keywords', $post->meta_keywords) }}">
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-send me-2"></i>Publish</h6>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @foreach(['draft'=>'Draft','published'=>'Published','scheduled'=>'Scheduled','archived'=>'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status',$post->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" value="1"
                           {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="allow_comments">Allow Comments</label>
                </div>
                <div class="mt-3 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Update Post
                    </button>
                </div>
            </div>

            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-folder2-open me-2"></i>Category</h6>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">— Uncategorised —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$post->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-tags me-2"></i>Tags</h6>
                <div class="d-flex flex-wrap gap-2">
                    @php $selectedTags = old('tags', $post->tags->pluck('id')->toArray()) @endphp
                    @foreach($tags as $tag)
                    <div class="tag-badge">
                        <input type="checkbox" name="tags[]" id="tag-{{ $tag->id }}" value="{{ $tag->id }}"
                               {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                        <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-image me-2"></i>Featured Image</h6>
                @if($post->featured_image)
                    <img src="{{ $post->featured_image_url }}" class="img-fluid rounded mb-2" style="max-height:130px">
                @endif
                <input type="file" name="featured_image" class="form-control form-control-sm" accept="image/*">
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic','underline'],['blockquote'],
            [{list:'ordered'},{list:'bullet'}],['link','image'],['clean']] }
    });
    document.getElementById('post-form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });
</script>
@endpush
