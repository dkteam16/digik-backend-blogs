@extends('admin.layouts.app')
@section('title', 'Edit Hiring Post')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 260px; }
    .form-card { background: #fff; border-radius: 12px; padding: 1.5rem; border: none; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
    @media(max-width:575px){.ql-toolbar.ql-snow{overflow-x:auto;flex-wrap:nowrap;}.ql-formats{white-space:nowrap;}}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Edit Hiring Post</h4>
        <small class="text-muted">{{ $post->title }}</small>
    </div>
    <a href="{{ route('admin.hiring-posts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.hiring-posts.update', $post) }}" method="POST" id="hiring-post-form">
    @csrf @method('PUT')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="form-card mb-3">
                <label class="form-label fw-semibold">Job Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                       value="{{ old('title', $post->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-card mb-3">
                <label class="form-label fw-semibold">Job Description <span class="text-danger">*</span></label>
                <div id="quill-editor">{!! old('description', $post->description) !!}</div>
                <input type="hidden" name="description" id="description-input">
            </div>

            <div class="form-card mb-3">
                <label class="form-label fw-semibold">Qualification</label>
                <textarea name="qualification" rows="4" class="form-control">{{ old('qualification', $post->qualification) }}</textarea>
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
                        @foreach(['draft'=>'Draft','published'=>'Published','closed'=>'Closed','archived'=>'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status',$post->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured</label>
                </div>
                <div class="mt-3 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Update Hiring Post
                    </button>
                </div>
            </div>

            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-briefcase me-2"></i>Job Details</h6>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Department</label>
                    <input type="text" name="department" class="form-control form-control-sm"
                           value="{{ old('department', $post->department) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Location</label>
                    <input type="text" name="location" class="form-control form-control-sm"
                           value="{{ old('location', $post->location) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Work Type</label>
                    <select name="work_type" class="form-select form-select-sm">
                        <option value="">— Select —</option>
                        @foreach(['onsite'=>'Onsite','remote'=>'Remote','hybrid'=>'Hybrid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('work_type',$post->work_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Employment Type <span class="text-danger">*</span></label>
                    <select name="employment_type" class="form-select form-select-sm">
                        @foreach(['full-time'=>'Full-time','part-time'=>'Part-time','contract'=>'Contract','internship'=>'Internship'] as $val => $label)
                            <option value="{{ $val }}" {{ old('employment_type',$post->employment_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Experience</label>
                    <input type="text" name="experience" class="form-control form-control-sm"
                           value="{{ old('experience', $post->experience) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Application Deadline</label>
                    <input type="date" name="application_deadline" class="form-control form-control-sm"
                           value="{{ old('application_deadline', $post->application_deadline?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label small fw-medium">Apply URL</label>
                    <input type="url" name="apply_url" class="form-control form-control-sm"
                           value="{{ old('apply_url', $post->apply_url) }}">
                </div>
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
            [{list:'ordered'},{list:'bullet'}],['link'],['clean']] }
    });
    document.getElementById('hiring-post-form').addEventListener('submit', function () {
        document.getElementById('description-input').value = quill.root.innerHTML;
    });
</script>
@endpush
