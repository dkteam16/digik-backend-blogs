@extends('admin.layouts.app')
@section('title','Tags')
@section('page-title','Tags')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Tags</h4>
        <small class="text-muted">Manage post tags</small>
    </div>
</div>

<div class="row g-3">
    {{-- Add Tag --}}
    <div class="col-md-4">
        <div class="form-panel">
            <h6 class="fw-semibold mb-3">Add New Tag</h6>
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Tag Name</label>
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

    {{-- Tags List --}}
    <div class="col-md-8">
        <div class="tbl-wrap">
            <div class="p-3 border-bottom">
                <h6 class="mb-0 fw-semibold">All Tags ({{ $tags->total() }})</h6>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th width="130">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $tag)
                    <tr>
                        <td class="fw-medium">{{ $tag->name }}</td>
                        <td><code class="text-muted small">{{ $tag->slug }}</code></td>
                        <td><span class="sbadge bg-secondary bg-opacity-15 text-secondary">{{ $tag->posts_count }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick="editTag({{ $tag->id }}, '{{ $tag->name }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                      onsubmit="return confirm('Delete tag?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No tags yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $tags->links() }}</div>
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
</script>
@endpush
