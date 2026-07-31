@extends('admin.layouts.app')
@section('title','Tags')
@section('page-title','Tags')

@section('content')
<div class="page-head">
    <div>
        <h4>Tags</h4>
        <div class="sub">Manage post tags</div>
    </div>
</div>

<div class="row g-3">
    {{-- Add Tag --}}
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-head">
                <h6><i class="bi bi-plus-circle me-2 text-muted"></i>Add New Tag</h6>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tag Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Laravel" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Tag
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tags List --}}
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-head">
                <h6><i class="bi bi-tags me-2 text-muted"></i>All Tags</h6>
            </div>
            <div class="table-responsive">
                <table id="tags-table" class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Posts</th>
                            <th width="130">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-semibold">Edit Tag</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editTag(id, name) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-form').action = `/admin/tags/${id}`;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

$(function () {
    initAdminDataTable('#tags-table', {
        ajax: "{{ route('admin.tags.data') }}",
        columns: [
            { data: 'name',        name: 'name' },
            { data: 'slug',        name: 'slug', orderable: false,
              render: data => `<code class="text-muted small">${data}</code>` },
            { data: 'posts_count', name: 'posts_count', orderable: false,
              render: data => `<span class="pill tint-secondary">${data}</span>` },
            { data: 'actions',     name: 'actions', orderable: false, searchable: false },
        ],
        order: [[0, 'asc']],
    });
});
</script>
@endpush
