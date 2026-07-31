@extends('admin.layouts.app')
@section('title', 'Create Hiring Post')

@push('styles')
<!-- Quill Editor -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 260px; font-size:.92rem; }
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
        <h4>Create Hiring Post</h4>
        <div class="sub">Fill in the job details below</div>
    </div>
    <a href="{{ route('admin.hiring-posts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('admin.hiring-posts.store') }}" method="POST" id="hiring-post-form">
    @csrf

    <div class="row g-3">
        <!-- Left Column (Main) -->
        <div class="col-lg-8">

            <div class="form-card mb-3">
                <div class="mb-3">
                    <label class="form-label">Job Title <span class="req">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="e.g. Senior Backend Engineer" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Description Editor -->
            <div class="form-card mb-3">
                <label class="form-label">Job Description <span class="req">*</span></label>
                <div id="quill-editor">{!! old('description') !!}</div>
                <input type="hidden" name="description" id="description-input">
                @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Qualification -->
            <div class="form-card mb-3">
                <label class="form-label">Qualification</label>
                <textarea name="qualification" rows="4" class="form-control @error('qualification') is-invalid @enderror"
                          placeholder="Required qualifications, education, skills...">{{ old('qualification') }}</textarea>
                @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- SEO -->
            <div class="form-card">
                <h6 class="fw-semibold mb-3"><i class="bi bi-search me-2"></i>SEO Settings</h6>
                <div class="mb-3">
                    <label class="form-label small">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control form-control-sm"
                           value="{{ old('meta_title') }}" placeholder="SEO title (defaults to job title)">
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
                        @foreach(['draft'=>'Draft','published'=>'Published','closed'=>'Closed','archived'=>'Archived'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status','draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control form-control-sm"
                           value="{{ old('published_at') }}">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured</label>
                </div>
                <div class="mt-3 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-1"></i> Save Hiring Post
                    </button>
                    <button type="submit" class="btn btn-outline-secondary btn-sm"
                            onclick="document.querySelector('[name=status]').value='draft'">
                        Save as Draft
                    </button>
                </div>
            </div>

            <!-- Job Details -->
            <div class="form-card mb-3">
                <h6 class="fw-semibold mb-3"><i class="bi bi-briefcase me-2"></i>Job Details</h6>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Department</label>
                    <input type="text" name="department" class="form-control form-control-sm"
                           value="{{ old('department') }}" placeholder="e.g. Engineering">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Location</label>
                    <input type="text" name="location" class="form-control form-control-sm"
                           value="{{ old('location') }}" placeholder="e.g. Bengaluru, India">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Work Type</label>
                    <select name="work_type" class="form-select form-select-sm">
                        <option value="">— Select —</option>
                        @foreach(['onsite'=>'Onsite','remote'=>'Remote','hybrid'=>'Hybrid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('work_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Employment Type <span class="req">*</span></label>
                    <select name="employment_type" class="form-select form-select-sm">
                        @foreach(['full-time'=>'Full-time','part-time'=>'Part-time','contract'=>'Contract','internship'=>'Internship'] as $val => $label)
                            <option value="{{ $val }}" {{ old('employment_type','full-time') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Experience</label>
                    <input type="text" name="experience" class="form-control form-control-sm"
                           value="{{ old('experience') }}" placeholder="e.g. 2-4 years">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Application Deadline</label>
                    <input type="date" name="application_deadline" class="form-control form-control-sm"
                           value="{{ old('application_deadline') }}">
                </div>
                <div>
                    <label class="form-label small fw-medium">Apply URL</label>
                    <input type="url" name="apply_url" class="form-control form-control-sm"
                           value="{{ old('apply_url') }}" placeholder="https://...">
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
        modules: {
            toolbar: [
                [{ header: [1,2,3,false] }],
                ['bold','italic','underline','strike'],
                ['blockquote','code-block'],
                [{ list:'ordered' },{ list:'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    document.getElementById('hiring-post-form').addEventListener('submit', function () {
        document.getElementById('description-input').value = quill.root.innerHTML;
    });
</script>
@endpush
