@extends('admin.layouts.app')
@section('title', 'Create Post')

@push('styles')
<!-- Quill Editor -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 320px; }
    .form-card { background: #fff; border-radius: 12px; padding: 1.5rem; border: none; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
    .tag-badge { cursor: pointer; user-select: none; }
    .tag-badge input[type=checkbox] { display: none; }
    .tag-badge label { cursor: pointer; padding: .25rem .65rem; border-radius: 20px; border: 1.5px solid #dee2e6; font-size: .8rem; transition: all .2s; display: inline-block; }
    .tag-badge input:checked + label { background: #4e6ef2; border-color: #4e6ef2; color: #fff; }
    @media(max-width:575px){.ql-toolbar.ql-snow{overflow-x:auto;flex-wrap:nowrap;}.ql-formats{white-space:nowrap;}}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Create New Post</h4>
        <small class="text-muted">Fill in the details below</small>
    </div>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
    @csrf

    <div class="row g-3">
        <!-- Left Column (Main) -->
        <div class="col-lg-8">

            <div class="form-card mb-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Post Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Enter post title..." required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Excerpt</label>
                    <textarea name="excerpt" rows="2" class="form-control @error('excerpt') is-invalid @enderror"
                              placeholder="Short description (optional)...">{{ old('excerpt') }}</textarea>
                    <small class="text-muted">Leave blank to auto-generate from content.</small>
                </div>
            </div>

            <!-- Content Editor -->
            <div class="form-card mb-3">
                <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                <div id="quill-editor">{!! old('content') !!}</div>
                <input type="hidden" name="content" id="content-input">
                @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- SEO -->
            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-search me-2"></i>SEO Settings</h6>
                <div class="mb-3">
                    <label class="form-label small">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control form-control-sm"
                           value="{{ old('meta_title') }}" placeholder="SEO title (defaults to post title)">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="form-control form-control-sm"
                              placeholder="SEO description (max 160 chars)">{{ old('meta_description') }}</textarea>
                </div>
                <div>
                    <label class="form-label small">Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control form-control-sm"
                           value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, ...">
                </div>
            </div>
        </div>

        <!-- Right Column (Settings) -->
        <div class="col-lg-4">

            <!-- Publish -->
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-send me-2"></i>Publish</h6>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        @foreach(['draft'=>'Draft','published'=>'Published','scheduled'=>'Scheduled','archived'=>'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status','draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="{{ old('published_at') }}">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" value="1"
                           {{ old('allow_comments', true) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="allow_comments">Allow Comments</label>
                </div>
                <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Save Post
                    </button>
                    <button type="submit" class="btn btn-outline-secondary btn-sm"
                            onclick="document.querySelector('[name=status]').value='draft'">
                        Save as Draft
                    </button>
                </div>
            </div>

            <!-- Category -->
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-folder2-open me-2"></i>Category</h6>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">— Uncategorised —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tags -->
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-tags me-2"></i>Tags</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <div class="tag-badge">
                        <input type="checkbox" name="tags[]" id="tag-{{ $tag->id }}" value="{{ $tag->id }}"
                               {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                        <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                    </div>
                    @endforeach
                    @if($tags->isEmpty())
                        <small class="text-muted">No tags yet.</small>
                    @endif
                </div>
            </div>

            <!-- Featured Image -->
            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-image me-2"></i>Featured Image</h6>
                <input type="file" name="featured_image" id="featured_image" class="form-control form-control-sm"
                       accept="image/*">
                <div class="mt-2">
                    <img id="img-preview" src="#" alt="Preview" class="img-fluid rounded d-none" style="max-height:160px">
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    // Quill init
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1,2,3,false] }],
                ['bold','italic','underline','strike'],
                ['blockquote','code-block'],
                [{ list:'ordered' },{ list:'bullet' }],
                ['link','image'],
                ['clean']
            ]
        }
    });

    // Sync content before submit
    document.getElementById('post-form').addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });

    // Image preview
    document.getElementById('featured_image').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('img-preview');
                img.src = e.target.result;
                img.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
